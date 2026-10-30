<?php

namespace App\Controllers;

use App\Libraries\DlocalGo;
use App\Libraries\Iyzico;
use App\Libraries\MercadoPago;
use App\Libraries\Midtrans;
use App\Libraries\PayTabs;
use App\Libraries\Razorpay;
use App\Libraries\Stripe;
use App\Models\CartModel;
use App\Models\CheckoutModel;
use App\Models\ProfileModel;
use App\Models\ShippingModel;
use App\Models\FileModel;

class CartController extends BaseController
{
    protected $cartModel;
    protected $checkoutModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->cartModel = new CartModel();
        $this->checkoutModel = new CheckoutModel();

        $router = service('router');
        $methodName = $router->methodName();
        if ($methodName !== 'payment') {
            helperSetSession('mds_cart_has_changed', 1);
        }
    }

    /**
     * Cart
     */
    public function cart()
    {
        $data = setPageMeta(trans("shopping_cart"));
        $data['isTranslatable'] = true;
        $data['cart'] = $this->cartModel->getCart();
        $data['userSession'] = getUserSession();

        helperDeleteSession('mds_service_payment');

        echo view('partials/_header', $data);
        echo view('cart/cart', $data);
        echo view('partials/_footer');
    }

    /**
     * Get all LAB plan product IDs
     */
    private function getLabPlanProductIds(): array
    {
        $labSlugs = [
            'cbse-composite-skill-lab-basic-68', 'cbse-composite-skill-lab-advanced-72', 'cbse-composite-skill-lab-premium-73',
            'basic-skill-1', 'advance-skill-1', 'premium-skill-1',
            'test-skill-1', 'basic-plan', 'advance-plan', 'premium-plan'
        ];

        $ids = [68, 72, 73, 1, 26, 27];

        try {
            $this->productModel->setBaseQuery(true);
            $products = $this->productModel->builder
                ->select('products.id')
                ->groupStart()
                    ->whereIn('products.slug', $labSlugs)
                    ->orWhereIn('products.id', [68, 72, 73, 1, 26, 27])
                ->groupEnd()
                ->where('products.is_deleted', 0)
                ->get()->getResult();

            if (!empty($products)) {
                foreach ($products as $p) {
                    $ids[] = (int)$p->id;
                }
            }
        } catch (\Throwable $e) {
            // Fallback to default IDs
        }

        return array_values(array_unique($ids));
    }

    /**
     * Ensure only the selected LAB product is in cart as the lab bundle,
     * removing any other conflicting LAB plans, but retaining all individual/other products.
     */
    private function syncSelectedLabCart($selectedProduct)
    {
        if (empty($selectedProduct) || $selectedProduct->status != 1) {
            return;
        }

        $selectedId = (int)$selectedProduct->id;
        $allLabIds = $this->getLabPlanProductIds();
        $otherLabIds = array_diff($allLabIds, [$selectedId]);

        $existingCart = $this->cartModel->getCart();
        $isSelectedInCart = false;

        if (!empty($existingCart) && !empty($existingCart->items)) {
            foreach ($existingCart->items as $item) {
                $itemPid = (int)$item->product_id;
                // If it's another LAB plan, remove it so user only has the selected LAB
                if (in_array($itemPid, $otherLabIds, true)) {
                    $this->cartModel->removeCartItem($item->id);
                } elseif ($itemPid === $selectedId) {
                    if (!$isSelectedInCart) {
                        $isSelectedInCart = true;
                    } else {
                        $this->cartModel->removeCartItem($item->id);
                    }
                }
                // Note: non-LAB products (accessories, extra items, consumables) are left intact!
            }
        }

        // Guarantee presence of selected LAB product in cart
        if (!$isSelectedInCart) {
            $this->cartModel->addToCart($selectedProduct, 1);
        }
    }

    /**
     * Basic Cart
     */
    public function basicCart()
    {
        $product = $this->productModel->getProductBySlug('cbse-composite-skill-lab-basic-68');
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(68);
        }
        if (empty($product)) {
            $product = $this->productModel->getProductBySlug('basic-skill-1');
        }
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(1);
        }

        $this->syncSelectedLabCart($product);

        $data = setPageMeta('Basic Plan — Cart');
        $data['isTranslatable'] = true;
        $data['cart'] = $this->cartModel->getCart();
        $data['userSession'] = getUserSession();
        $data['planKey'] = 'basic';
        $data['planLabel'] = 'Basic';
        $data['planDefaultProductId'] = !empty($product) ? (int)$product->id : 68;

        helperDeleteSession('mds_service_payment');

        echo view('partials/_header', $data);
        echo view('cart/basic-cart', $data);
        echo view('partials/_footer');
    }

    /**
     * Design My Lab: load a Kalpana quotation into the matching plan cart.
     * The quotation is fetched server-side by its (unguessable) session ID, so the browser cannot alter
     * the items; prices always come from this shop. Lab components become the bundle's quantities,
     * other shop products are separate cart lines, and MySkool courses are listed as not sold here.
     * The lab matches the quotation exactly: the bundle's usual minimums are not applied to it.
     */
    public function labDesignCart()
    {
        $sessionId = (string)inputGet('session');
        if (!preg_match('/^[0-9a-f-]{36}$/', $sessionId)) {
            return redirect()->to(base_url('basic-cart'));
        }
        $plans = [
            'basic'    => ['cart' => 'basic-cart',   'slug' => 'cbse-composite-skill-lab-basic-68',    'id' => 68],
            'advanced' => ['cart' => 'advance-cart', 'slug' => 'cbse-composite-skill-lab-advanced-72', 'id' => 72],
            'premium'  => ['cart' => 'premium-cart', 'slug' => 'cbse-composite-skill-lab-premium-73',  'id' => 73],
        ];
        // Reloading (or the popup's redirect racing the "Go to cart now" click) must not add the quotation twice,
        // but the notice is shown again.
        // A budget option chosen in Kalpana continues straight to checkout.
        $checkout = inputGet('next') === 'checkout';
        $applied = helperGetSession('lab_design_applied') ?: [];
        if (isset($applied[$sessionId]['cart'])) {
            session()->setFlashdata('lab_design_notice', $applied[$sessionId]['notice']);
            return redirect()->to($checkout ? generateUrl('cart', 'payment_method') : base_url($applied[$sessionId]['cart']));
        }

        $baseUrl = rtrim((string)env('kalpana.baseURL'), '/');
        $apiKey = (string)env('kalpana.apiKey');
        $data = null;
        if ($baseUrl !== '' && $apiKey !== '') {
            try {
                $response = \Config\Services::curlrequest(['timeout' => 20, 'http_errors' => false])
                    ->get($baseUrl . '/api/v1/sessions/' . rawurlencode($sessionId) . '/result', ['headers' => ['Authorization' => 'Bearer ' . $apiKey, 'Accept' => 'application/json']]);
                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                }
            } catch (\Throwable $e) {
                log_message('error', '[LabDesignCart] ' . $e->getMessage());
            }
        }
        if (empty($data) || ($data['status'] ?? '') !== 'ready' || empty($data['quotation']['lines'])) {
            session()->setFlashdata('lab_design_notice', 'Your designed lab could not be loaded. Please try Design My Lab again.');
            return redirect()->to(base_url('basic-cart'));
        }

        $plan = $plans[strtolower((string)($data['plan']['tier'] ?? ''))] ?? $plans['basic'];
        $labProduct = $this->productModel->getProductBySlug($plan['slug']) ?: $this->productModel->getActiveProduct($plan['id']);
        // The cart becomes exactly the reviewed Kalpana design: items from an earlier design or plan are removed.
        foreach ($this->cartModel->getCart()->items ?? [] as $item) {
            $this->cartModel->removeCartItem($item->id);
        }
        $this->syncSelectedLabCart($labProduct);

        $quantities = [];
        $courses = [];
        foreach ($data['quotation']['lines'] as $line) {
            $qty = max(0, min(99999, (int)($line['quantity'] ?? 0)));
            if (preg_match('/^sv-(\d+)$/', (string)($line['productId'] ?? ''), $m)) {
                $quantities[(int)$m[1]] = ($quantities[(int)$m[1]] ?? 0) + $qty;
            } elseif (($line['kind'] ?? '') === 'course') {
                $courses[] = (string)($line['name'] ?? 'Course');
            }
        }

        // Bundle components take the quotation's quantities; components it did not choose are set to zero,
        // including required ones (the "designed" marker makes the cart keep these quantities).
        $bundleItems = [];
        $components = !empty($labProduct) ? (new \App\Models\BundleModel())->getBundleComponents((int)$labProduct->id, true) : [];
        foreach ($components as $component) {
            $pid = (int)$component->component_product_id;
            $bundleItems[] = ['comp_id' => (int)$component->id, 'product_id' => $pid, 'variant_id' => $component->variant_id, 'qty' => $quantities[$pid] ?? 0, 'designed' => 1];
            unset($quantities[$pid]);
        }
        if (!empty($bundleItems)) {
            $labItemId = null;
            foreach ($this->cartModel->getCart()->items ?? [] as $item) {
                if ((int)$item->product_id === (int)$labProduct->id) {
                    $labItemId = $item->id;
                    break;
                }
            }
            $this->cartModel->addToCart($labProduct, 1, null, [], $bundleItems, $labItemId);
        }

        $skipped = [];
        foreach ($quantities as $pid => $qty) {
            $product = $this->productModel->getActiveProduct($pid);
            if (!empty($product) && $product->status == 1 && $qty > 0) {
                $this->cartModel->addToCart($product, $qty);
            } elseif ($qty > 0) {
                $skipped[] = '#' . $pid;
            }
        }

        $notice = 'Your designed lab from Kalpana has been added to the cart.';
        if (!empty($courses)) {
            $notice .= ' Courses are not sold in the shop and were not added: ' . implode(', ', $courses) . '.';
        }
        if (!empty($skipped)) {
            $notice .= ' Unavailable products were skipped: ' . implode(', ', $skipped) . '.';
        }
        $applied[$sessionId] = ['cart' => $plan['cart'], 'notice' => $notice];
        helperSetSession('lab_design_applied', $applied);
        session()->setFlashdata('lab_design_notice', $notice);
        return redirect()->to($checkout ? generateUrl('cart', 'payment_method') : base_url($plan['cart']));
    }

    /**
     * Advance Cart
     */
    public function advanceCart()
    {
        $product = $this->productModel->getProductBySlug('cbse-composite-skill-lab-advanced-72');
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(72);
        }
        if (empty($product)) {
            $product = $this->productModel->getProductBySlug('advance-skill-1');
        }
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(26);
        }

        $this->syncSelectedLabCart($product);

        $data = setPageMeta('Advance Plan — Cart');
        $data['isTranslatable'] = true;
        $data['cart'] = $this->cartModel->getCart();
        $data['userSession'] = getUserSession();
        $data['planKey'] = 'advance';
        $data['planLabel'] = 'Advance';
        $data['planDefaultProductId'] = !empty($product) ? (int)$product->id : 72;
        helperDeleteSession('mds_service_payment');

        echo view('partials/_header', $data);
        echo view('cart/advance-cart', $data);
        echo view('partials/_footer');
    }

    /**
     * Premium Cart
     */
    public function premiumCart()
    {
        $product = $this->productModel->getProductBySlug('cbse-composite-skill-lab-premium-73');
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(73);
        }
        if (empty($product)) {
            $product = $this->productModel->getProductBySlug('premium-skill-1');
        }
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(27);
        }

        $this->syncSelectedLabCart($product);

        $data = setPageMeta('Premium Plan — Cart');
        $data['isTranslatable'] = true;
        $data['cart'] = $this->cartModel->getCart();
        $data['userSession'] = getUserSession();
        $data['planKey'] = 'premium';
        $data['planLabel'] = 'Premium';
        $data['planDefaultProductId'] = !empty($product) ? (int)$product->id : 73;
        helperDeleteSession('mds_service_payment');

        echo view('partials/_header', $data);
        echo view('cart/premium-cart', $data);
        echo view('partials/_footer');
    }

    /**
     * Skillvations Cart
     */
    public function skilllabCart()
    {
        $product = $this->productModel->getProductBySlug('skillvation-lab');
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(74);
        }
        if (empty($product)) {
            $product = $this->productModel->getProductBySlug('skillvation-lab');
        }
        if (empty($product)) {
            $product = $this->productModel->getActiveProduct(1);
        }

        $this->syncSelectedLabCart($product);

        $data = setPageMeta('Sillvation Plan — Cart');
        $data['isTranslatable'] = true;
        $data['cart'] = $this->cartModel->getCart();
        $data['userSession'] = getUserSession();
        $data['planKey'] = 'skilllab';
        $data['planLabel'] = 'Skillvation';
        $data['planDefaultProductId'] = !empty($product) ? (int)$product->id : 74;

        helperDeleteSession('mds_service_payment');

        echo view('partials/_header', $data);
        echo view('cart/skillvation-cart', $data);
        echo view('partials/_footer');
    }

    /**
     * Add to Cart Bundle
     */
    // public function addToCartBundle()
    // {
        // echo 'onee';
        // exit();
        // $items = json_decode(file_get_contents("php://input"), true);
        // foreach($items['items'] as $row){
        //     $product = $this->productModel->getActiveProduct($row['product_id']);
        //     if(!empty($product)){
        //         $this->cartModel->addToCart($product,1,null,null);
        //     }
        // }
        // return jsonResponse(['result'=>1]);
    // }
    public function addToCartBundlecc()
    {
        $raw = json_decode($this->request->getPost('cart_items'), true);
        // print_r($raw);
        // exit();
        $bundleTotal = 0;
        foreach($raw['items'] as $i){
            $p = $this->productModel->getActiveProduct($i['product_id']);
            // productModel->getActiveProduct($productId);
            $price = ($p->price_discounted > 0 ? $p->price_discounted : $p->price);
            $bundleTotal += $price * $i['qty'];
        }
        // print_r($raw);
        //         exit();
        $virtual = (object)[
            'id' => 0,
            'title' => 'Custom Learning Bundle',
            'price' => $bundleTotal,
            'price_discounted' => $bundleTotal,
            'slug' => 'custom-bundle',
            'user_id' => 0,
            'product_type' => 'physical'
        ];

        $this->cartModel->addBundleAsSingle($virtual, json_encode($raw['items']));

        return jsonResponse(['result'=>1]);
    }
    public function addToCartBundlebb()
    {
        $raw = json_decode($this->request->getPost('cart_items'), true);

        $bundleTotal = 0;
        foreach($raw['items'] as $i){
            $p = $this->productModel->getActiveProduct($i['product_id']);
            $price = ($p->price_discounted > 0 ? $p->price_discounted : $p->price);
            $bundleTotal += $price * $i['qty'];
        }
        

        $virtual = (object)[
            // 'id' => 0,
            'title' => 'Custom Learning Bundle',
            'price' => $bundleTotal,
            'price_discounted' => $bundleTotal,
            'slug' => 'custom-bundle',
            'user_id' => 0,
            'product_type' => 'physical'
        ];
        print_r($virtual);
        exit();

        // INSERT and capture the return value
        $insertId = $this->cartModel->addBundleAsSingle($virtual, json_encode($raw['items']));

        // DEBUG LOG
        if($insertId){
            log_message('info', "Bundle added successfully with ID: {$insertId}");
        } else {
            log_message('error', "Failed to add bundle to cart_items table");
        }

        // OPTIONAL: temporary response to browser for debugging
        return jsonResponse([
            'result' => $insertId ? 1 : 0,
            'insert_id' => $insertId,
            'bundle_total' => $bundleTotal
        ]);
    }
    public function addToCartBundlejan13()
    {
        $raw = json_decode($this->request->getPost('cart_items'), true);

        if (empty($raw['items'])) {
            return jsonResponse(['result' => 0]);
        }

        $bundleTotal = 0;
        $mainProductId = null;
        foreach ($raw['items'] as $i) {

            if (isset($i['main_product_id'])) {
                $mainProductId = $i['main_product_id'];
                $pid = $mainProductId;
            } else {
                $pid = $i['product_id'];
            }

            $p = $this->productModel->getActiveProduct($pid);
            if (!$p) continue;

            $price = ($p->price_discounted > 0 ? $p->price_discounted : $p->price);
            $bundleTotal += $price * $i['qty'];
        }

        // create or get cart
        $cart = $this->cartModel->fetchRawCartData();
        $cartId = $cart->id ?? $this->cartModel->createCart();

        // Virtual bundle product row
        $bundleRow = [
            'cart_id'       => $cartId,
            'item_hash'     => md5('bundle-' . $cartId),
            'product_id'    => $mainProductId,
            'product_title' => 'Custom Learning Bundle',
            'quantity'      => 1,
            'purchase_type' => 'product',
            'quote_request_id' => 0,
            'unit_price'    => $bundleTotal,
            'total_price'   => $bundleTotal,
            'is_bundle'     => 1,
            'bundle_items'  => json_encode($raw['items']),   // 👈 all products inside ONE row
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $insertId = $this->cartModel->insertBundleRow($bundleRow);

        return jsonResponse([
            'result' => $insertId ? 1 : 0,
            'bundle_id' => $insertId
        ]);
    }
    public function addToCartBundlett()
    {
        $raw = json_decode($this->request->getPost('cart_items'), true);

        if (empty($raw['items'])) {
            return jsonResponse(['result' => 0]);
        }

        $bundleTotal = 0;
        $mainProductId = null;

        foreach ($raw['items'] as $i) {
            $pid = $i['main_product_id'] ?? $i['product_id'];
            $mainProductId = $pid;

            $p = $this->productModel->getActiveProduct($pid);
            if (!$p) continue;

            $price = $p->price_discounted > 0 ? $p->price_discounted : $p->price;
            $bundleTotal += $price * $i['qty'];
        }

        $cart = $this->cartModel->fetchRawCartData();
        $cartId = $cart->id ?? $this->cartModel->createCart();

        $hash = md5('bundle-' . md5(json_encode($raw['items'])));

        $pMain = $this->productModel->getActiveProduct($mainProductId);
        echo '<pre>';
        print_r($pMain);
        exit();
        if (!$pMain) return jsonResponse(['result' => 0]);

        // prevent duplicates
        $existing = $this->cartModel->getItemByHash($hash, $cartId);

        if ($existing) {
            $newQty = $existing->quantity + 1;

            $this->cartModel->update($existing->id, [
                'quantity' => $newQty,
                'total_price' => $existing->unit_price * $newQty
            ]);

            return jsonResponse([
                'result' => 1,
                'bundle_id' => $existing->id
            ]);
        }


        $fileModel = new FileModel();
        $mainImage = $fileModel->getProductMainImage($pMain->id);

        $imageId = ''; $imageData='';
        if ($mainImage) {
            $imageId = $mainImage->id;
            $imageData = safeJsonEncode([
                'path'=>$mainImage->image_small,
                'storage'=>$mainImage->storage
            ]);
        }

        $bundleRow = [
            'cart_id'=>$cartId,
            'item_hash'=>$hash,
            'product_id'=>$pMain->id,
            'product_title'=>$pMain->title,
            'product_type'=>'bundle',
            'listing_type'=>'bundle',
            // 'product_url'=>generateProductUrl($pMain),
            'product_sku'=>$pMain->sku.'-BUNDLE',
            'quantity'=>1,
            'purchase_type'=>'product',
            'quote_request_id'=>0,
            'unit_price'=>$bundleTotal,
            'unit_price_base'=>$bundleTotal,
            'total_price'=>$bundleTotal,
            'is_bundle'=>1,
            'bundle_items'=>json_encode($raw['items']),
            'seller_id'=>$pMain->user_id,
            // 'seller_info'=>$pMain->user_username.'|||'.$pMain->user_slug,
            'product_image_id'=>$imageId,
            'product_image_data'=>$imageData,
            // 'chargeable_weight'=>0,
            'product_vat'=>0,
            'product_vat_rate'=>0,
            'is_stock_available'=>1,
            'created_at'=>date('Y-m-d H:i:s')
        ];



        $insertId = $this->cartModel->insertBundleRow($bundleRow);

        return jsonResponse([
            'result' => $insertId ? 1 : 0,
            'bundle_id' => $insertId
        ]);
    }
    public function addToCartBundle()
    {
        $raw = json_decode($this->request->getPost('cart_items'), true);
        if (empty($raw['items'])) return jsonResponse(['result' => 0]);

        $mainItem = null;
        foreach ($raw['items'] as $item) {
            if (isset($item['main_product_id'])) {
                $mainItem = $item;
                break;
            }
        }
        if (!$mainItem) {
            $mainItem = $raw['items'][0];
        }

        $mainProductId = (int)($mainItem['main_product_id'] ?? $mainItem['product_id']);
        $mainProductqty = max(1, (int)($mainItem['qty'] ?? 1));
        $pMain = $this->productModel->getActiveProduct($mainProductId);
        if (!$pMain) return jsonResponse(['result' => 0]);

        $bundleItems = [];
        $bundleTotal = 0.0;

        foreach ($raw['items'] as $i) {
            $pid = (int)$i['product_id'];
            $qty = (int)$i['qty'];
            if ($qty <= 0) continue;

            $p = $this->productModel->getActiveProduct($pid);
            if (!$p) continue;

            $price = (float)(!empty($p->price_discounted) && $p->price_discounted > 0 ? $p->price_discounted : $p->price);
            $bundleTotal += ($price * $qty);

            $bundleItems[] = [
                'product_id' => $pid,
                'qty'        => $qty,
                'unit_price' => $price,
                'is_main'    => isset($i['main_product_id']) ? 1 : 0
            ];
        }

        $cart = $this->cartModel->fetchRawCartData();
        $cartId = $cart->id ?? $this->cartModel->createCart();

        $hash = md5('bundle-' . md5(json_encode($bundleItems)));

        $existing = $this->cartModel->getItemByHash($hash, $cartId);
        if ($existing) {
            $newQty = $existing->quantity + 1;
            $this->cartModel->updateItem($existing->id, [
                'quantity'    => $newQty,
                'total_price' => $existing->unit_price * $newQty
            ]);
            return jsonResponse(['result' => 1, 'bundle_id' => $existing->id]);
        }

        $fileModel = new FileModel();
        $mainImage = $fileModel->getProductMainImage($pMain->id);

        $imageId = ''; $imageData = '';
        if ($mainImage) {
            $imageId = $mainImage->id;
            $imageData = safeJsonEncode([
                'path' => $mainImage->image_small,
                'storage' => $mainImage->storage
            ]);
        }

        $bundleRow = [
            'cart_id'            => $cartId,
            'item_hash'          => $hash,
            'product_id'         => $pMain->id,
            'product_title'      => $pMain->title,
            'product_type'       => $pMain->product_type,
            'listing_type'       => $pMain->listing_type,
            'product_sku'        => $pMain->sku . '-BUNDLE',
            'quantity'           => $mainProductqty,
            'purchase_type'      => 'product',
            'quote_request_id'   => 0,
            'unit_price'         => $bundleTotal,
            'unit_price_base'    => $bundleTotal,
            'total_price'        => $bundleTotal * $mainProductqty,
            'is_bundle'          => 1,
            'bundle_items'       => json_encode($bundleItems),
            'seller_id'          => $pMain->user_id,
            'product_image_id'   => $imageId,
            'product_image_data' => $imageData,
            'product_vat'        => 0,
            'product_vat_rate'   => 0,
            'is_stock_available' => 1,
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $insertId = $this->cartModel->insertBundleRow($bundleRow);

        return jsonResponse(['result' => $insertId ? 1 : 0, 'bundle_id' => $insertId]);
    }
    /**
     * Add to Cart using Barcode / SKU
     */
    public function addToCartByBarcode()
    {
        $sku = inputPost('sku');
        $data = ['result' => 0];

        if (empty($sku)) {
            return jsonResponse($data);
        }

        // Find product by SKU
        $product = $this->productModel
            ->where('sku', $sku)
            ->where('status', 1)
            ->get()
            ->getRow();

        if (empty($product)) {
            return jsonResponse([
                'result' => 0,
                'message' => 'Product not found'
            ]);
        }

        // Default values for barcode scan
        $_POST['product_id'] = $product->id;
        $_POST['product_quantity'] = 1;
        $_POST['variant_id'] = null;
        $_POST['extra_options'] = null;

        // ✅ Call SAME addToCart logic
        return $this->addToCart();
    }


    /**
     * Add to Cart
     */
    public function addToCart()
    {
        helperDeleteSession('plan_removed_basic');
        helperDeleteSession('plan_removed_advance');
        helperDeleteSession('plan_removed_premium');

        $productId = inputPost('product_id');
        $quantity = (int)inputPost('product_quantity');
        $variantId = inputPost('variant_id');
        $extraOptions = inputPost('extra_options');
        $bundleComponentsJson = inputPost('bundle_components_data');
        $cartItemId = inputPost('cart_item_id');
        $product = $this->productModel->getActiveProduct($productId);
        $data = ['result' => 0];

        if (!empty($product) && $product->status == 1) {
            $labPlanIds = $this->getLabPlanProductIds();
            if (empty($cartItemId) && in_array((int)$product->id, $labPlanIds, true)) {
                // When adding a new LAB plan, remove other conflicting LAB plans from the cart
                $existingCart = $this->cartModel->getCart();
                if (!empty($existingCart) && !empty($existingCart->items)) {
                    foreach ($existingCart->items as $item) {
                        $itemPid = (int)$item->product_id;
                        if ($itemPid !== (int)$product->id && in_array($itemPid, $labPlanIds, true)) {
                            $this->cartModel->removeCartItem($item->id);
                        }
                    }
                }
            }

            $cartItemId = $this->cartModel->addToCart($product, $quantity, $variantId, $extraOptions, $bundleComponentsJson, $cartItemId);
            if (!empty($cartItemId)) {
                $cart = $this->cartModel->getCart();
                $cartItem = $this->cartModel->getCartItem($cartItemId);
                if (empty($cart) || empty($cartItem)) {
                    return jsonResponse(['result' => 0]);
                }
                $cartHasPhysicalProduct = !empty($cart) && !empty($cart->has_physical_product) ? true : false;
                $relatedProducts = $this->productModel->getRelatedProducts($product->id, $product->category_id);
                $data = [
                    'result' => 1,
                    'is_update' => !empty(inputPost('cart_item_id')) ? 1 : 0,
                    'return_url' => inputPost('return_url') ?: generateUrl('cart'),
                    'productCount' => $cart->num_items ?? 1,
                    'htmlCartProduct' => view('cart/_modal_cart_product', ['cartItem' => $cartItem, 'product' => $product, 'relatedProducts' => $relatedProducts, 'cartHasPhysicalProduct' => $cartHasPhysicalProduct])
                ];
            }
        }
        return jsonResponse($data);
    }

    /**
     * Add to Cart qQuote
     */
    public function addToCartQuote()
    {
        $quoteRequestId = inputPost('id');
        if (!empty($this->cartModel->addToCartQuote($quoteRequestId))) {
            return redirect()->to(generateUrl('cart'));
        }
        return redirect()->back();
    }

    /**
     * Remove from Cart
     */
    public function removeFromCart()
    {
        $cartItemId = inputPost('cart_item_id');
        $this->cartModel->removeCartItem($cartItemId);
        helperSetSession('plan_removed_basic', 1);
        helperSetSession('plan_removed_advance', 1);
        helperSetSession('plan_removed_premium', 1);
        return jsonResponse();
    }

    /**
     * Remove Cart Discount Coupon
     */
    public function removeCartDiscountCoupon()
    {
        $cartRaw = $this->cartModel->fetchRawCartData();
        if (!empty($cartRaw)) {
            $this->cartModel->removeCoupon($cartRaw->id);
        }

        return jsonResponse();
    }

    /**
     * Update Cart Item Quantity
     */
    public function updateCartItemQuantity()
    {
        $cartItemId = (int)inputPost('cart_item_id');
        $quantity = max(1, min(99999, (int)clrNum(inputPost('quantity'))));
        $this->cartModel->updateItemQuantity($cartItemId, $quantity);
        return jsonResponse();
    }

    /**
     * Coupon Code Post
     */
    public function couponCodePost()
    {
        $couponCode = inputPost('coupon_code');
        $this->cartModel->applyCoupon($couponCode);
        return redirect()->to(generateUrl('cart'));
    }

    /**
     * Shipping
     */
    public function shipping()
    {
        $data = setPageMeta(trans("shopping_cart"));
        $data['isTranslatable'] = true;

        helperDeleteSession('mds_service_payment');

        $profileModel = new ProfileModel();
        $shippingModel = new ShippingModel();

        $cart = $this->cartModel->getCart();
        if (empty($cart) || !$cart->is_valid) {
            return redirect()->to(generateUrl('cart'));
        }

        //check shipping status
        if ($this->productSettings->marketplace_shipping != 1) {
            return redirect()->to(generateUrl('cart'));
        }
        //check guest checkout
        if (empty(authCheck()) && $this->generalSettings->guest_checkout != 1) {
            return redirect()->to(generateUrl('cart'));
        }

        //check auth for digital products
        if (!authCheck() && $cart->has_digital_product == true) {
            setErrorMessage(trans("msg_digital_product_register_error"));
            return redirect()->to(generateUrl('register'));
        }

        //check physical products
        if ($cart->has_physical_product == false) {
            return redirect()->to(generateUrl('cart'));
        }

        $data['cartShippingData'] = !empty($cart->shipping_data) ? json_decode($cart->shipping_data) : null;
        $data['shippingAddresses'] = array();
        $data['selectedShippingAddressId'] = 0;
        $data['selectedBillingAddressId'] = 0;
        $data['selectedSameAddressForBilling'] = 1;
        $stateId = null;
        if (!empty($data['cartShippingData'])) {
            if (!empty($data['cartShippingData']->shippingStateId)) {
                $stateId = $data['cartShippingData']->shippingStateId;
            }
            if (!empty($data['cartShippingData']->shippingAddressId)) {
                $data['selectedShippingAddressId'] = $data['cartShippingData']->shippingAddressId;
            }
            if (!empty($data['cartShippingData']->billingAddressId)) {
                $data['selectedBillingAddressId'] = $data['cartShippingData']->billingAddressId;
            }
            if (isset($data['cartShippingData']->useSameAddressForBilling)) {
                $data['selectedSameAddressForBilling'] = $data['cartShippingData']->useSameAddressForBilling;
            }
        }

        if (authCheck()) {
            $data['shippingAddresses'] = $profileModel->getShippingAddresses(user()->id);
            $isCorrectShippingAddress = false;
            $isCorrectBillingAddress = false;
            if (!empty($data['shippingAddresses'])) {
                foreach ($data['shippingAddresses'] as $item) {
                    if ($data['selectedShippingAddressId'] == $item->id && $item->address_type == 'shipping') {
                        $isCorrectShippingAddress = true;
                    }
                    if ($data['selectedBillingAddressId'] == $item->id && $item->address_type == 'billing') {
                        $isCorrectBillingAddress = true;
                    }
                }
            }
            //reset selected addresses
            if ($isCorrectShippingAddress == false || $isCorrectBillingAddress == false) {
                $data['selectedShippingAddressId'] = 0;
                $data['selectedBillingAddressId'] = 0;
                $stateId = null;
            }
            if (empty($data['selectedShippingAddressId']) || empty($stateId)) {
                $address = $profileModel->getFirstShippingAddress(user()->id, 'shipping');
                if (!empty($address)) {
                    $data['selectedShippingAddressId'] = $address->id;
                    $stateId = $address->state_id;
                }
            }
            if (empty($data['selectedBillingAddressId'])) {
                $addressBilling = $profileModel->getFirstShippingAddress(user()->id, 'billing');
                if (!empty($addressBilling)) {
                    $data['selectedBillingAddressId'] = $addressBilling->id;
                }
            }
        }
        $data['stateId'] = null;
        if (!empty($stateId)) {
            $data['stateId'] = $stateId;
            $data['shippingMethods'] = $shippingModel->getSellerShippingMethodsArray($cart->items, $stateId, $cart->currency_code);
        }
        $data['selectedShippingMethodIds'] = [];
        if (!empty(helperGetSession('mds_selected_shipping_methods'))) {
            $data['selectedShippingMethodIds'] = helperGetSession('mds_selected_shipping_methods');
        }
        //cart seller ids
        $data['cartSellerIds'] = null;
        if (!empty(helperGetSession('mds_array_cart_seller_ids'))) {
            $data['cartSellerIds'] = helperGetSession('mds_array_cart_seller_ids');
        }

        // Express Checkout: Pass active payment methods and gateways
        $data['gateways'] = getActivePaymentGateways();
        $data['paymentSettings'] = $this->paymentSettings;
        $data['vendorCashOnDelivery'] = 0;
        foreach ($cart->items as $item) {
            $vendor = getUser($item->seller_id);
            if (!empty($vendor) && $vendor->cash_on_delivery == 1 && $vendor->commission_debt < $this->paymentSettings->cash_on_delivery_debt_limit) {
                $data['vendorCashOnDelivery'] = 1;
            }
        }
        $data['selectedPaymentOption'] = !empty($cart->payment_method) ? $cart->payment_method : 'razorpay';
        $locationModel = new \App\Models\LocationModel();
        $data['activeCountries'] = $locationModel->getActiveCountries();

        $data['cart'] = $cart;

        echo view('partials/_header', $data);
        if (authCheck()) {
            echo view('cart/shipping_information', $data);
        } else {
            echo view('cart/shipping_information_guest', $data);
        }
        echo view('partials/_footer');
    }

    /**
     * Shipping Post
     */
    public function shippingPost()
    {
        $cartRaw = $this->cartModel->fetchRawCartData();
        // echo '<pre>';
        // print_r($cartRaw);
        // exit();
        if (empty($cartRaw)) {
            setErrorMessage(trans("msg_error"));
            return redirect()->to(generateUrl('cart', 'shipping'));
        }

        $this->cartModel->setShippingData($cartRaw);

        if (!$this->cartModel->setShippingCost($cartRaw)) {
            setErrorMessage(trans("msg_error"));
            return redirect()->to(generateUrl('cart', 'shipping'));
        }

        helperDeleteSession('mds_service_payment');

        return redirect()->to(generateUrl('cart', 'payment_method'));
    }

    /**
     * Payment Method
     */
    public function paymentMethod()
    {
        // echo '22';
        // exit();
        $data = setPageMeta(trans("shopping_cart"));
        $data['isTranslatable'] = true;

        $servicePayment = helperGetSession('mds_service_payment');

        $checkoutType = !empty($servicePayment) ? 'service' : 'product';

        $showLocationSelection = $this->paymentSettings->cart_location_selection;

        $payWithBalance = new \stdClass();
        $payWithBalance->total = 0;
        $payWithBalance->currency = 'USD';

        if ($checkoutType == 'product') {

            //validate cart
            $cart = $this->cartModel->getCart(true, false);
            if (empty($cart) || empty($cart->is_valid)) {
                return redirect()->to(generateUrl('cart'));
            }

            //check auth for digital products
            if (!authCheck() && $cart->has_digital_product == true) {
                setErrorMessage(trans("msg_digital_product_register_error"));
                return redirect()->to(generateUrl('cart'));
            }

            //show location selection if it is required
            if ($showLocationSelection == true && !empty($cart->location_country_id) && !empty($cart->location_state_id)) {
                $showLocationSelection = false;
            }

            //reset previous payment method
            $this->cartModel->setCartPaymentMethod($cart->id, '');

            $data['vendorCashOnDelivery'] = 0;
            foreach ($cart->items as $item) {
                $vendor = getUser($item->seller_id);
                if (!empty($vendor)) {
                    if ($vendor->cash_on_delivery == 1 && $vendor->commission_debt < $this->paymentSettings->cash_on_delivery_debt_limit) {
                        $data['vendorCashOnDelivery'] = 1;
                    }
                }
            }

            if (!empty($cart->totals) && !empty($cart->totals->total)) {
                $payWithBalance->total = $cart->totals->total;
                $payWithBalance->currency = $cart->currency_code;
            }

            // Express Checkout: Pass active payment methods and gateways
        $data['gateways'] = getActivePaymentGateways();
        $data['paymentSettings'] = $this->paymentSettings;
        $data['vendorCashOnDelivery'] = 0;
        foreach ($cart->items as $item) {
            $vendor = getUser($item->seller_id);
            if (!empty($vendor) && $vendor->cash_on_delivery == 1 && $vendor->commission_debt < $this->paymentSettings->cash_on_delivery_debt_limit) {
                $data['vendorCashOnDelivery'] = 1;
            }
        }
        $data['selectedPaymentOption'] = !empty($cart->payment_method) ? $cart->payment_method : 'razorpay';
        $locationModel = new \App\Models\LocationModel();
        $data['activeCountries'] = $locationModel->getActiveCountries();

        $data['cart'] = $cart;

        } elseif ($checkoutType == 'service') {
            $data['servicePayment'] = $servicePayment;
            $servicePayment->globalTaxesArray = [];

            //do not show location selection for deposit
            if ($showLocationSelection == true && $servicePayment->serviceType === 'add_funds') {
                $showLocationSelection = false;
            }

            if (!empty($servicePayment->subtotal) && $servicePayment->serviceType != 'add_funds') {

                $location = (object)[
                    'country_id' => authCheck() ? user()->country_id : null,
                    'state_id' => authCheck() ? user()->state_id : null
                ];

                //show location selection if it is required
                if ($showLocationSelection == true && !empty($location->country_id) && !empty($location->state_id)) {
                    $showLocationSelection = false;
                }

                if (!empty($location->country_id) && !empty($location->state_id)) {
                    $servicePayment = $this->cartModel->setServicePaymentsTaxes($servicePayment, $location);
                }

                $payWithBalance->total = $servicePayment->grandTotal;
                $payWithBalance->currencyCode = $servicePayment->currencyCode;
            }
        }

        $data['payWithBalance'] = $payWithBalance;
        $data['checkoutType'] = $checkoutType;
        $data['showLocationSelection'] = $showLocationSelection;

        echo view('partials/_header', $data);
        echo view('cart/payment_method', $data);
        echo view('partials/_footer');
    }

    /**
     * Handle payment method selection, validate it, set on cart, and create checkout
     */
    public function paymentMethodPost()
    {
        // echo '23';
        // exit();
        //bot verification
        if (!authCheck()) {
            verifyTurnstile();
        }

        $servicePayment = helperGetSession('mds_service_payment');
        $cart = $this->cartModel->getCart();

        //set cart location if location form submit
        $submit = inputPost('submit');
        if ($submit === 'location') {
            $countryId = inputPost('country_id');
            $stateId = inputPost('state_id');
            $this->cartModel->setCartLocation($cart, $countryId, $stateId);

            return redirect()->to(generateUrl('cart', 'payment_method'));
        }

        if (!empty($servicePayment)) {

            $availableMethods = $this->getAvailablePaymentMethods(null, true);
            $selectedPaymentOption = inputPost('payment_option');

            //validate selected payment method
            if (!in_array($selectedPaymentOption, $availableMethods)) {
                setErrorMessage(trans("msg_error"));
                return redirect()->to(generateUrl('cart', 'payment_method'));
            }

            //set payment method
            $servicePayment->payment_method = $selectedPaymentOption;
            helperSetSession('mds_service_payment', $servicePayment);

        } else {

            //validate cart
            if (empty($cart) || !$cart->is_valid) {
                return redirect()->to(generateUrl('cart'));
            }

            $availableMethods = $this->getAvailablePaymentMethods($cart);
            $selectedPaymentOption = inputPost('payment_option');

            //validate selected payment method
            if (!in_array($selectedPaymentOption, $availableMethods)) {
                setErrorMessage(trans("msg_error"));
                return redirect()->to(generateUrl('cart', 'payment_method'));
            }

            //set payment method
            $this->cartModel->setCartPaymentMethod($cart->id, $selectedPaymentOption);

        }

        return redirect()->to(generateUrl('cart', 'payment'));
    }

    /**
     * Prepare and display the payment page.
     */
    public function payment()
    {
        // echo '24';
        // exit();
        $data = setPageMeta(trans("shopping_cart"));

        //check for guest checkout
        if (empty(authCheck()) && $this->generalSettings->guest_checkout != 1) {
            setErrorMessage(trans("msg_cart_login_error"));
            return redirect()->to(generateUrl('cart'));
        }

        $servicePayment = helperGetSession('mds_service_payment');

        if (!empty($servicePayment)) {

            if (!authCheck()) {
                return redirect()->to(generateUrl('cart'));
            }

            if ($this->paymentSettings->cart_location_selection && $servicePayment->serviceType != 'add_funds'
                && (empty(user()->country_id) || empty(user()->state_id))) {
                return redirect()->to(generateUrl('cart', 'payment_method'));
            }

            $data['cart'] = null;

        } else {
            $data['cart'] = $this->cartModel->getCart(true, true);

            $cartIsInvalid = empty($data['cart']) || empty($data['cart']->is_valid);
            if (empty($servicePayment) && $cartIsInvalid) {
                return redirect()->to(generateUrl('cart'));
            }

            if ($this->paymentSettings->cart_location_selection && (empty($data['cart']->location_country_id) || empty($data['cart']->location_state_id))) {
                return redirect()->to(generateUrl('cart', 'payment_method'));
            }
        }

        $checkout = $this->checkoutModel->createCheckout($data['cart'], $servicePayment);
        if (empty($checkout) || empty($checkout->checkout_token)) {
            return redirect()->to(generateUrl('cart', 'payment_method'));
        }

        if ($checkout->currency_code !== $this->selectedCurrency->code) {
            $checkout = $this->checkoutModel->createCheckout($data['cart'], $servicePayment, true);
        }

        $data['servicePayment'] = $servicePayment;
        $data['customer'] = getCartCustomerData($checkout);
        $data['totalAmount'] = numToDecimal($checkout->grand_total);
        $data['totalAmountInSubunits'] = (int)round($data['totalAmount'] * 100);
        $data['currencyCode'] = $checkout->currency_code;
        $data['checkout'] = $checkout;
        $data['checkoutItems'] = $this->checkoutModel->getCheckoutItems($checkout->id);
        $data['initPaymentGateway'] = false;

        if ($checkout->payment_method != 'bank_transfer' && $checkout->payment_method != 'cash_on_delivery' && $checkout->payment_method != 'wallet_balance') {
            $data['paymentGateway'] = getPaymentGateway($checkout->payment_method);
            if (empty($data['paymentGateway'])) {
                return redirect()->to(generateUrl('cart', 'payment_method'));
            }

            $gatewayKey = $data['paymentGateway']->name_key ?? null;
            $gatewayCurrenciesArray = [];
            if ($gatewayKey && isset(\Config\PaymentGateways::$currencies[$gatewayKey])) {
                $gatewayCurrenciesArray = \Config\PaymentGateways::$currencies[$gatewayKey];
            }

            $currencyCode = is_string($checkout->currency_code) ? $checkout->currency_code : '';

            if ($currencyCode === '' || !in_array($currencyCode, $gatewayCurrenciesArray, true)) {
                setErrorMessage(trans("currency_not_supported"));
            } else {
                $data['initPaymentGateway'] = true;
                $data = $this->initiatePayment($data['paymentGateway'], $data['checkout'], $data['checkoutItems'], $data);
            }
        }

        if ($checkout->payment_method == 'wallet_balance') {
            $data['initPaymentGateway'] = true;
        }

        echo view('partials/_header', $data);
        echo view('cart/payment', $data);
        echo view('partials/_footer');
    }

    /**
     * Initiates the payment process based on the selected payment method.
     */
    private function initiatePayment(object $paymentGateway, object $checkout, array $items, array $data): array
    {
        // echo '25';
        // exit();
        try {
            switch ($paymentGateway->name_key) {
                case 'stripe':
                    $stripe = new Stripe($paymentGateway, $this->baseVars->appName);
                    $data['stripeCheckoutUrl'] = $stripe->createCheckoutSessionFromOrder($checkout, $items);
                    if (empty($data['stripeCheckoutUrl'])) {
                        throw new \Exception(trans("payment_option_load_error"));
                    }
                    break;

                case 'razorpay':
                    $razorpay = new Razorpay($paymentGateway);
                    $orderData = [
                        'receipt' => $checkout->checkout_token,
                        'amount' => $data['totalAmountInSubunits'],
                        'currency' => $data['currencyCode'],
                        'notes' => ['checkout_token' => $checkout->checkout_token]
                    ];
                    $data['razorpayOrderId'] = $razorpay->createOrder($orderData);
                    if (empty($data['razorpayOrderId'])) {
                        throw new \Exception(trans("payment_option_load_error"));
                    }
                    break;

                case 'dlocalgo':
                    $dlocalGo = new DlocalGo($paymentGateway);
                    $data['dlocalRedirectUrl'] = $checkout->payment_url;
                    if (empty($data['dlocalRedirectUrl'])) {
                        $data['dlocalRedirectUrl'] = $dlocalGo->generatePaymentRedirectUrl($checkout);
                        if (!empty($data['dlocalRedirectUrl'])) {
                            $this->checkoutModel->setPaymentUrl($checkout->id, $data['dlocalRedirectUrl']);
                        }
                    }
                    break;

                case 'midtrans':
                    $midtrans = new Midtrans($paymentGateway);
                    $customerDetails = getCartCustomerData($checkout);
                    $grossAmount = intval($data['totalAmount']);
                    $data['midtransSnapToken'] = $midtrans->createSnapToken($checkout->checkout_token, $grossAmount, $customerDetails);
                    if (empty($data['midtransSnapToken'])) {
                        throw new \Exception(trans("payment_option_load_error"));
                    }
                    break;

                case 'paytabs':
                    $payTabs = new PayTabs($paymentGateway);
                    $data['paytabsCheckoutUrl'] = $checkout->payment_url;
                    if (empty($data['paytabsCheckoutUrl'])) {
                        $customer = getCartCustomerData($checkout);
                        $data['paytabsCheckoutUrl'] = $payTabs->createPayPage($checkout, $customer, $this->generalSettings->application_name);
                        if (!empty($data['paytabsCheckoutUrl'])) {
                            $this->checkoutModel->setPaymentUrl($checkout->id, $data['paytabsCheckoutUrl']);
                        }
                    }
                    break;

                case 'iyzico':
                    $iyzico = new Iyzico($paymentGateway);
                    $data['checkoutForm'] = $iyzico->createCheckoutForm($checkout);
                    break;

                case 'mercado_pago':
                    $mercadoPago = new MercadoPago($paymentGateway, $this->baseVars->appName);
                    $data['preferenceId'] = $mercadoPago->createPreferenceFromCheckout($checkout);
                    if (empty($data['preferenceId'])) {
                        throw new \Exception(trans("payment_option_load_error"));
                    }
                    break;
            }

        } catch (\Exception $e) {
            setErrorMessage($e->getMessage());
        }

        return $data;
    }

    /**
     * Return active payment methods based on cart and settings
     */
    private function getAvailablePaymentMethods($cart, $isServicePayment = false): array
    {
        $methods = [];

        foreach (getActivePaymentGateways() as $gateway) {
            $methods[] = esc($gateway->name_key);
        }

        if ($this->paymentSettings->bank_transfer_enabled == 1) {
            $methods[] = 'bank_transfer';
        }

        if ($this->paymentSettings->pay_with_wallet_balance == 1) {
            $methods[] = 'wallet_balance';
        }

        if ($this->paymentSettings->cash_on_delivery_enabled
            && !empty($cart)
            && $isServicePayment == false
            && empty($cart->has_digital_product)
            && $this->isAnyVendorEligibleForCOD($cart->items)) {
            $methods[] = 'cash_on_delivery';
        }

        return $methods;
    }

    /**
     * Check if any vendor allows cash on delivery
     */
    private function isAnyVendorEligibleForCOD(array $items): bool
    {
        foreach ($items as $item) {
            $vendor = getUser($item->seller_id);
            if (!empty($vendor)
                && $vendor->cash_on_delivery == 1
                && $vendor->commission_debt < $this->paymentSettings->cash_on_delivery_debt_limit) {
                return true;
            }
        }
        return false;
    }

    /**
     * AJAX Endpoint: Get shipping method by location
     *
     * @method POST
     */
    public function getShippingMethodsByLocation()
    {
        $data = [
            'result' => 0,
            'htmlContent' => ''
        ];

        $cart = $this->cartModel->getCart();
        if (empty($cart) || !$cart->is_valid) {
            return jsonResponse($data);
        }

        $stateId = inputPost('state_id');

        if (!empty($stateId)) {
            $shippingModel = new ShippingModel();

            $vars = [
                'stateId' => $stateId,
                'shippingMethods' => $shippingModel->getSellerShippingMethodsArray($cart->items, $stateId, $cart->currency_code)
            ];

            $data['result'] = 1;
            $data['htmlContent'] = view('cart/_shipping_methods', $vars);
        }
        return jsonResponse($data);
    }

    /**
     * Single-Page Express Checkout Confirmation Endpoint
     * Handles address, delivery method, and payment method in one unified step.
     */
    public function expressCheckoutConfirm()
    {
        // 1. Cart Validation
        $cart = $this->cartModel->getCart(true, true);
        if (empty($cart) || empty($cart->is_valid) || empty($cart->items)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => trans("msg_cart_error") ?: 'Your shopping cart is empty or invalid.'
            ]);
        }

        // 2. Address Validation
        $shippingAddressId = (int)$this->request->getPost('shipping_address_id');
        $useSameAddressForBilling = (int)($this->request->getPost('use_same_address_for_billing') ?? 1);
        $billingAddressId = $useSameAddressForBilling ? $shippingAddressId : (int)$this->request->getPost('billing_address_id');

        if (authCheck()) {
            if ($shippingAddressId <= 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please select or add a delivery address to proceed.'
                ]);
            }
            $profileModel = new ProfileModel();
            $sAddr = $profileModel->getShippingAddress($shippingAddressId);
            if (empty($sAddr) || $sAddr->user_id != user()->id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'The selected delivery address is invalid. Please choose a valid address.'
                ]);
            }
        } else {
            $firstName = cleanStr($this->request->getPost('shipping_first_name'));
            $email = cleanStr($this->request->getPost('shipping_email'));
            $address = cleanStr($this->request->getPost('shipping_address'));
            $stateId = (int)$this->request->getPost('shipping_state_id');
            if (empty($firstName) || empty($email) || empty($address) || empty($stateId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please fill in all required shipping address fields.'
                ]);
            }
        }

        // 3. Save Shipping Data & Shipping Cost
        $cartRaw = $this->cartModel->fetchRawCartData();
        if (!empty($cartRaw)) {
            $this->cartModel->setShippingData($cartRaw);

            // Auto-populate first available shipping method for sellers if not in POST
            $cartCurrent = $this->cartModel->getCart();
            if (!empty($cartCurrent) && !empty($cartCurrent->items)) {
                $shippingData = !empty($cartCurrent->shipping_data) ? safeJsonDecode($cartCurrent->shipping_data) : null;
                $stateId = !empty($shippingData->shippingStateId) ? $shippingData->shippingStateId : null;
                $shippingModel = new ShippingModel();
                $shippingMethodsArray = $shippingModel->getSellerShippingMethodsArray($cartCurrent->items, $stateId, $cartCurrent->currency_code);
                $cartSellerIds = $this->cartModel->getCartSellerIds($cartCurrent->items);
                if (!empty($cartSellerIds)) {
                    foreach ($cartSellerIds as $sellerId) {
                        if (empty($this->request->getPost('shipping_method_' . $sellerId))) {
                            foreach ($shippingMethodsArray as $sm) {
                                if ($sm->shop_id == $sellerId && !empty($sm->methods)) {
                                    $_POST['shipping_method_' . $sellerId] = $sm->methods[0]->id;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            $this->cartModel->setShippingCost($cartRaw);
        }

        // 4. Set Payment Method
        $paymentMethod = cleanStr($this->request->getPost('payment_method') ?: 'razorpay');
        $this->cartModel->setCartPaymentMethod($cart->id, $paymentMethod);

        // 5. Re-fetch cart with updated shipping and payment data
        $cart = $this->cartModel->getCart(true, true);

        // 6. Create Checkout Session
        helperDeleteSession('mds_service_payment');
        $checkout = $this->checkoutModel->createCheckout($cart, null, true);
        if (empty($checkout) || empty($checkout->checkout_token)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unable to initialize checkout session. Please refresh and try again.'
            ]);
        }

        // 7. Route to Payment Gateway Handler
        if ($paymentMethod === 'razorpay') {
            $paymentGateway = getPaymentGateway('razorpay');
            if (empty($paymentGateway) || empty($paymentGateway->status)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Razorpay payment gateway is currently unavailable.'
                ]);
            }

            try {
                $razorpay = new Razorpay($paymentGateway);
                $totalAmountInSubunits = (int)round($checkout->grand_total * 100);
                $orderData = [
                    'receipt'  => $checkout->checkout_token,
                    'amount'   => $totalAmountInSubunits,
                    'currency' => $checkout->currency_code,
                    'notes'    => ['checkout_token' => $checkout->checkout_token]
                ];
                $razorpayOrderId = $razorpay->createOrder($orderData);
                if (empty($razorpayOrderId)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Could not initiate Razorpay order. Please verify payment credentials.'
                    ]);
                }

                $customerData = getCartCustomerData($checkout);

                return $this->response->setJSON([
                    'status'         => 'razorpay',
                    'key'            => $paymentGateway->public_key,
                    'amount'         => $totalAmountInSubunits,
                    'currency'       => $checkout->currency_code,
                    'name'           => $this->generalSettings->application_name,
                    'description'    => 'Order #' . $checkout->checkout_token,
                    'image'          => getLogoEmail(),
                    'orderId'        => $razorpayOrderId,
                    'checkoutToken'  => $checkout->checkout_token,
                    'customer'       => [
                        'name'    => trim(($customerData->first_name ?? '') . ' ' . ($customerData->last_name ?? '')),
                        'email'   => $customerData->email ?? '',
                        'contact' => $customerData->phone_number ?? ''
                    ],
                    'paymentPostUrl' => generateUrl('checkout/complete-razorpay-payment')
                ]);
            } catch (\Throwable $e) {
                log_message('error', '[ExpressCheckout] Razorpay init error: ' . $e->getMessage());
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Razorpay communication error: ' . $e->getMessage()
                ]);
            }
        } elseif ($paymentMethod === 'cash_on_delivery') {
            $transaction = (object)[
                'payment_id'     => 'COD-' . generateToken(),
                'status_text'    => 'pending_payment',
                'status'         => 1,
                'payment_method' => 'cash_on_delivery',
            ];
            $result = $this->checkoutModel->handleProductCheckout($checkout, $transaction);
            return $this->response->setJSON([
                'status'      => 'success',
                'redirectUrl' => $result['redirectUrl'] ?? (generateUrl('order-completed') . '/' . $checkout->checkout_token)
            ]);
        } elseif ($paymentMethod === 'bank_transfer') {
            $transaction = (object)[
                'payment_id'     => 'BANK-' . generateToken(),
                'status_text'    => 'awaiting_payment',
                'status'         => 1,
                'payment_method' => 'bank_transfer',
            ];
            $result = $this->checkoutModel->handleProductCheckout($checkout, $transaction);
            return $this->response->setJSON([
                'status'      => 'success',
                'redirectUrl' => $result['redirectUrl'] ?? (generateUrl('order-completed') . '/' . $checkout->checkout_token)
            ]);
        } elseif ($paymentMethod === 'wallet_balance') {
            $result = $this->checkoutModel->handleWalletPayment($checkout);
            return $this->response->setJSON([
                'status'      => 'success',
                'redirectUrl' => $result['redirectUrl'] ?? (generateUrl('order-completed') . '/' . $checkout->checkout_token)
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unsupported payment method selected.'
        ]);
    }
}