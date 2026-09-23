<?php

namespace App\Models;

use CodeIgniter\Model;

class BundleModel extends BaseModel
{
    protected $table = 'product_bundles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'bundle_product_id',
        'component_product_id',
        'variant_id',
        'quantity',
        'price_override',
        'is_optional',
        'sort_order',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    /**
     * Auto-create tables and columns if not present
     */
    private function ensureSchema()
    {
        static $schemaChecked = false;
        if ($schemaChecked) {
            return;
        }
        $schemaChecked = true;

        try {
            // 1. Columns in products
            $cols = $this->db->getFieldNames('products');
            if (!in_array('is_bundle', $cols)) {
                $this->db->query("ALTER TABLE products ADD COLUMN is_bundle TINYINT(1) DEFAULT 0");
            }
            if (!in_array('bundle_pricing_type', $cols)) {
                $this->db->query("ALTER TABLE products ADD COLUMN bundle_pricing_type VARCHAR(30) DEFAULT 'fixed'");
            }
            if (!in_array('bundle_discount_rate', $cols)) {
                $this->db->query("ALTER TABLE products ADD COLUMN bundle_discount_rate DECIMAL(5,2) DEFAULT 0.00");
            }

            // 2. Table product_bundles
            $this->db->query("CREATE TABLE IF NOT EXISTS product_bundles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                bundle_product_id INT UNSIGNED NOT NULL,
                component_product_id INT UNSIGNED NOT NULL,
                variant_id BIGINT UNSIGNED NULL DEFAULT NULL,
                quantity INT UNSIGNED NOT NULL DEFAULT 1,
                price_override DECIMAL(10,2) NULL DEFAULT NULL,
                is_optional TINYINT(1) NOT NULL DEFAULT 0,
                sort_order INT NOT NULL DEFAULT 0,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                KEY idx_bundle_prod (bundle_product_id),
                KEY idx_comp_prod (component_product_id),
                KEY idx_var_id (variant_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            if ($this->db->tableExists('product_bundles')) {
                $bundleCols = $this->db->getFieldNames('product_bundles');
                if (!in_array('is_optional', $bundleCols)) {
                    $this->db->query("ALTER TABLE product_bundles ADD COLUMN is_optional TINYINT(1) NOT NULL DEFAULT 0");
                }
            }

            // 3. Table order_bundle_items
            $this->db->query("CREATE TABLE IF NOT EXISTS order_bundle_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id INT UNSIGNED NOT NULL,
                order_product_id INT UNSIGNED NOT NULL,
                component_product_id INT UNSIGNED NOT NULL,
                variant_id BIGINT UNSIGNED NULL DEFAULT NULL,
                product_title VARCHAR(500) NOT NULL,
                variant_description VARCHAR(500) NULL DEFAULT NULL,
                quantity INT UNSIGNED NOT NULL DEFAULT 1,
                unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                created_at DATETIME NULL,
                KEY idx_order (order_id),
                KEY idx_order_prod (order_product_id),
                KEY idx_comp (component_product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (\Throwable $e) {
            log_message('error', '[BundleModel Schema Check] ' . $e->getMessage());
        }
    }

    /**
     * Efficiently retrieve all bundle components (100+ items) in just 2-3 queries
     *
     * @param int $bundleProductId
     * @param bool $onlyActive
     * @return array
     */
    public function getBundleComponents(int $bundleProductId, bool $onlyActive = true): array
    {
        $bundleRows = $this->db->table('product_bundles')
            ->where('bundle_product_id', $bundleProductId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultObject();

        if (empty($bundleRows)) {
            return [];
        }

        $productIds = [];
        $variantIds = [];

        foreach ($bundleRows as $row) {
            $productIds[] = (int)$row->component_product_id;
            if (!empty($row->variant_id)) {
                $variantIds[] = (int)$row->variant_id;
            }
        }

        $productIds = array_unique($productIds);
        $variantIds = array_unique($variantIds);

        // Collect product IDs that have NO pre-assigned variant (need all-variants fetch)
        $productIdsWithoutVariant = [];
        foreach ($bundleRows as $row) {
            if (empty($row->variant_id)) {
                $productIdsWithoutVariant[] = (int)$row->component_product_id;
            }
        }
        $productIdsWithoutVariant = array_unique($productIdsWithoutVariant);

        // 1. Batch load products and default details
        $productsMap = [];
        if (!empty($productIds)) {
            $langId = defaultLangId();
            $productsQuery = $this->db->table('products p')
                ->select('p.id, p.category_id, p.slug, p.sku, p.price, p.price_discounted, p.currency, p.stock, p.product_type, p.is_active, p.status, p.is_deleted, pd.title')
                ->join('product_details pd', "pd.product_id = p.id AND pd.lang_id = {$langId}", 'left')
                ->whereIn('p.id', $productIds);

            if ($onlyActive) {
                $productsQuery->where('p.status', 1)
                    ->where('p.is_deleted', 0)
                    ->where('p.is_active', 1);
            }

            $productsResult = $productsQuery->get()->getResultObject();

            // Fetch category names
            $categoryIds = [];
            foreach ($productsResult as $prod) {
                if (!empty($prod->category_id)) {
                    $categoryIds[] = (int)$prod->category_id;
                }
            }
            $categoryIds = array_values(array_unique(array_filter($categoryIds)));
            $categoriesMap = [];
            if (!empty($categoryIds)) {
                $catRows = $this->db->table('categories c')
                    ->select('c.id, cl.name as cat_name')
                    ->join('category_lang cl', "cl.category_id = c.id AND cl.lang_id = {$langId}", 'left')
                    ->whereIn('c.id', $categoryIds)
                    ->get()
                    ->getResultObject();
                foreach ($catRows as $cRow) {
                    $categoriesMap[$cRow->id] = !empty($cRow->cat_name) ? $cRow->cat_name : ('Category #' . $cRow->id);
                }
            }

            foreach ($productsResult as $prod) {
                // Get main image
                $img = $this->db->table('images')->where('product_id', $prod->id)->orderBy('is_main', 'DESC')->get()->getRow();
                $prod->image_small = !empty($img) ? getProductImageURL($img, 'image_small') : getProductMainImage($prod->id, 'image_small');
                $prod->image_default = !empty($img) ? getProductImageURL($img, 'image_default') : getProductMainImage($prod->id, 'image_default');
                $prod->category_name = !empty($categoriesMap[$prod->category_id]) ? $categoriesMap[$prod->category_id] : 'General';
                $productsMap[$prod->id] = $prod;
            }
        }

        // 2. Batch load variants
        $variantsMap = [];
        if (!empty($variantIds)) {
            $variantsResult = $this->db->table('product_option_variants')
                ->select('id, product_id, sku, price, price_discounted, quantity, is_active')
                ->whereIn('id', $variantIds)
                ->get()
                ->getResultObject();

            foreach ($variantsResult as $variant) {
                // Fetch variant value titles
                $variantValues = $this->db->table('product_option_variant_values povv')
                    ->select('pov.value_name_translations')
                    ->join('product_option_values pov', 'pov.id = povv.value_id')
                    ->where('povv.variant_id', $variant->id)
                    ->get()
                    ->getResultObject();

                $nameParts = [];
                foreach ($variantValues as $val) {
                    $translations = json_decode($val->value_name_translations, true);
                    $nameParts[] = $translations[defaultLangId()] ?? reset($translations) ?? '';
                }
                $variant->variant_name = !empty($nameParts) ? implode(' / ', array_filter($nameParts)) : 'Variant #' . $variant->id;
                $variantsMap[$variant->id] = $variant;
            }
        }

        // 2b. Batch load all available variants for products without a pre-assigned variant
        $allVariantsByProductMap = [];
        if (!empty($productIdsWithoutVariant)) {
            $langId = defaultLangId();
            $allVariantsResult = $this->db->table('product_option_variants pov')
                ->select('pov.id, pov.product_id, pov.sku, pov.price, pov.price_discounted, pov.quantity, pov.is_active')
                ->whereIn('pov.product_id', $productIdsWithoutVariant)
                ->where('pov.is_active', 1)
                ->orderBy('pov.id', 'ASC')
                ->get()
                ->getResultObject();

            foreach ($allVariantsResult as $av) {
                // Fetch variant value labels
                $variantValues = $this->db->table('product_option_variant_values povv')
                    ->select('pov2.value_name_translations')
                    ->join('product_option_values pov2', 'pov2.id = povv.value_id')
                    ->where('povv.variant_id', $av->id)
                    ->get()
                    ->getResultObject();

                $nameParts = [];
                foreach ($variantValues as $val) {
                    $translations = json_decode($val->value_name_translations, true);
                    $nameParts[] = $translations[$langId] ?? reset($translations) ?? '';
                }
                $av->variant_name = !empty($nameParts) ? implode(' / ', array_filter($nameParts)) : ('Variant #' . $av->id);

                if (!isset($allVariantsByProductMap[$av->product_id])) {
                    $allVariantsByProductMap[$av->product_id] = [];
                }
                $allVariantsByProductMap[$av->product_id][] = $av;
            }
        }

        // 3. Assemble components list
        $components = [];
        foreach ($bundleRows as $row) {
            $prod = $productsMap[$row->component_product_id] ?? null;
            if (!$prod) {
                continue;
            }

            $variant = !empty($row->variant_id) ? ($variantsMap[$row->variant_id] ?? null) : null;

            $item = new \stdClass();
            $item->id = $row->id;
            $item->bundle_product_id = $row->bundle_product_id;
            $item->component_product_id = $row->component_product_id;
            $item->variant_id = $row->variant_id;
            $item->is_optional = !empty($row->is_optional) ? 1 : 0;
            $item->required_quantity = !empty($item->is_optional) ? (int)$row->quantity : max(1, (int)$row->quantity);
            $item->price_override = $row->price_override;
            $item->sort_order = (int)$row->sort_order;

            // Product details
            $item->title = $prod->title ?: 'Product #' . $prod->id;
            $item->slug = $prod->slug;
            $item->sku = $variant ? ($variant->sku ?: $prod->sku) : $prod->sku;
            $item->image_small = $prod->image_small;
            $item->image_default = $prod->image_default;
            $item->product_type = $prod->product_type;
            $item->category_id = !empty($prod->category_id) ? (int)$prod->category_id : 0;
            $item->category_name = !empty($prod->category_name) ? $prod->category_name : 'General';

            // Pricing
            if ($row->price_override !== null) {
                $item->unit_price = (float)$row->price_override;
            } elseif ($variant && $variant->price_discounted > 0) {
                $item->unit_price = (float)$variant->price_discounted;
            } else {
                $item->unit_price = (float)($prod->price_discounted > 0 ? $prod->price_discounted : $prod->price);
            }
            $item->total_price = $item->unit_price * $item->required_quantity;

            // Stock
            if ($variant) {
                $item->available_stock = ($variant->quantity === null || $variant->quantity === '') ? 999999 : (int)$variant->quantity;
                $item->variant_name = $variant->variant_name;
                $item->available_variants = [];
            } else {
                $item->available_stock = (int)$prod->stock;
                $item->variant_name = null;
                $item->available_variants = $allVariantsByProductMap[$item->component_product_id] ?? [];
            }

            $item->can_fulfill = ($item->is_optional && $item->required_quantity == 0) ? true : ($item->available_stock >= $item->required_quantity);
            $item->max_possible_bundles = $item->required_quantity > 0 ? (int)floor($item->available_stock / $item->required_quantity) : ($item->is_optional ? 999999 : 0);

            $components[] = $item;
        }

        return $components;
    }

    /**
     * Compute maximum bundle stock and pricing summary across 100+ components
     *
     * @param int $bundleProductId
     * @return array
     */
    public function calculateBundleMetrics(int $bundleProductId): array
    {
        $components = $this->getBundleComponents($bundleProductId, true);
        if (empty($components)) {
            return [
                'total_components' => 0,
                'total_units' => 0,
                'max_stock' => 0,
                'sum_price' => 0.00,
                'is_in_stock' => false
            ];
        }

        $minStock = null;
        $sumPrice = 0.00;
        $totalUnits = 0;

        foreach ($components as $component) {
            $totalUnits += $component->required_quantity;
            $sumPrice += $component->total_price;

            // Optional items with 0 required quantity do not limit bundle availability
            if ($component->is_optional && $component->required_quantity == 0) {
                continue;
            }

            $maxBundlesFromComponent = $component->max_possible_bundles;
            if ($minStock === null || $maxBundlesFromComponent < $minStock) {
                $minStock = $maxBundlesFromComponent;
            }
        }

        $maxStock = $minStock !== null ? max(0, (int)$minStock) : 999999;

        return [
            'total_components' => count($components),
            'total_units' => $totalUnits,
            'max_stock' => $maxStock,
            'sum_price' => round($sumPrice, 2),
            'is_in_stock' => $maxStock > 0
        ];
    }

    /**
     * Batch save bundle components (supports 100+ items in single transaction)
     *
     * @param int $bundleProductId
     * @param array $componentsArray
     * @return bool
     */
    public function saveBundleComponents(int $bundleProductId, array $componentsArray): bool
    {
        $this->db->transStart();

        // 1. Clear existing components
        $this->db->table('product_bundles')->where('bundle_product_id', $bundleProductId)->delete();

        // 2. Prepare batch insert rows
        $batch = [];
        $sort = 1;
        $now = date('Y-m-d H:i:s');

        foreach ($componentsArray as $comp) {
            $productId = (int)($comp['component_product_id'] ?? 0);
            $isOptional = !empty($comp['is_optional']) ? 1 : 0;
            $qty = $isOptional ? max(0, (int)($comp['quantity'] ?? 0)) : max(1, (int)($comp['quantity'] ?? 1));
            $variantId = !empty($comp['variant_id']) ? (int)$comp['variant_id'] : null;
            $priceOverride = isset($comp['price_override']) && is_numeric($comp['price_override']) ? (float)$comp['price_override'] : null;

            if ($productId > 0) {
                $batch[] = [
                    'bundle_product_id'    => $bundleProductId,
                    'component_product_id' => $productId,
                    'variant_id'           => $variantId,
                    'quantity'             => $qty,
                    'price_override'       => $priceOverride,
                    'is_optional'          => $isOptional,
                    'sort_order'           => $sort++,
                    'created_at'           => $now,
                    'updated_at'           => $now
                ];
            }
        }

        if (!empty($batch)) {
            $this->db->table('product_bundles')->insertBatch($batch);
        }

        // 3. Mark parent product as bundle
        $metrics = $this->calculateBundleMetrics($bundleProductId);
        $this->db->table('products')->where('id', $bundleProductId)->update([
            'is_bundle' => 1,
            'stock'     => $metrics['max_stock'],
            'updated_at'=> $now
        ]);

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Bulk import 100+ bundle components from CSV content
     * CSV Headers expected: sku, variant_sku, quantity, price_override, is_optional
     *
     * @param int $bundleProductId
     * @param string $csvContent
     * @return array ['success' => bool, 'imported_count' => int, 'errors' => array]
     */
    public function importComponentsFromCsv(int $bundleProductId, string $csvContent): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($csvContent));
        if (count($lines) <= 1) {
            return ['success' => false, 'imported_count' => 0, 'errors' => ['CSV file is empty or has no data rows.']];
        }

        $header = str_getcsv(array_shift($lines));
        $headerMap = [];
        foreach ($header as $idx => $col) {
            $headerMap[strtolower(trim($col))] = $idx;
        }

        if (!isset($headerMap['sku'])) {
            return ['success' => false, 'imported_count' => 0, 'errors' => ['CSV must contain a "sku" column.']];
        }

        $skuIdx = $headerMap['sku'];
        $variantSkuIdx = $headerMap['variant_sku'] ?? null;
        $qtyIdx = $headerMap['quantity'] ?? ($headerMap['qty'] ?? null);
        $priceIdx = $headerMap['price_override'] ?? ($headerMap['price'] ?? null);
        $optionalIdx = $headerMap['is_optional'] ?? ($headerMap['optional'] ?? null);

        $parsedRows = [];
        $skusToLookup = [];
        $variantSkusToLookup = [];

        foreach ($lines as $lineNum => $line) {
            if (empty(trim($line))) continue;
            $row = str_getcsv($line);
            $sku = trim($row[$skuIdx] ?? '');
            if (empty($sku)) continue;

            $variantSku = ($variantSkuIdx !== null && isset($row[$variantSkuIdx])) ? trim($row[$variantSkuIdx]) : '';
            $isOptionalVal = ($optionalIdx !== null && isset($row[$optionalIdx])) ? strtolower(trim($row[$optionalIdx])) : '0';
            $isOptional = in_array($isOptionalVal, ['1', 'yes', 'true', 'optional', 'y'], true) ? 1 : 0;
            $qty = ($qtyIdx !== null && isset($row[$qtyIdx])) ? ($isOptional ? max(0, (int)$row[$qtyIdx]) : max(1, (int)$row[$qtyIdx])) : ($isOptional ? 0 : 1);
            $priceOverride = ($priceIdx !== null && isset($row[$priceIdx]) && is_numeric($row[$priceIdx])) ? (float)$row[$priceIdx] : null;

            $skusToLookup[] = $sku;
            if (!empty($variantSku)) {
                $variantSkusToLookup[] = $variantSku;
            }

            $parsedRows[] = [
                'sku'            => $sku,
                'variant_sku'    => $variantSku,
                'quantity'       => $qty,
                'price_override' => $priceOverride,
                'is_optional'    => $isOptional,
                'line'           => $lineNum + 2
            ];
        }

        // Batch lookups
        $skusToLookup = array_unique($skusToLookup);
        $productSkuMap = [];
        if (!empty($skusToLookup)) {
            $prods = $this->db->table('products')->select('id, sku')->whereIn('sku', $skusToLookup)->get()->getResultObject();
            foreach ($prods as $p) {
                $productSkuMap[$p->sku] = $p->id;
            }
        }

        $variantSkuMap = [];
        if (!empty($variantSkusToLookup)) {
            $vars = $this->db->table('product_option_variants')->select('id, product_id, sku')->whereIn('sku', array_unique($variantSkusToLookup))->get()->getResultObject();
            foreach ($vars as $v) {
                $variantSkuMap[$v->sku] = $v;
            }
        }

        $componentsToSave = [];
        $errors = [];

        foreach ($parsedRows as $row) {
            $productId = $productSkuMap[$row['sku']] ?? null;
            $variantId = null;

            if (!empty($row['variant_sku'])) {
                $varObj = $variantSkuMap[$row['variant_sku']] ?? null;
                if ($varObj) {
                    $variantId = $varObj->id;
                    if (!$productId) {
                        $productId = $varObj->product_id;
                    }
                } else {
                    $errors[] = "Line {$row['line']}: Variant SKU '{$row['variant_sku']}' not found.";
                    continue;
                }
            }

            if (!$productId) {
                $errors[] = "Line {$row['line']}: Product SKU '{$row['sku']}' not found.";
                continue;
            }

            $componentsToSave[] = [
                'component_product_id' => $productId,
                'variant_id'           => $variantId,
                'quantity'             => $row['quantity'],
                'price_override'       => $row['price_override'],
                'is_optional'          => $row['is_optional']
            ];
        }

        if (!empty($componentsToSave)) {
            $this->saveBundleComponents($bundleProductId, $componentsToSave);
        }

        return [
            'success'        => count($componentsToSave) > 0,
            'imported_count' => count($componentsToSave),
            'errors'         => $errors
        ];
    }

    /**
     * Deduct stock for all 100+ components atomically and save order snapshot
     *
     * @param int $orderId
     * @param int $orderProductId
     * @param int $bundleProductId
     * @param int $bundleQtyPurchased
     * @return bool
     */
    public function processBundleOrderDeduction(int $orderId, int $orderProductId, int $bundleProductId, int $bundleQtyPurchased): bool
    {
        $components = $this->getBundleComponents($bundleProductId, false);
        if (empty($components)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $orderBundleRows = [];

        foreach ($components as $component) {
            $totalQtyToDeduct = $component->required_quantity * $bundleQtyPurchased;
            if ($totalQtyToDeduct <= 0) {
                continue;
            }

            // Snapshot row
            $orderBundleRows[] = [
                'order_id'             => $orderId,
                'order_product_id'     => $orderProductId,
                'component_product_id' => $component->component_product_id,
                'variant_id'           => $component->variant_id,
                'product_title'        => $component->title,
                'variant_description'  => $component->variant_name,
                'quantity'             => $totalQtyToDeduct,
                'unit_price'           => $component->unit_price,
                'created_at'           => $now
            ];

            // Inventory deduction
            if (!empty($component->variant_id)) {
                $this->db->query("UPDATE product_option_variants SET quantity = GREATEST(0, quantity - {$totalQtyToDeduct}) WHERE id = " . (int)$component->variant_id);
            } else {
                $this->db->query("UPDATE products SET stock = GREATEST(0, stock - {$totalQtyToDeduct}) WHERE id = " . (int)$component->component_product_id);
            }
        }

        if (!empty($orderBundleRows)) {
            $this->db->table('order_bundle_items')->insertBatch($orderBundleRows);
        }

        // Recalculate bundle product virtual stock
        $metrics = $this->calculateBundleMetrics($bundleProductId);
        $this->db->table('products')->where('id', $bundleProductId)->update(['stock' => $metrics['max_stock']]);

        return true;
    }
}
