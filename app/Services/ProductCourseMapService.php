<?php
namespace App\Services;

use App\Models\{RoboquestConnection, SchoolMember, User};
use Illuminate\Support\Facades\{Cache, DB, Http, Log};
use RuntimeException;

/**
 * Mapper API: what each Club Shop product includes.
 *
 * Current feed, GET {COLLAB_PRODUCT_COURSE_MAP_URL} (https://collab.myskool.club/api/course-product-map):
 *   {status:"success", data:[{product_id, product_sku, product_title, category_name,
 *     courses:[{course_id: "course:27" | "kit:G7-K03" | "lab:ar-vr:basic", course_name, course_category_name}]}],
 *    pagination:{last_page}}
 *   course:N  a provider course (RoboQuest course with that API ID)
 *   kit:ID    a RoboQuest robot kit and its missions
 *   lab:T:L   a RoboQuest Skill Lab topic T at level L (basic, advanced, premium or all)
 *   lms:ID              one LMS of the Composite Skill Lab / Skillvation Lab (RoboQuest expands nothing here)
 *   lms-category:ID     a whole category of those labs: RoboQuest unlocks all its LMS
 * The older course-first feed ({course_id, products:[...]}) is still read, so either URL works.
 *
 * Products are matched to paid Club Shop order lines by product ID, or by SKU. A lab bundle (Composite Skill Lab 68/72,
 * Skillvation Lab 74/114 …) is one order line for the bundle product with its chosen components in bundle_items; it
 * matches the mapper's "bundle_cat_<bundle ID>_<category ID>" for every Club Shop category it has components of
 * (e.g. bundle_cat_72_1: Agriculture and Gardening of bundle 72), and the bundle's own product ID.
 * The mapper only describes product contents; access always comes from a paid Club Shop order.
 * Changes in the mapper reach RoboQuest within 15 minutes (cache), at the person's next RoboQuest launch.
 */
class ProductCourseMapService
{
    private const CACHE_KEY = 'collab.product_course_map.v2';
    private const FRESH_SECONDS = 900;

    public function enabled(): bool
    {
        return (bool) config('services.collab.lab_course_access', true);
    }

