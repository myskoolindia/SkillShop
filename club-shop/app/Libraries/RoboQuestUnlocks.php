<?php

namespace App\Libraries;

/**
 * Unlocks RoboQuest (digital lab) Skill Labs content for paid shop orders.
 *
 * Each order is reported to RoboQuest's signed purchase webhook (see RoboQuest docs/SKILL_LABS.md):
 * a paid order unlocks the sections its products map to; cancelled or refunded lines re-lock them.
 * The school is found by the buyer's email (a RoboQuest School Admin or Teacher), or by an explicit
 * link in roboquest_school_links. Sends are idempotent; anything not delivered is retried by
 * `php spark roboquest:sync`.
 */
class RoboQuestUnlocks
{
    // Product title => sections. Digital Lab unlocks every topic at its level; ECE and Design Thinking
    // + 3D Printer products unlock their matching topic at their tier.
    private const RULES = [
        ['/^Digital Lab\b/i', 'all'],
        ['/^ECE\b/i', 'ece'],
        ['/^Design Thinking\b/i', 'design-3d'],
    ];
    private const LEVELS = ['basic' => 'basic', 'advance' => 'advanced', 'advanced' => 'advanced', 'premium' => 'premium'];
    private const WITHDRAWN = ['cancelled', 'refund_approved'];

    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->db->query('CREATE TABLE IF NOT EXISTS roboquest_unlocks (
            order_id INT UNSIGNED NOT NULL PRIMARY KEY, order_number VARCHAR(100) NULL, payload_hash CHAR(64) NULL,
            state VARCHAR(20) NOT NULL, school_id VARCHAR(80) NULL, attempts INT NOT NULL DEFAULT 0,
            last_error VARCHAR(500) NULL, updated_at DATETIME NOT NULL)');
        $this->db->query('CREATE TABLE IF NOT EXISTS roboquest_school_links (
            email VARCHAR(200) NOT NULL PRIMARY KEY, school_id VARCHAR(80) NOT NULL, updated_at DATETIME NOT NULL)');
    }

    /** Never lets a RoboQuest problem break a shop flow; failures stay queued for roboquest:sync. */
    public static function syncOrderSafely($orderId): void
    {
        try {
            (new self())->syncOrder((int)$orderId);
        } catch (\Throwable $e) {
            log_message('error', '[RoboQuestUnlocks] order ' . $orderId . ': ' . $e->getMessage());
        }
    }

    public static function configured(): bool
    {
        return (string)env('roboquest.webhookURL') !== '' && strlen((string)env('roboquest.webhookSecret')) >= 32;
    }

    /** Sections a product title unlocks, e.g. "ECE ... – Advanced Kit" => ["ece:advanced"]. */
    public static function sectionsFor(string $title): array
    {
        if (!preg_match('/\b(basic|advanced|advance|premium)\b/i', $title, $tier)) {
            return [];
        }
        foreach (self::RULES as [$pattern, $topic]) {
            if (preg_match($pattern, trim($title))) {
                return [$topic . ':' . self::LEVELS[strtolower($tier[1])]];
            }
        }
        return [];
    }

    /** The purchase to report for an order, or null when the order has nothing to unlock or revoke. */
    public function purchaseFor(object $order): ?array
    {
        $sections = [];
        $hadSections = false;
        foreach ($this->db->table('order_items')->where('order_id', $order->id)->get()->getResult() as $item) {
            $itemSections = $this->sectionsFor((string)$item->product_title);
            foreach (json_decode((string)$item->bundle_items, true) ?: [] as $component) {
                if ((int)($component['qty'] ?? 0) > 0 && !empty($component['product_id'])) {
                    $itemSections = array_merge($itemSections, $this->sectionsFor($this->productTitle((int)$component['product_id'])));
                }
            }
            $hadSections = $hadSections || !empty($itemSections);
            if (!in_array($item->order_status, self::WITHDRAWN, true)) {
                $sections = array_merge($sections, $itemSections);
            }
        }
        $sections = array_values(array_unique($sections));
        sort($sections);
        if (!$hadSections || $order->payment_status !== 'payment_received') {
            return null;
        }
        $items = array_map(function ($section) {
            [$topicId, $level] = explode(':', $section);
            return ['topicId' => $topicId, 'level' => $level];
        }, $sections);
        // A paid order whose RoboQuest lines were all cancelled or refunded re-locks what it unlocked.
        return ['orderId' => 'SHOP-' . $order->order_number, 'status' => $sections ? 'confirmed' : 'cancelled', 'items' => $items ?: [['topicId' => 'all', 'level' => 'all']]];
    }

    public function syncOrder(int $orderId, bool $retry = false): ?string
    {
        $order = $this->db->table('orders')->where('id', $orderId)->get()->getRow();
        if (empty($order) || !self::configured()) {
            return null;
        }
        $purchase = $this->purchaseFor($order);
        $row = $this->db->table('roboquest_unlocks')->where('order_id', $orderId)->get()->getRow();
        if ($purchase === null) {
            return $row->state ?? null;
        }
        $email = $this->buyerEmail($order);
        $link = $email ? $this->db->table('roboquest_school_links')->where('email', $email)->get()->getRow() : null;
        if (!empty($link)) {
            $purchase['schoolId'] = $link->school_id;
        } elseif ($email) {
            $purchase['buyerEmail'] = $email;
        } else {
            return $this->record($order, null, 'failed', 'The order has no buyer email; link it with roboquest:link', $row);
        }
        $hash = hash('sha256', json_encode($purchase));
        if (!empty($row) && $row->payload_hash === $hash && ($row->state === 'sent' || ($row->state === 'failed' && !$retry))) {
            return $row->state;
        }
        $purchase['occurredAt'] = gmdate('Y-m-d\TH:i:s\Z');
        [$state, $error, $schoolId] = $this->send($purchase);
        return $this->record($order, $hash, $state, $error, $row, $schoolId);
    }

    /** Re-sends orders that are pending, failed or never reported. Used by `php spark roboquest:sync`. */
    public function syncAll(int $limit = 500): array
    {
        $ids = $this->db->query("SELECT o.id FROM orders o LEFT JOIN roboquest_unlocks u ON u.order_id = o.id
            WHERE o.payment_status = 'payment_received' OR u.order_id IS NOT NULL ORDER BY o.id DESC LIMIT " . (int)$limit)->getResult();
        $counts = [];
        foreach ($ids as $id) {
            $state = $this->syncOrder((int)$id->id, true) ?? 'nothing to unlock';
            $counts[$state] = ($counts[$state] ?? 0) + 1;
        }
        return $counts;
    }

    public function link(string $email, string $schoolId): void
    {
        $this->db->query('REPLACE INTO roboquest_school_links (email, school_id, updated_at) VALUES (?, ?, ?)', [strtolower(trim($email)), $schoolId, date('Y-m-d H:i:s')]);
    }

    private function send(array $purchase): array
    {
        $body = json_encode($purchase);
        $timestamp = (string)time();
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, (string)env('roboquest.webhookSecret'));
        try {
            $response = \Config\Services::curlrequest(['timeout' => 15, 'http_errors' => false])->post((string)env('roboquest.webhookURL'), [
                'headers' => ['Content-Type' => 'application/json', 'X-RoboQuest-Timestamp' => $timestamp, 'X-RoboQuest-Signature' => $signature],
                'body' => $body,
            ]);
        } catch (\Throwable $e) {
            return ['pending', 'RoboQuest is unreachable: ' . $e->getMessage(), null];
        }
        $status = $response->getStatusCode();
        $result = json_decode($response->getBody(), true) ?: [];
        if ($status === 200 || $status === 202) {
            return ['sent', null, $result['schoolId'] ?? ($purchase['schoolId'] ?? null)];
        }
        // 404: the buyer's school is not known yet (it may not have synced); retried later.
        $state = $status === 404 || $status >= 500 ? 'pending' : 'failed';
        return [$state, 'HTTP ' . $status . ': ' . ($result['error'] ?? 'unexpected response'), null];
    }

    private function record(object $order, ?string $hash, string $state, ?string $error, $row, ?string $schoolId = null): string
    {
        $data = ['order_number' => $order->order_number, 'payload_hash' => $hash, 'state' => $state, 'last_error' => $error ? substr($error, 0, 500) : null,
            'school_id' => $schoolId ?? ($row->school_id ?? null), 'attempts' => (int)($row->attempts ?? 0) + 1, 'updated_at' => date('Y-m-d H:i:s')];
        if (empty($row)) {
            $this->db->table('roboquest_unlocks')->insert(['order_id' => $order->id] + $data);
        } else {
            $this->db->table('roboquest_unlocks')->where('order_id', $order->id)->update($data);
        }
        if ($state !== 'sent') {
            log_message('warning', '[RoboQuestUnlocks] order ' . $order->order_number . ' ' . $state . ': ' . $error);
        }
        return $state;
    }

    private function buyerEmail(object $order): ?string
    {
        if ($order->buyer_type === 'registered' && !empty($order->buyer_id)) {
            $user = $this->db->table('users')->select('email')->where('id', $order->buyer_id)->get()->getRow();
            if (!empty($user->email)) {
                return strtolower(trim($user->email));
            }
        }
        $shipping = json_decode((string)$order->shipping);
        $email = $shipping->sEmail ?? ($shipping->bEmail ?? null);
        return $email ? strtolower(trim($email)) : null;
    }

    private function productTitle(int $productId): string
    {
        $row = $this->db->table('product_details')->select('title')->where('product_id', $productId)->orderBy('lang_id', 'ASC')->get()->getRow();
        return (string)($row->title ?? '');
    }
}
