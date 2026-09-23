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

        $mainProductId = $raw['items'][0]['main_product_id'] ?? $raw['items'][0]['product_id'];
        $mainProductqty = $raw['items'][0]['qty'] ?? 1;
        $pMain = $this->productModel->getActiveProduct($mainProductId);
        if (!$pMain) return jsonResponse(['result' => 0]);


        $bundleChildren = [];
        $bundleTotal = 0;

        foreach ($raw['items'] as $i) {

            $pid = (int)$i['product_id'];
            $qty = (int)$i['qty'];

            $p = $this->productModel->getActiveProduct($pid);
            if (!$p) continue;

            $price = $p->price_discounted > 0 ? $p->price_discounted : $p->price;
            $bundleTotal += $price * $qty;

            if (!isset($i['main_product_id'])) {
                $bundleChildren[] = [
                    'product_id' => $pid,
                    'qty' => $qty
                ];
            }
        }

        $cart = $this->cartModel->fetchRawCartData();
        $cartId = $cart->id ?? $this->cartModel->createCart();

        $hash = md5('bundle-' . md5(json_encode($bundleChildren)));

        $existing = $this->cartModel->getItemByHash($hash, $cartId);
        if ($existing) {
            $newQty = $existing->quantity + 1;
            $this->cartModel->updateItem($existing->id, [
                'quantity' => $newQty,
                'total_price' => $existing->unit_price * $newQty
            ]);
            return jsonResponse(['result'=>1,'bundle_id'=>$existing->id]);
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
            'cart_id'       => $cartId,
            'item_hash'     => $hash,
            'product_id'    => $pMain->id,
            'product_title' => $pMain->title,
            'product_type'  => $pMain->product_type,
            'listing_type'  => $pMain->listing_type,
            'product_sku'   => $pMain->sku . '-BUNDLE',
            'quantity'      => $mainProductqty,
            'purchase_type' => 'product',
            'quote_request_id' => 0,
            'unit_price'    => $pMain->price_discounted ?? $pMain->price,
            'unit_price_base' => $bundleTotal,
            'total_price'   => $bundleTotal,
            'is_bundle'     => 1,
            'bundle_items'  => json_encode($bundleChildren),
            'seller_id'     => $pMain->user_id,
            'product_image_id' => $imageId,
            'product_image_data' => $imageData,
            'product_vat'   => 0,
            'product_vat_rate' => 0,
            'is_stock_available' => 1,
            'created_at'    => date('Y-m-d H:i:s')
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
        $productId = inputPost('product_id');
        $quantity = (int)inputPost('product_quantity');
        $variantId = inputPost('variant_id');
        $extraOptions = inputPost('extra_options');
        $bundleComponentsJson = inputPost('bundle_components_data');
        $cartItemId = inputPost('cart_item_id');
        $product = $this->productModel->getActiveProduct($productId);
        $data = ['result' => 0];

        if (!empty($product) && $product->status == 1) {
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
        $cartItemId = inputPost('cart_item_id');
        $quantity = clrNum(inputPost('quantity'));
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
}