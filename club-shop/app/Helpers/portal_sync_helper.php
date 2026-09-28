<?php

use Config\Database;

/**
 * Get Laravel database connection instance safely
 */
if (!function_exists('getLaravelDb')) {
    function getLaravelDb()
    {
        static $db = null;
        if ($db === null) {
            try {
                $db = Database::connect('laravel', false);
            } catch (\Throwable $e) {
                $db = false;
            }
        }
        return $db;
    }
}

/**
 * Fetch dynamic menu items tree from Laravel DB by slug
 */
if (!function_exists('getPortalMenuBySlug')) {
    function getPortalMenuBySlug(string $slug): array
    {
        $db = getLaravelDb();
        if (!$db) {
            return [];
        }

        try {
            // Find menu by slug or partial match
            $menu = $db->table('menus')->like('slug', $slug)->get()->getFirstRow();
            if (!$menu) {
                return [];
            }

            $items = $db->table('menu_items')
                ->where('menu_id', $menu->id)
                ->orderBy('sort', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($items)) {
                return [];
            }

            // Build hierarchical tree
            $itemMap = [];
            foreach ($items as $item) {
                $item['child'] = [];
                $itemMap[$item['id']] = $item;
            }

            $tree = [];
            foreach ($itemMap as $id => &$item) {
                if (!empty($item['parent_id']) && isset($itemMap[$item['parent_id']])) {
                    $itemMap[$item['parent_id']]['child'][] = &$item;
                } else {
                    $tree[] = &$item;
                }
            }
            unset($item);

            // Special check: If labs menu item has empty child, populate standard labs
            foreach ($tree as &$m) {
                $labelLower = strtolower(trim($m['label'] ?? ''));
                $linkTrim = trim($m['link'] ?? '', '/');
                if (($labelLower === 'labs' || $linkTrim === 'labs') && empty($m['child'])) {
                    $m['child'] = [
                        ['label' => 'AI & Robotics Lab',   'link' => '/labs/ai-robotics'],
                        ['label' => 'STEM Lab',            'link' => '/labs/stem'],
                        ['label' => 'ECEC Lab',            'link' => '/labs/ecec'],
                        ['label' => 'Composite Skill Lab', 'link' => '/labs/composite-skill'],
                    ];
                }
            }
            unset($m);

            return $tree;
        } catch (\Throwable $e) {
            return [];
        }
    }
}

/**
 * Fetch footer settings from Laravel DB
 */
if (!function_exists('getPortalFooterSettings')) {
    function getPortalFooterSettings()
    {
        $db = getLaravelDb();
        if (!$db) {
            return null;
        }
        try {
            return $db->table('footer_settings')->get()->getFirstRow();
        } catch (\Throwable $e) {
            return null;
        }
    }
}

/**
 * Fetch social links from Laravel DB
 */
if (!function_exists('getPortalSocialLinks')) {
    function getPortalSocialLinks(): array
    {
        $db = getLaravelDb();
        if (!$db) {
            return [];
        }
        try {
            return $db->table('social_links')->get()->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

/**
 * Fetch site settings from Laravel DB
 */
if (!function_exists('getPortalSettings')) {
    function getPortalSettings()
    {
        $db = getLaravelDb();
        if (!$db) {
            return null;
        }
        try {
            return $db->table('settings')->get()->getFirstRow();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
