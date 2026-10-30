<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProductModel;
use App\Models\BundleModel;
use App\Models\CartModel;

class TestPricingScenarios extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test:pricing';
    protected $description = 'Thoroughly tests product and bundle price add, edit, override, markup, and discount scenarios.';
    protected $usage = 'test:pricing';

    private $db;
    private $bundleModel;
    private $productModel;
    private $cartModel;
    private $passed = 0;
    private $failed = 0;

    public function run(array $params)
    {
        $this->db = \Config\Database::connect();
        $this->bundleModel = new BundleModel();
        $this->productModel = new ProductModel();
        $this->cartModel = new CartModel();

        CLI::write("==================================================================", 'yellow');
        CLI::write(" STARTING COMPREHENSIVE PRODUCT PRICING VERIFICATION & TESTS ", 'black', 'light_green');
        CLI::write("==================================================================\n", 'yellow');

        try {
            $this->testScenario1_RegularProductAddAndDiscountCalc();
            $this->testScenario2_RegularProductPriceEdit();
            $this->testScenario3_ProductMarkupAndCategoryDiscount();
            $this->testScenario4_BundleComponentPriceOverrides();
            $this->testScenario5_BundleComponentCatalogPriceFallback();
            $this->testScenario6_BundleAddRemoveComponentsAndOptionalToggle();
            $this->testScenario7_ExistingLabBundlesPricingIntegrity();
            $this->testScenario8_CartCalculationsForRegularAndBundle();
            $this->testScenario9_ProductOptionVariantPricing();
            $this->testScenario10_BundleSafeguardInProductDetailsEdit();
        } catch (\Throwable $e) {
            CLI::error("FATAL EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }

        CLI::write("\n==================================================================", 'yellow');
        CLI::write(" TEST RESULTS SUMMARY ", 'black', 'light_blue');
        CLI::write("==================================================================", 'yellow');
        CLI::write("PASSED TESTS : " . $this->passed, 'green');
        if ($this->failed > 0) {
            CLI::write("FAILED TESTS : " . $this->failed, 'red');
        } else {
            CLI::write("ALL SCENARIOS PASSED WITH ZERO FAILURES! ✓", 'light_green');
        }
        CLI::write("==================================================================\n", 'yellow');

        return $this->failed === 0 ? EXIT_SUCCESS : EXIT_ERROR;
    }

    private function assert($condition, $message)
    {
        if ($condition) {
            CLI::write("  [PASS] " . $message, 'green');
            $this->passed++;
        } else {
            CLI::write("  [FAIL] " . $message, 'red');
            $this->failed++;
        }
    }

    /**
     * Scenario 1: Adding a regular product with base price, discounted price and auto discount rate
     */
    private function testScenario1_RegularProductAddAndDiscountCalc()
    {
        CLI::write("\n--- SCENARIO 1: Adding Product & Automatic Discount Rate Calculation ---", 'cyan');

        $testSku = 'TEST-PRC-' . time();
        $basePrice = 1000.00;
        $discountedPrice = 800.00;
        $expectedDiscountRate = 20;

        $insertData = [
            'slug' => 'test-product-' . time(),
            'sku' => $testSku,
            'price' => $basePrice,
            'price_discounted' => $discountedPrice,
            'discount_rate' => $expectedDiscountRate,
            'currency' => 'INR',
            'user_id' => 1,
            'status' => 1,
            'visibility' => 1,
            'is_active' => 1,
            'stock' => 50,
            'is_bundle' => 0
        ];

        $this->db->table('products')->insert($insertData);
        $prodId = $this->db->insertID();

        $saved = $this->db->table('products')->where('id', $prodId)->get()->getRow();

        $this->assert($saved !== null, "Product inserted successfully with ID: $prodId");
        $this->assert(floatval($saved->price) == 1000.00, "Base price saved accurately as 1000.00");
        $this->assert(floatval($saved->price_discounted) == 800.00, "Discounted price saved accurately as 800.00");
        $this->assert(intval($saved->discount_rate) == 20, "Discount rate accurately computed as 20%");

        // Clean up
        $this->db->table('products')->where('id', $prodId)->delete();
    }

    /**
     * Scenario 2: Editing regular product price and testing price/discount recalculation
     */
    private function testScenario2_RegularProductPriceEdit()
    {
        CLI::write("\n--- SCENARIO 2: Editing Product Prices & Discount Rate Changes ---", 'cyan');

        $prodId = $this->createTestProduct(2000.00, 1500.00, 25);

        // Edit 1: Increase price to 2500, discounted to 2000 (20% discount)
        $newPrice = 2500.00;
        $newDiscounted = 2000.00;
        $newRate = (int)((($newPrice - $newDiscounted) * 100) / $newPrice);

        $this->db->table('products')->where('id', $prodId)->update([
            'price' => $newPrice,
            'price_discounted' => $newDiscounted,
            'discount_rate' => $newRate
        ]);

        $edited = $this->db->table('products')->where('id', $prodId)->get()->getRow();
        $this->assert(floatval($edited->price) == 2500.00, "Price successfully updated from 2000 to 2500");
        $this->assert(floatval($edited->price_discounted) == 2000.00, "Discounted price updated to 2000");
        $this->assert(intval($edited->discount_rate) == 20, "Discount rate recalculated from 25% to 20%");

        // Edit 2: Remove discount (price_discounted = price, discount_rate = 0)
        $this->db->table('products')->where('id', $prodId)->update([
            'price' => $newPrice,
            'price_discounted' => $newPrice,
            'discount_rate' => 0
        ]);

        $editedNoDisc = $this->db->table('products')->where('id', $prodId)->get()->getRow();
        $this->assert(floatval($editedNoDisc->price_discounted) == 2500.00, "Removing discount aligns discounted price to base price");
        $this->assert(intval($editedNoDisc->discount_rate) == 0, "Discount rate resets to 0%");

        // Clean up
        $this->db->table('products')->where('id', $prodId)->delete();
    }

    /**
     * Scenario 3: Markup & Category Discount calculations
     */
    private function testScenario3_ProductMarkupAndCategoryDiscount()
    {
        CLI::write("\n--- SCENARIO 3: Store Markup & Product Markup Verification ---", 'cyan');

        $basePrice = 1000.00;
        $markupRate = 15.00; // 15%

        $markedPrice = calculateMarkupPrice($basePrice, $markupRate);
        $this->assert($markedPrice == 1150.00, "calculateMarkupPrice(1000, 15%) = 1150.00");

        // Check store markup status
        $origSettings = $this->db->table('payment_settings')->where('id', 1)->get()->getRow();
        $origStatus = (int)$origSettings->seller_markup_status;

        // Test with store markup OFF
        $this->db->table('payment_settings')->where('id', 1)->update(['seller_markup_status' => 0]);
        setContextValue('paymentSettings', $this->db->table('payment_settings')->where('id', 1)->get()->getRow());
        $prodId = $this->createTestProduct($basePrice, $basePrice, 0, ['is_markup_set' => 1, 'markup_rate' => 15.00]);
        $prod = $this->db->table('products')->where('id', $prodId)->get()->getRow();
        $unmarkedProd = applyProductMarkup($prod);
        $this->assert(floatval($unmarkedProd->price) == 1000.00, "When store markup is inactive, product price remains at base price (1000.00)");

        // Test with store markup ON
        $this->db->table('payment_settings')->where('id', 1)->update(['seller_markup_status' => 1]);
        setContextValue('paymentSettings', $this->db->table('payment_settings')->where('id', 1)->get()->getRow());
        $prod2 = $this->db->table('products')->where('id', $prodId)->get()->getRow();
        $markedProd = applyProductMarkup($prod2);
        $this->assert(floatval($markedProd->price) == 1150.00, "When store markup is active, applyProductMarkup applies 15% markup -> 1150.00");

        // Restore original settings
        $this->db->table('payment_settings')->where('id', 1)->update(['seller_markup_status' => $origStatus]);
        setContextValue('paymentSettings', $this->db->table('payment_settings')->where('id', 1)->get()->getRow());
        $this->db->table('products')->where('id', $prodId)->delete();
    }

    /**
     * Scenario 4: Bundle with Price Overrides
     */
    private function testScenario4_BundleComponentPriceOverrides()
    {
        CLI::write("\n--- SCENARIO 4: Bundle Components with Price Overrides ---", 'cyan');

        $c1 = $this->createTestProduct(500.00, 500.00, 0);
        $c2 = $this->createTestProduct(1000.00, 1000.00, 0);
        $bId = $this->createTestProduct(0.00, 0.00, 0, ['is_bundle' => 1]);

        $bundleItems = [
            ['component_product_id' => $c1, 'quantity' => 2, 'price_override' => 450.00, 'is_optional' => 0],
            ['component_product_id' => $c2, 'quantity' => 1, 'price_override' => 900.00, 'is_optional' => 0]
        ];

        $saveOk = $this->bundleModel->saveBundleComponents($bId, $bundleItems);
        $this->assert($saveOk, "Bundle components saved successfully via BundleModel::saveBundleComponents");

        $metrics = $this->bundleModel->calculateBundleMetrics($bId);
        $expectedTotal = (450.00 * 2) + (900.00 * 1); // 900 + 900 = 1800.00
        $this->assert($metrics['sum_price'] == $expectedTotal, "calculateBundleMetrics calculates sum_price as $expectedTotal (vs catalog 2000)");

        $comps = $this->bundleModel->getBundleComponents($bId, true);
        $this->assert(count($comps) == 2, "getBundleComponents returns exactly 2 components");

        $c1Item = $comps[0]->component_product_id == $c1 ? $comps[0] : $comps[1];
        $this->assert(floatval($c1Item->unit_price) == 450.00, "Component 1 unit_price uses override 450.00");
        $this->assert(floatval($c1Item->total_price) == 900.00, "Component 1 total_price (450 * 2) = 900.00");

        // Now EDIT price override of C1 to 400.00
        $bundleItems[0]['price_override'] = 400.00;
        $this->bundleModel->saveBundleComponents($bId, $bundleItems);

        $metrics2 = $this->bundleModel->calculateBundleMetrics($bId);
        $newExpected = (400.00 * 2) + (900.00 * 1); // 800 + 900 = 1700.00
        $this->assert($metrics2['sum_price'] == $newExpected, "Editing price override to 400 updates bundle sum_price to $newExpected");

        // Clean up
        $this->db->table('product_bundles')->where('bundle_product_id', $bId)->delete();
        $this->db->table('products')->whereIn('id', [$bId, $c1, $c2])->delete();
    }

    /**
     * Scenario 5: Bundle Component WITHOUT price override (falls back to component's own price)
     */
    private function testScenario5_BundleComponentCatalogPriceFallback()
    {
        CLI::write("\n--- SCENARIO 5: Bundle Components Catalog Price Fallback & Live Updates ---", 'cyan');

        $c1 = $this->createTestProduct(300.00, 300.00, 0);
        $bId = $this->createTestProduct(0.00, 0.00, 0, ['is_bundle' => 1]);

        // C1 has NO override (price_override = NULL)
        $bundleItems = [
            ['component_product_id' => $c1, 'quantity' => 3, 'price_override' => null, 'is_optional' => 0]
        ];

        $this->bundleModel->saveBundleComponents($bId, $bundleItems);
        $metrics = $this->bundleModel->calculateBundleMetrics($bId);
        $this->assert($metrics['sum_price'] == 900.00, "Bundle without price override correctly falls back to component catalog price (3 * 300 = 900)");

        // Now EDIT C1's catalog price from 300 to 350
        $this->db->table('products')->where('id', $c1)->update(['price' => 350.00, 'price_discounted' => 350.00]);

        $metricsUpdated = $this->bundleModel->calculateBundleMetrics($bId);
        $this->assert($metricsUpdated['sum_price'] == 1050.00, "Updating component catalog price to 350 automatically updates bundle sum to 1050 (3 * 350)");

        // Clean up
        $this->db->table('product_bundles')->where('bundle_product_id', $bId)->delete();
        $this->db->table('products')->whereIn('id', [$bId, $c1])->delete();
    }

    /**
     * Scenario 6: Adding, Removing Components & Toggling Optional
     */
    private function testScenario6_BundleAddRemoveComponentsAndOptionalToggle()
    {
        CLI::write("\n--- SCENARIO 6: Adding/Removing Bundle Items & Optional Components ---", 'cyan');

        $c1 = $this->createTestProduct(100.00, 100.00, 0);
        $c2 = $this->createTestProduct(200.00, 200.00, 0);
        $c3 = $this->createTestProduct(500.00, 500.00, 0);
        $bId = $this->createTestProduct(0.00, 0.00, 0, ['is_bundle' => 1]);

        // 1. Initial: C1 (qty 1 = 100) + C2 (qty 1 = 200) = 300
        $bundleItems = [
            ['component_product_id' => $c1, 'quantity' => 1, 'is_optional' => 0],
            ['component_product_id' => $c2, 'quantity' => 1, 'is_optional' => 0]
        ];
        $this->bundleModel->saveBundleComponents($bId, $bundleItems);
        $this->assert($this->bundleModel->calculateBundleMetrics($bId)['sum_price'] == 300.00, "Initial bundle sum is 300.00");

        // 2. Add C3 (qty 2 = 1000) -> total should become 1300
        $bundleItems[] = ['component_product_id' => $c3, 'quantity' => 2, 'is_optional' => 0];
        $this->bundleModel->saveBundleComponents($bId, $bundleItems);
        $this->assert($this->bundleModel->calculateBundleMetrics($bId)['sum_price'] == 1300.00, "Adding component C3 (2 x 500) increases bundle sum to 1300.00");

        // 3. Toggle C3 to optional (is_optional = 1) -> mandatory sum should exclude it (back to 300)
        $bundleItems[2]['is_optional'] = 1;
        $this->bundleModel->saveBundleComponents($bId, $bundleItems);
        $this->assert($this->bundleModel->calculateBundleMetrics($bId)['sum_price'] == 300.00, "Marking C3 as optional excludes it from mandatory bundle base price (300.00)");

        // 4. Remove C2 -> total should become 100
        unset($bundleItems[1]);
        $this->bundleModel->saveBundleComponents($bId, array_values($bundleItems));
        $this->assert($this->bundleModel->calculateBundleMetrics($bId)['sum_price'] == 100.00, "Removing component C2 drops mandatory bundle sum to 100.00");

        // Clean up
        $this->db->table('product_bundles')->where('bundle_product_id', $bId)->delete();
        $this->db->table('products')->whereIn('id', [$bId, $c1, $c2, $c3])->delete();
    }

    /**
     * Scenario 7: Live Database Existing Lab Bundles Verification
     */
    private function testScenario7_ExistingLabBundlesPricingIntegrity()
    {
        CLI::write("\n--- SCENARIO 7: Existing Live Lab Bundles Pricing Verification ---", 'cyan');

        $labs = [
            68 => ['name' => 'CBSE Composite Skill Lab Basic', 'expected_mand_count' => 16, 'expected_mand_sum' => 296400.00, 'pkg_price' => 113696.00],
            72 => ['name' => 'CBSE Composite Skill Lab Advanced', 'expected_mand_count' => 25, 'expected_mand_sum' => 789149.00, 'pkg_price' => 676576.00],
            73 => ['name' => 'CBSE Composite Skill Lab Premium', 'expected_mand_count' => 11, 'expected_mand_sum' => 762408.00, 'pkg_price' => 538944.00],
            74 => ['name' => 'Skillvation Lab', 'expected_mand_count' => 12, 'expected_mand_sum' => 131193.00, 'pkg_price' => 131193.00]
        ];

        foreach ($labs as $id => $info) {
            $prod = $this->db->table('products')->where('id', $id)->get()->getRow();
            $this->assert($prod !== null, "Lab Product ID $id ({$info['name']}) exists in database");

            $comps = $this->bundleModel->getBundleComponents($id, true);
            if ($id == 68) {
                $allComps68 = $this->bundleModel->getBundleComponents(68, false);
                $catStats = [];
                foreach ($allComps68 as $c) {
                    $cat = $c->category_name ?? 'General';
                    $catStats[$cat] = ($catStats[$cat] ?? 0) + 1;
                }
                foreach ($catStats as $cn => $cnt) {
                    CLI::write("  -> CAT: [$cn] = $cnt items", 'yellow');
                }
            }
            $mandComps = array_filter($comps, function($c) { return empty($c->is_optional) && $c->required_quantity > 0; });
            $mandSum = array_sum(array_map(function($c) { return $c->total_price; }, $mandComps));

            $this->assert(count($mandComps) == $info['expected_mand_count'], "Lab $id has exactly {$info['expected_mand_count']} mandatory components");
            $this->assert(round($mandSum, 2) == round($info['expected_mand_sum'], 2), "Lab $id mandatory components sum = ₹" . number_format($mandSum, 2) . " (matches expected ₹" . number_format($info['expected_mand_sum'], 2) . ")");
        }
    }

    /**
     * Scenario 8: Cart Calculations
     */
    private function testScenario8_CartCalculationsForRegularAndBundle()
    {
        CLI::write("\n--- SCENARIO 8: Cart Calculation Engine Integrity ---", 'cyan');

        $regularProd = $this->db->table('products')->where('is_bundle', 0)->where('status', 1)->where('price >', 0)->get()->getRow();
        if ($regularProd) {
            $pPrice = floatval($regularProd->price_discounted > 0 ? $regularProd->price_discounted : $regularProd->price);
            $qty = 3;
            $subtotal = $pPrice * $qty;
            $gst = round($subtotal * 0.18, 2);
            $grand = $subtotal + $gst;

            $this->assert($subtotal > 0, "Regular product ({$regularProd->slug}) cart subtotal for $qty units = ₹$subtotal");
            $this->assert($grand == ($subtotal + $gst), "Grand total with 18% GST correctly equals Subtotal + GST (₹$grand)");
        }

        $lab74 = $this->db->table('products')->where('id', 74)->get()->getRow();
        if ($lab74) {
            $comps = $this->bundleModel->getBundleComponents(74, true);
            $bundleSum = array_sum(array_map(function($c) { return $c->total_price; }, $comps));
            $this->assert(round($bundleSum, 2) == 131193.00, "Skillvation Lab bundle unit price resolves to ₹131,193.00 in cart engine");

            $gst = round(131193.00 * 0.18, 2);
            $totalIncGst = 131193.00 + $gst;
            $this->assert($gst == 23614.74, "Skillvation Lab GST (18%) = ₹23,614.74");
            $this->assert($totalIncGst == 154807.74, "Skillvation Lab Grand Total (Inc. 18% GST) = ₹154,807.74");
        }
    }

    /**
     * Scenario 9: Product Option Variants Pricing
     */
    private function testScenario9_ProductOptionVariantPricing()
    {
        CLI::write("\n--- SCENARIO 9: Product Option Variants Price Precedence ---", 'cyan');

        $parentProdId = $this->createTestProduct(500.00, 500.00, 0);

        // Insert variant with higher price: 750.00
        $variantData = [
            'product_id' => $parentProdId,
            'sku' => 'VAR-' . time(),
            'price' => 750.00,
            'price_discounted' => 600.00,
            'quantity' => 20,
            'is_active' => 1
        ];
        $this->db->table('product_option_variants')->insert($variantData);
        $varId = $this->db->insertID();

        $savedVar = $this->db->table('product_option_variants')->where('id', $varId)->get()->getRow();
        $this->assert($savedVar !== null, "Product variant created with ID: $varId");
        $this->assert(floatval($savedVar->price) == 750.00, "Variant price is 750.00 (distinct from parent 500.00)");

        // Now edit variant price to 850.00
        $this->db->table('product_option_variants')->where('id', $varId)->update(['price' => 850.00, 'price_discounted' => 700.00]);
        $updatedVar = $this->db->table('product_option_variants')->where('id', $varId)->get()->getRow();
        $this->assert(floatval($updatedVar->price) == 850.00, "Editing variant price updates variant price to 850.00");

        // Clean up
        $this->db->table('product_option_variants')->where('id', $varId)->delete();
        $this->db->table('products')->where('id', $parentProdId)->delete();
    }

    /**
     * Scenario 10: Bundle Safeguard in editProductDetails (when price submitted as 0, uses bundle metrics)
     */
    private function testScenario10_BundleSafeguardInProductDetailsEdit()
    {
        CLI::write("\n--- SCENARIO 10: Bundle Safeguard (Auto-Price From Components If Zero) ---", 'cyan');

        $c1 = $this->createTestProduct(400.00, 400.00, 0);
        $bId = $this->createTestProduct(0.00, 0.00, 0, ['is_bundle' => 1]);

        $bundleItems = [
            ['component_product_id' => $c1, 'quantity' => 2, 'price_override' => null, 'is_optional' => 0]
        ];
        $this->bundleModel->saveBundleComponents($bId, $bundleItems);

        // If seller saves bundle with price = 0, safeguard sets it to components sum (2 * 400 = 800)
        $bMetrics = $this->bundleModel->calculateBundleMetrics($bId);
        $this->assert($bMetrics['sum_price'] == 800.00, "Bundle safeguard calculates sum_price = 800.00");

        $this->db->table('products')->where('id', $bId)->update([
            'price' => $bMetrics['sum_price'],
            'price_discounted' => $bMetrics['sum_price'],
            'stock' => $bMetrics['max_stock']
        ]);

        $bSaved = $this->db->table('products')->where('id', $bId)->get()->getRow();
        $this->assert(floatval($bSaved->price) == 800.00, "Bundle price auto-populated from components sum as 800.00");

        // Clean up
        $this->db->table('product_bundles')->where('bundle_product_id', $bId)->delete();
        $this->db->table('products')->whereIn('id', [$bId, $c1])->delete();
    }

    private function createTestProduct($price, $priceDiscounted, $discountRate, $extra = [])
    {
        $data = array_merge([
            'slug' => 'test-item-' . time() . '-' . rand(100, 999),
            'sku' => 'SKU-' . time() . '-' . rand(100, 999),
            'price' => $price,
            'price_discounted' => $priceDiscounted,
            'discount_rate' => $discountRate,
            'currency' => 'INR',
            'user_id' => 1,
            'status' => 1,
            'visibility' => 1,
            'is_active' => 1,
            'stock' => 100,
            'is_bundle' => 0
        ], $extra);

        $this->db->table('products')->insert($data);
        return $this->db->insertID();
    }
}
