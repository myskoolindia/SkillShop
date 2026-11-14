<?php
namespace App\Services;

use App\Models\{RoboquestConnection, SchoolMember, User};
use Illuminate\Support\Facades\{Cache, DB, Http, Log};
use RuntimeException;

/**
 * Mapper API: which provider courses each Club Shop lab product includes.
 *
 * GET {COLLAB_PRODUCT_COURSE_MAP_URL} returns
 *   {status:"success", data:[{course_id, course_title, course_type, products:[{product_id, product_title, ...}]}],
 *    pagination:{last_page}}
 *
 * A school that has paid for a mapped lab product in Club Shop receives the mapped courses in RoboQuest.
 * The mapper only describes product contents; access always comes from a paid Club Shop order.
 */
class ProductCourseMapService
{
    private const CACHE_KEY = 'collab.product_course_map';
    private const FRESH_SECONDS = 900;

    public function enabled(): bool
    {
        return (bool) config('services.collab.lab_course_access', true);
    }

    /**
     * product_id => [course_id => ['courseId', 'title', 'courseType', 'courseTypeName']]
     * Uses a 15-minute cache. If the provider is unreachable, the last good list is used.
     * Throws when no list has ever been loaded, so callers fail closed instead of revoking access.
     */
    public function productCourses(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && time() - ($cached['fetchedAt'] ?? 0) < self::FRESH_SECONDS) return $cached['products'];
        try {
            $products = $this->fetch();
            Cache::forever(self::CACHE_KEY, ['fetchedAt' => time(), 'products' => $products]);
            return $products;
        } catch (\Throwable $error) {
            Log::warning('Product-course mapper unavailable: ' . $error->getMessage());
            if (is_array($cached)) return $cached['products'];
            throw new RuntimeException('The product-course mapper is unavailable.', 0, $error);
        }
    }

    private function fetch(): array
    {
        $url = (string) config('services.collab.product_course_map_url');
        $parts = parse_url($url);
        $local = in_array($parts['host'] ?? '', ['localhost', '127.0.0.1', '[::1]'], true);
        if (!isset($parts['scheme'], $parts['host']) || isset($parts['user']) || isset($parts['pass'])
            || !($parts['scheme'] === 'https' || ($parts['scheme'] === 'http' && $local))) {
            throw new RuntimeException('The mapper URL must use HTTPS without credentials.');
        }
        $products = [];
        $lastPage = 1;
        for ($page = 1; $page <= $lastPage; $page++) {
            $response = Http::acceptJson()->timeout(15)->withOptions(['allow_redirects' => false])
                ->get($url, ['page' => $page, 'per_page' => 100]);
            if (!$response->successful()) throw new RuntimeException('Mapper returned HTTP ' . $response->status());
            $payload = $response->json();
            if (($payload['status'] ?? null) !== 'success' || !is_array($payload['data'] ?? null)) throw new RuntimeException('Invalid mapper response.');
            $lastPage = (int) ($payload['pagination']['last_page'] ?? 1);
            if ($lastPage < 1 || $lastPage > 100) throw new RuntimeException('Invalid mapper pagination.');
            foreach ($payload['data'] as $row) {
                $courseId = filter_var($row['course_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if (!$courseId || !is_array($row['products'] ?? null)) throw new RuntimeException('Invalid mapped course.');
                $course = [
                    'courseId' => $courseId,
                    'title' => mb_substr(trim(strip_tags((string) ($row['course_title'] ?? ''))), 0, 300) ?: 'Course ' . $courseId,
                    'courseType' => (int) ($row['course_type'] ?? 0) ?: null,
                    'courseTypeName' => mb_substr(trim(strip_tags((string) ($row['course_type_name'] ?? ''))), 0, 100),
                ];
                foreach ($row['products'] as $item) {
                    $productId = filter_var($item['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                    if (!$productId) throw new RuntimeException('Invalid mapped product.');
                    $products[$productId][$courseId] = $course + ['productTitle' => mb_substr(trim(strip_tags((string) ($item['product_title'] ?? ''))), 0, 500)];
                }
            }
        }
        return $products;
    }

    /**
     * Portal users whose lab purchases count for a school: the school account and its active teachers.
     * Used for the master's content check; each launch snapshot uses only its own buyer (see coursesForBuyers).
     */
    public function schoolBuyerIds(RoboquestConnection $connection): array
    {
        $teacherIds = SchoolMember::where('school_id', $connection->school_user_id)->where('status', 'active')->pluck('user_id');
        $teachers = User::whereIn('id', $teacherIds)->where('role', 'teacher')->where('status', 'active')->pluck('id');
        return $teachers->prepend($connection->school_user_id)->map(fn($id) => (int) $id)->unique()->values()->all();
    }

    /**
     * Club Shop lab product IDs paid for by these portal users.
     * Matching is by verified email: SSO signs a portal user into the Club Shop account with the same email.
     */
    public function paidLabProductIds(array $portalUserIds): array
    {
        $emails = User::whereIn('id', $portalUserIds)->where('status', 'active')
            ->pluck('email')->filter()->map(fn($email) => mb_strtolower(trim($email)))->unique()->values();
        if ($emails->isEmpty()) return [];
        $shop = DB::connection('clubshop');
        $buyers = $shop->table('users')->whereIn(DB::raw('LOWER(email)'), $emails)->where('email_status', 1)->where('banned', 0)->pluck('id');
        if ($buyers->isEmpty()) return [];
        return $shop->table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.buyer_id', $buyers)->where('orders.payment_status', 'payment_received')
            ->where(fn($query) => $query->whereNull('order_items.order_status')->orWhereNotIn('order_items.order_status', ['cancelled', 'refund_approved']))
            ->whereNotNull('order_items.product_id')->distinct()->pluck('order_items.product_id')
            ->map(fn($id) => (int) $id)->sort()->values()->all();
    }

    /**
     * Courses included with the lab products these portal users paid for, keyed by provider course ID.
     * Each entry lists the lab products that include it.
     */
    public function coursesForBuyers(array $portalUserIds): array
    {
        if (!$this->enabled()) return [];
        $productIds = $this->paidLabProductIds($portalUserIds);
        if (!$productIds) return [];
        $map = $this->productCourses();
        $courses = [];
        foreach ($productIds as $productId) {
            foreach ($map[$productId] ?? [] as $courseId => $course) {
                $courses[$courseId] ??= ['courseId' => $course['courseId'], 'title' => $course['title'], 'courseType' => $course['courseType'], 'courseTypeName' => $course['courseTypeName'], 'labProducts' => []];
                $courses[$courseId]['labProducts'][] = ['productId' => $productId, 'title' => $course['productTitle']];
            }
        }
        ksort($courses);
        return $courses;
    }
}