    /**
     * ['products' => key => ['title', 'courses' => [courseId => course], 'kits' => [...], 'labs' => [...]], 'skus' => sku => key]
     * Uses a 15-minute cache. If the provider is unreachable, the last good list is used.
     * Throws when no list has ever been loaded, so callers fail closed instead of revoking access.
     */
    public function catalog(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && time() - ($cached['fetchedAt'] ?? 0) < self::FRESH_SECONDS) return $cached['catalog'];
        try {
            $catalog = $this->fetch();
            Cache::forever(self::CACHE_KEY, ['fetchedAt' => time(), 'catalog' => $catalog]);
            return $catalog;
        } catch (\Throwable $error) {
            Log::warning('Product-course mapper unavailable: ' . $error->getMessage());
            if (is_array($cached)) return $cached['catalog'];
            throw new RuntimeException('The product-course mapper is unavailable.', 0, $error);
        }
    }

    /** product key => [course_id => course] (compatibility view of catalog()). */
    public function productCourses(): array
    {
        return array_map(fn ($p) => $p['courses'], $this->catalog()['products']);
    }

    private function text($value, int $max): string
    {
        return mb_substr(trim(strip_tags((string) $value)), 0, $max);
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
        $skus = [];
        $add = function (string $key, string $title, string $sku) use (&$products, &$skus) {
            $products[$key] ??= ['title' => $title, 'courses' => [], 'kits' => [], 'labs' => [], 'lms' => [], 'lmsCategories' => []];
            if ($sku !== '') $skus[mb_strtolower($sku)] = $key;
        };
        $course = function (string $key, int $courseId, string $title, string $typeName, string $productTitle) use (&$products) {
            $products[$key]['courses'][$courseId] = ['courseId' => $courseId, 'title' => $title ?: 'Course ' . $courseId, 'courseType' => null,
                'courseTypeName' => $typeName, 'productTitle' => $productTitle];
        };
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
                if (is_array($row['courses'] ?? null)) {
                    // Courses sold on Skillvation itself (category "s2s", or a product that is only its own course) are bought
                    // through the course checkout, which already gives access. They are not Club Shop products: their IDs
                    // must never match Club Shop orders.
                    $ids = array_map(fn ($item) => (string) ($item['course_id'] ?? ''), $row['courses']);
                    if (($row['category_id'] ?? null) === 's2s' || ($ids && count(array_unique($ids)) === 1 && $ids[0] === 'course:' . ($row['product_id'] ?? ''))) continue;
                    // Product first: one product and everything it includes.
                    $key = $this->text($row['product_id'] ?? '', 80);
                    if ($key === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $key)) throw new RuntimeException('Invalid mapped product.');
                    $title = $this->text($row['product_title'] ?? '', 500);
                    $add($key, $title, $this->text($row['product_sku'] ?? '', 100));
                    foreach ($row['courses'] as $item) {
                        $id = (string) ($item['course_id'] ?? '');
                        $name = $this->text($item['course_name'] ?? '', 300);
                        if (preg_match('/^course:(\d{1,10})$/', $id, $m) && (int) $m[1] > 0) {
                            $course($key, (int) $m[1], $name, $this->text($item['course_category_name'] ?? '', 100), $title);
                        } elseif (preg_match('/^kit:(G(?:[1-9]|1[0-2])-K\d{2})$/', $id, $m)) {
                            $products[$key]['kits'][] = $m[1];
                        } elseif (preg_match('/^lab:([a-z0-9-]{1,60}):(basic|advanced|premium|all)$/', $id, $m)) {
                            $products[$key]['labs'][] = $m[1] . ':' . $m[2];
                        } elseif (preg_match('/^lms:([a-z0-9-]{1,60})$/', $id, $m)) {
                            $products[$key]['lms'][] = $m[1];
                        } elseif (preg_match('/^lms-category:([a-z0-9-]{1,80})$/', $id, $m)) {
                            $products[$key]['lmsCategories'][] = $m[1];
                        }
                        // Other kinds are ignored, so the mapper can add new ones without breaking access.
                    }
                } else {
                    // Older course-first feed.
                    $courseId = filter_var($row['course_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                    if (!$courseId || !is_array($row['products'] ?? null)) throw new RuntimeException('Invalid mapped course.');
                    foreach ($row['products'] as $item) {
                        $productId = filter_var($item['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                        if (!$productId) throw new RuntimeException('Invalid mapped product.');
                        $productTitle = $this->text($item['product_title'] ?? '', 500);
                        $add((string) $productId, $productTitle, $this->text($item['product_sku'] ?? '', 100));
                        $course((string) $productId, $courseId, $this->text($row['course_title'] ?? '', 300), $this->text($row['course_type_name'] ?? '', 100), $productTitle);
                    }
                }
            }
        }
        foreach ($products as &$product) foreach (['kits', 'labs', 'lms', 'lmsCategories'] as $kind) $product[$kind] = array_values(array_unique($product[$kind] ?? []));
        return ['products' => $products, 'skus' => $skus];
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
        return array_values(array_unique(array_map(fn ($line) => (int) $line->product_id, array_filter($this->paidLines($portalUserIds), fn ($line) => $line->product_id !== null))));
    }

    /** Paid, not cancelled or refunded Club Shop order lines (product ID and SKU) of these portal users. */
    private function paidLines(array $portalUserIds): array
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
            ->select(array_merge(['order_items.product_id', 'order_items.product_sku'], $this->hasBundleItems($shop) ? ['order_items.bundle_items'] : []))
            ->distinct()->get()->all();
    }

    private ?bool $bundleItems = null;
    private function hasBundleItems($shop): bool
    {
        return $this->bundleItems ??= $shop->getSchemaBuilder()->hasColumn('order_items', 'bundle_items');
    }

    /**
     * Mapper keys of the bundle categories bought: "bundle_cat_<bundle product>_<category>" for each Club Shop category
     * with at least one component in the bundle line (quantity above zero).
     */
    private function bundleCategoryKeys(array $lines): array
    {
        $components = [];
        foreach ($lines as $line) {
            $items = json_decode((string) ($line->bundle_items ?? ''), true);
            if ($line->product_id === null || !is_array($items)) continue;
            foreach ($items as $item) {
                if (is_array($item) && (int) ($item['qty'] ?? 0) > 0 && (int) ($item['product_id'] ?? 0) > 0) $components[(int) $line->product_id][] = (int) $item['product_id'];
            }
        }
        if (!$components) return [];
        $categories = DB::connection('clubshop')->table('products')->whereIn('id', array_unique(array_merge(...array_values($components))))
            ->pluck('category_id', 'id');
        $keys = [];
        foreach ($components as $bundle => $ids) foreach ($ids as $id) {
            if (!empty($categories[$id])) $keys['bundle_cat_' . $bundle . '_' . (int) $categories[$id]] = true;
        }
        return array_keys($keys);
    }

    /** Mapper product keys these portal users paid for (by product ID, or by SKU for bundles). */
    public function paidProductKeys(array $portalUserIds): array
    {
        $lines = $this->paidLines($portalUserIds);
        if (!$lines) return [];
        $catalog = $this->catalog();
        $keys = [];
        foreach ($lines as $line) {
            if ($line->product_id !== null && isset($catalog['products'][(string) $line->product_id])) $keys[(string) $line->product_id] = true;
            $sku = mb_strtolower(trim((string) ($line->product_sku ?? '')));
            if ($sku !== '' && isset($catalog['skus'][$sku])) $keys[$catalog['skus'][$sku]] = true;
        }
        foreach ($this->bundleCategoryKeys($lines) as $key) if (isset($catalog['products'][$key])) $keys[$key] = true;
        $keys = array_keys($keys);
        sort($keys);
        return $keys;
    }

    /** Robot kits, Skill Lab sections and lab LMS (single, or whole categories) included with the paid products. */
    public function unlocksForBuyers(array $portalUserIds): array
    {
        $empty = ['kits' => [], 'labs' => [], 'lms' => [], 'lmsCategories' => []];
        if (!$this->enabled()) return $empty;
        // The mapper is only asked when this person has paid shop orders.
        $keys = $this->paidProductKeys($portalUserIds);
        if (!$keys) return $empty;
        $products = $this->catalog()['products'];
        $out = $empty;
        foreach ($keys as $key) foreach (array_keys($empty) as $kind) array_push($out[$kind], ...($products[$key][$kind] ?? []));
        foreach ($out as $kind => $values) { $values = array_values(array_unique($values)); sort($values); $out[$kind] = $values; }
        return $out;
    }

    /**
     * Courses included with the lab products these portal users paid for, keyed by provider course ID.
     * Each entry lists the lab products that include it.
     */
    public function coursesForBuyers(array $portalUserIds): array
    {
        if (!$this->enabled()) return [];
        $keys = $this->paidProductKeys($portalUserIds);
        if (!$keys) return [];
        $products = $this->catalog()['products'];
        $courses = [];
        foreach ($keys as $key) {
            foreach ($products[$key]['courses'] as $courseId => $course) {
                $courses[$courseId] ??= ['courseId' => $course['courseId'], 'title' => $course['title'], 'courseType' => $course['courseType'], 'courseTypeName' => $course['courseTypeName'], 'labProducts' => []];
                $courses[$courseId]['labProducts'][] = ['productId' => ctype_digit($key) ? (int) $key : $key, 'title' => $course['productTitle']];
            }
        }
        ksort($courses);
        return $courses;
    }
}
