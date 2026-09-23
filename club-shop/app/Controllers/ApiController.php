<?php namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\FileModel;
use App\Models\TagModel;

/**
 * ApiController
 * Public REST API — no authentication required for GET.
 * POST /api/login   — get user token
 * POST /api/orders  — place order (guest or authenticated)
 *
 * Authentication: Pass Bearer token in Authorization header
 *   Authorization: Bearer {token}
 *
 * GET  /api/categories                  — list all parent categories
 * GET  /api/categories/{id}/products    — list active products for a category
 * GET  /api/products                    — list all active products (supports ?featured=1 to filter featured only)
 * GET  /api/products/{id}               — single product detail
 * GET  /api/products/slug/{slug}        — single product detail by slug
 * POST /api/login                       — authenticate and get token
 * POST /api/orders                      — place a new order (guest or registered)
 * POST /api/cart/validate-coupon  — validate a discount coupon (no cart required)
 * POST /api/coupons               — list active public coupons with pagination & filters
 * POST /api/cart/shipping-methods — get available shipping methods and costs for cart
 */
class ApiController extends BaseController
{
    // categoryModel and productModel are inherited as public from BaseController
    // Only declare what BaseController doesn't have
    public FileModel $fileModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->fileModel = new FileModel();

        // Disable debug toolbar for all API responses
        if (class_exists('\Config\Toolbar')) {
            $toolbar = config('Toolbar');
            if (isset($toolbar->collectors)) {
                $toolbar->collectors = [];
            }
        }
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function json($data, int $status = 200)
    {
        return $this->response
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setJSON($data);
    }

    private function perPage(): int
    {
        $pp = (int)($this->request->getGet('per_page') ?? 20);
        return max(1, min($pp, 100));
    }

    private function page(): int
    {
        return max(1, (int)($this->request->getGet('page') ?? 1));
    }

    /**
     * Resolve authenticated user from Authorization: Bearer {token} header.
     * Returns user object or null for guest.
     */
    private function getAuthUser(): ?object
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) {
            return null;
        }
        $token = trim(str_replace('Bearer', '', $header));
        if (empty($token)) {
            return null;
        }
        $user = $this->authModel->getUserByToken($token);
        if (empty($user) || $user->banned == 1) {
            return null;
        }
        return $user;
    }

    // ----------------------------------------------------------------
    // POST /api/login
    // Body: { "email": "user@example.com", "password": "secret" }
    // Returns token for use in Authorization header
    // ----------------------------------------------------------------
    public function login()
    {
        $body  = $this->request->getJSON(true) ?? [];
        $email = trim($body['email'] ?? '');
        $pass  = $body['password'] ?? '';

        if (empty($email) || empty($pass)) {
            return $this->json(['status' => 'error', 'message' => 'email and password are required'], 422);
        }

        $user = $this->authModel->getUserByEmail($email);
        if (empty($user)) {
            return $this->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        if (!password_verify($pass, $user->password)) {
            return $this->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        if ($user->banned == 1) {
            return $this->json(['status' => 'error', 'message' => 'Account is banned'], 403);
        }

        // Generate token if not set
        if (empty($user->token)) {
            $token = bin2hex(random_bytes(32));
            \Config\Database::connect()->table('users')->where('id', $user->id)->update(['token' => $token]);
        } else {
            $token = $user->token;
        }

        return $this->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'       => (int)$user->id,
                'username' => $user->username,
                'email'    => $user->email,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
            ],
        ]);
    }

    private function formatProduct(object $p): array
    {
        $image = $this->fileModel->getProductMainImage($p->id);
        $imageUrl = base_url('assets/img/no-image.jpg');
        if (!empty($image) && !empty($image->image_medium)) {
            $imageUrl = getStorageFileUrl('uploads/images/' . $image->image_medium, $image->storage ?? 'local');
        } elseif (!empty($image) && !empty($image->image_small)) {
            $imageUrl = getStorageFileUrl('uploads/images/' . $image->image_small, $image->storage ?? 'local');
        }

        // Fetch tags for this product
        $tagModel = new TagModel();
        $langId = 1; // or $this->activeLang->id

        $tagRows = $tagModel->getProductTags($p->id, $langId);
        // print_r($tagRows);

        $tagList = implode(', ', array_map(function ($row) {
            return $row->tag;
        }, $tagRows));

        return [
            'id'          => (int)$p->id,
            'title'       => $p->title ?? '',
            'slug'        => $p->slug,
            'sku'         => $p->sku ?? '',
            'price'       => (float)$p->price,
            'price_discounted' => (float)$p->price_discounted,
            'discount_rate'    => (int)$p->discount_rate,
            'currency'    => $p->currency,
            'product_type'=> $p->product_type,
            'category_id' => (int)$p->category_id,
            'rating'      => (float)$p->rating,
            'stock'       => (int)$p->stock,
            'image'       => $imageUrl,
            'url'         => generateProductUrlBySlug($p->slug),
            'vat_rate'    => (float)($p->vat_rate ?? 0),
            'cgst_rate'   => round((float)($p->vat_rate ?? 0) / 2, 2),
            'sgst_rate'   => round((float)($p->vat_rate ?? 0) / 2, 2),
            'tax_amount'  => round((float)($p->price_discounted > 0 ? $p->price_discounted : $p->price) * (float)($p->vat_rate ?? 0) / 100, 2),
            'tags'        => $tagList,
            'created_at'  => $p->created_at,
        ];
    }

    private function formatCategory(object $c): array
    {
        $imageUrl = '';
        if (!empty($c->image)) {
            $imageUrl = getStorageFileUrl($c->image, $c->storage ?? 'local');
        }

        return [
            'id'        => (int)$c->id,
            'name'      => $c->cat_name ?? $c->slug,
            'slug'      => $c->slug,
            'parent_id' => (int)$c->parent_id,
            'image'     => $imageUrl,
            'url'       => generateCategoryUrl($c),
        ];
    }

    // ----------------------------------------------------------------
    // GET /api/categories
    // ----------------------------------------------------------------
    public function categories()
    {
        $langId     = (int)($this->request->getGet('lang_id') ?? $this->activeLang->id);
        $parentOnly = $this->request->getGet('parent_only') !== '0'; // default true

        $this->categoryModel->buildQuery($langId);
        $this->categoryModel->builder->where('status', 1);

        if ($parentOnly) {
            $this->categoryModel->builder->where('parent_id', 0);
        }

        $rows = $this->categoryModel->builder->get()->getResult();

        return $this->json([
            'status' => 'success',
            'data'   => array_map([$this, 'formatCategory'], $rows),
            'total'  => count($rows),
        ]);
    }

    // ----------------------------------------------------------------
    // GET /api/categories/{id}/products
    // ----------------------------------------------------------------
    public function categoryProducts(int $categoryId)
    {
        $category = $this->categoryModel->getCategory($categoryId);
        if (empty($category) || $category->status != 1) {
            return $this->json(['status' => 'error', 'message' => 'Category not found'], 404);
        }

        $perPage = $this->perPage();
        $page    = $this->page();
        $offset  = ($page - 1) * $perPage;

        // Build query — include subcategory products via closure table
        $this->productModel->setBaseQuery(true);
        $this->productModel->builder
            ->where('products.is_active', 1)
            ->where('products.is_deleted', 0)
            ->join('category_paths AS cp', 'products.category_id = cp.descendant_id')
            ->where('cp.ancestor_id', $categoryId)
            ->orderBy('products.created_at', 'DESC');

        // Count
        $countBuilder = clone $this->productModel->builder;
        $total = $countBuilder->countAllResults();

        // Fetch
        $products = $this->productModel->builder
            ->limit($perPage, $offset)
            ->get()->getResult();

        return $this->json([
            'status'   => 'success',
            'category' => $this->formatCategory($category),
            'data'     => array_map([$this, 'formatProduct'], $products),
            'pagination' => [
                'total'    => $total,
                'per_page' => $perPage,
                'page'     => $page,
                'pages'    => (int)ceil($total / $perPage),
            ],
        ]);
    }

    // ----------------------------------------------------------------
    // GET /api/products
    // Optional query params: category_id, search, sort, featured, page, per_page
    //   featured=1  — return only featured (promoted) products
    //   sort        — newest|price_asc|price_desc|rating|featured
    // ----------------------------------------------------------------
    public function products()
    {
        $perPage    = $this->perPage();
        $page       = $this->page();
        $offset     = ($page - 1) * $perPage;
        $categoryId = (int)($this->request->getGet('category_id') ?? 0);
        $search     = removeSpecialCharacters($this->request->getGet('search') ?? '');
        $sort       = $this->request->getGet('sort') ?? 'newest'; // newest|price_asc|price_desc|rating|featured
        $featured   = $this->request->getGet('featured');         // 1 = only featured products

        $this->productModel->setBaseQuery(true);
        $this->productModel->builder
            ->where('products.is_active', 1)
            ->where('products.is_deleted', 0);

        // Filter by featured (is_promoted)
        if ($featured === '1' || $featured === 'true') {
            $this->productModel->builder->where('products.is_promoted', 1);
        }

        // Filter by category (includes subcategories)
        if (!empty($categoryId)) {
            $this->productModel->builder
                ->join('category_paths AS cp', 'products.category_id = cp.descendant_id')
                ->where('cp.ancestor_id', $categoryId);
        }

        // Search
        if (!empty($search)) {
            $langId = (int)$this->activeLang->id;
            $this->productModel->builder
                ->join('product_details AS pd_s', 'pd_s.product_id = products.id AND pd_s.lang_id = ' . $langId, 'left')
                ->like('pd_s.title', $search);
        }

        // Sort
        switch ($sort) {
            case 'price_asc':
                $this->productModel->builder->orderBy('products.price_discounted', 'ASC');
                break;
            case 'price_desc':
                $this->productModel->builder->orderBy('products.price_discounted', 'DESC');
                break;
            case 'rating':
                $this->productModel->builder->orderBy('products.rating', 'DESC');
                break;
            case 'featured':
                $this->productModel->builder
                    ->orderBy('products.is_promoted', 'DESC')
                    ->orderBy('products.promote_start_date', 'DESC');
                break;
            default:
                $this->productModel->builder->orderBy('products.created_at', 'DESC');
        }

        // Count
        $countBuilder = clone $this->productModel->builder;
        $total = $countBuilder->countAllResults();

        // Fetch
        $products = $this->productModel->builder
            ->limit($perPage, $offset)
            ->get()->getResult();

        return $this->json([
            'status' => 'success',
            'data'   => array_map([$this, 'formatProduct'], $products),
            'pagination' => [
                'total'    => $total,
                'per_page' => $perPage,
                'page'     => $page,
                'pages'    => (int)ceil($total / $perPage),
            ],
        ]);
    }

    // ----------------------------------------------------------------
    // GET /api/products/{id}
    // ----------------------------------------------------------------
    public function productDetail(int $id)
    {
        $this->productModel->setBaseQuery(true);
        $product = $this->productModel->builder
            ->where('products.id', $id)
            ->where('products.is_active', 1)
            ->where('products.is_deleted', 0)
            ->get()->getRow();

        if (empty($product)) {
            return $this->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        return $this->json([
            'status' => 'success',
            'data'   => $this->formatProductDetail($product),
        ]);
    }

    // ----------------------------------------------------------------
    // GET /api/products/slug/{slug}
    // ----------------------------------------------------------------
    public function productDetailBySlug(string $slug)
    {
        $this->productModel->setBaseQuery(true);
        $product = $this->productModel->builder
            ->where('products.slug', $slug)
            ->where('products.is_active', 1)
            ->where('products.is_deleted', 0)
            ->get()->getRow();

        if (empty($product)) {
            return $this->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        return $this->json([
            'status' => 'success',
            'data'   => $this->formatProductDetail($product),
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/orders — guest or registered user
    // Pass Authorization: Bearer {token} header for registered orders
    // ----------------------------------------------------------------
    public function placeOrder()
    {
        $body = $this->request->getJSON(true) ?? [];

        // ── Resolve auth user (optional — guest if no token) ──────
        $authUser  = $this->getAuthUser();
        $buyerId   = !empty($authUser) ? (int)$authUser->id : 0;
        $buyerType = !empty($authUser) ? 'registered' : 'guest';

        // Auto-fill from user profile if fields are missing
        if (!empty($authUser)) {
            $body['name']     = $body['name']     ?? trim($authUser->first_name . ' ' . $authUser->last_name) ?: $authUser->username;
            $body['email']    = $body['email']    ?? $authUser->email;
            $body['phone']    = $body['phone']    ?? ($authUser->phone_number ?? '');
            $body['address']  = $body['address']  ?? ($authUser->address ?? '');
            $body['zip_code'] = $body['zip_code'] ?? ($authUser->zip_code ?? '');
        }

        // ── Validate required fields ──────────────────────────────
        $required = ['items', 'payment_method', 'name', 'email', 'phone', 'address'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                return $this->json(['status' => 'error', 'message' => "Field '{$field}' is required"], 422);
            }
        }

        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['status' => 'error', 'message' => 'Invalid email address'], 422);
        }

        $items = $body['items'];
        if (!is_array($items) || empty($items)) {
            return $this->json(['status' => 'error', 'message' => 'items must be a non-empty array'], 422);
        }

        // ── Payment method & status logic ─────────────────────────
        $paymentMethod = $body['payment_method'];

        $offlineMethods = ['cash_on_delivery', 'bank_transfer', 'free'];
        $onlineMethods  = ['stripe', 'paypal', 'razorpay', 'paystack', 'flutterwave',
                           'iyzico', 'midtrans', 'mercado_pago', 'paytabs', 'yoomoney',
                           'dlocalgo', 'wallet'];
        $allMethods = array_merge($offlineMethods, $onlineMethods);

        if (!in_array($paymentMethod, $allMethods)) {
            return $this->json([
                'status'  => 'error',
                'message' => 'Invalid payment_method. Allowed: ' . implode(', ', $allMethods)
            ], 422);
        }

        // Determine payment & order status
        if (!empty($body['payment_status']) && in_array($body['payment_status'], ['pending_payment', 'payment_received'])) {
            $paymentStatus = $body['payment_status'];
        } elseif ($paymentMethod === 'cash_on_delivery') {
            $paymentStatus = 'pending_payment';
        } elseif ($paymentMethod === 'bank_transfer') {
            $paymentStatus = 'pending_payment';
        } elseif ($paymentMethod === 'free') {
            $paymentStatus = 'payment_received';
        } else {
            // Online payment — assume received if transaction_id provided
            $paymentStatus = !empty($body['transaction_id']) ? 'payment_received' : 'pending_payment';
        }

        $orderStatus = match($paymentMethod) {
            'cash_on_delivery' => 'order_processing',
            'bank_transfer'    => 'pending_payment',
            'free'             => 'payment_received',
            default            => $paymentStatus === 'payment_received' ? 'payment_received' : 'pending_payment',
        };

        // ── Resolve products & calculate totals ───────────────────
        $db         = \Config\Database::connect();
        $orderItems = [];
        $subtotal   = 0.0;
        $currency   = !empty($body['currency']) ? strtoupper(trim($body['currency'])) : ($this->defaultCurrency->code ?? 'INR');

        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $quantity  = max(1, (int)($item['quantity'] ?? 1));

            $product = $db->table('products')
                ->where('id', $productId)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get()->getRow();

            if (empty($product)) {
                return $this->json(['status' => 'error', 'message' => "Product ID {$productId} not found or inactive"], 404);
            }

            if ($product->product_type === 'physical' && (int)$product->stock < $quantity) {
                return $this->json(['status' => 'error', 'message' => "Insufficient stock for product ID {$productId} (available: {$product->stock})"], 422);
            }

            $unitPrice  = (float)($product->price_discounted > 0 ? $product->price_discounted : $product->price);
            $totalPrice = $unitPrice * $quantity;
            $subtotal  += $totalPrice;

            $details = $db->table('product_details')
                ->where('product_id', $productId)
                ->limit(1)->get()->getRow();

            $mainImage = $db->table('images')
                ->where('product_id', $productId)
                ->orderBy('is_main DESC, id')
                ->limit(1)->get()->getRow();

            $orderItems[] = [
                'product_id'    => $productId,
                'product'       => $product,
                'seller_id'     => $product->user_id,
                'product_title' => $details->title ?? $product->slug,
                'product_sku'   => $product->sku ?? '',
                'product_type'  => $product->product_type,
                'listing_type'  => $product->listing_type,
                'unit_price'    => $unitPrice,
                'quantity'      => $quantity,
                'total_price'   => $totalPrice,
                'image_id'      => $mainImage->id ?? 0,
                'image_data'    => !empty($mainImage) ? json_encode(['image_small' => $mainImage->image_small ?? '']) : '',
            ];
        }

        $shippingCharges = max(0, (float)($body['shipping_charges'] ?? 0));
        $grandTotal      = $subtotal + $shippingCharges; // shipping added to grand total

        // ── Build billing address ─────────────────────────────────
        $billing = [
            'bFirstName'   => $body['name'],
            'bLastName'    => $body['last_name'] ?? '',
            'bEmail'       => $body['email'],
            'bPhoneNumber' => $body['phone'],
            'bAddress'     => $body['address'],
            'bCity'        => $body['city'] ?? '',
            'bState'       => $body['state'] ?? '',
            'bCountry'     => $body['country'] ?? '',
            'bZipCode'     => $body['zip_code'] ?? '',
        ];

        // ── Build shipping address ────────────────────────────────
        $sameAsBilling = ($body['shipping_same_as_billing'] ?? true) !== false;
        if ($sameAsBilling) {
            $shipping = [
                'sFirstName'   => $billing['bFirstName'],
                'sLastName'    => $billing['bLastName'],
                'sEmail'       => $billing['bEmail'],
                'sPhoneNumber' => $billing['bPhoneNumber'],
                'sAddress'     => $billing['bAddress'],
                'sCity'        => $billing['bCity'],
                'sState'       => $billing['bState'],
                'sCountry'     => $billing['bCountry'],
                'sZipCode'     => $billing['bZipCode'],
            ];
        } else {
            $shipping = [
                'sFirstName'   => $body['shipping_name']     ?? $billing['bFirstName'],
                'sLastName'    => $body['shipping_last_name'] ?? $billing['bLastName'],
                'sEmail'       => $body['shipping_email']    ?? $billing['bEmail'],
                'sPhoneNumber' => $body['shipping_phone']    ?? $billing['bPhoneNumber'],
                'sAddress'     => $body['shipping_address']  ?? $billing['bAddress'],
                'sCity'        => $body['shipping_city']     ?? $billing['bCity'],
                'sState'       => $body['shipping_state']    ?? $billing['bState'],
                'sCountry'     => $body['shipping_country']  ?? $billing['bCountry'],
                'sZipCode'     => $body['shipping_zip_code'] ?? $billing['bZipCode'],
            ];
        }

        $shippingData = serialize((object)array_merge($billing, $shipping));

        // ── Insert order ──────────────────────────────────────────
        $orderNumber = uniqid();

        $orderData = [
            'order_number'           => $orderNumber,
            'buyer_id'               => $buyerId,
            'buyer_type'             => $buyerType,
            'price_subtotal'         => number_format($subtotal, 2, '.', ''),
            'price_shipping'         => number_format($shippingCharges, 2, '.', ''),
            'price_vat'              => '0.00',
            'price_total'            => number_format($grandTotal, 2, '.', ''),
            'price_currency'         => $currency,
            'status'                 => ($paymentStatus === 'payment_received') ? 1 : 0,
            'payment_method'         => $paymentMethod,
            'payment_status'         => $paymentStatus,
            'bank_transaction_number'=> $body['bank_transaction_number'] ?? '',
            'shipping'               => $shippingData,
            'coupon_code'            => $body['coupon_code'] ?? '',
            'coupon_discount'        => '0.00',
            'updated_at'             => date('Y-m-d H:i:s'),
            'created_at'             => date('Y-m-d H:i:s'),
        ];

        $db->table('orders')->insert($orderData);
        $orderId = $db->insertID();

        if (empty($orderId)) {
            return $this->json(['status' => 'error', 'message' => 'Failed to create order'], 500);
        }

        // Finalize order number with ID
        // $orderNumber = 'API-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        // $orderNumber = uniqid();
        
        // $db->table('orders')->where('id', $orderId)->update(['order_number' => $orderNumber]);

        // ── Insert order items ────────────────────────────────────
        foreach ($orderItems as $item) {
            $itemStatus = $item['product_type'] === 'digital' && $orderStatus === 'payment_received'
                ? 'completed' : $orderStatus;

            $db->table('order_items')->insert([
                'order_id'                 => $orderId,
                'seller_id'                => $item['seller_id'],
                'buyer_id'                 => $buyerId,
                'buyer_type'               => $buyerType,
                'product_id'               => $item['product_id'],
                'product_type'             => $item['product_type'],
                'listing_type'             => $item['listing_type'],
                'product_title'            => $item['product_title'],
                'product_sku'              => $item['product_sku'],
                'product_unit_price'       => number_format($item['unit_price'], 2, '.', ''),
                'product_quantity'         => $item['quantity'],
                'product_total_price'      => number_format($item['total_price'], 2, '.', ''),
                'product_currency'         => $currency,
                'product_vat_rate'         => 0,
                'product_vat'              => '0.00',
                'image_id'                 => $item['image_id'],
                'image_data'               => $item['image_data'],
                'commission_rate'          => 0,
                'order_status'             => $itemStatus,
                'is_approved'              => ($itemStatus === 'completed') ? 1 : 0,
                'shipping_method'          => '',
                'seller_shipping_cost'     => '0.00',
                'shipping_tracking_number' => '',
                'shipping_tracking_url'    => '',
                'bundle_items'             => '',
                'is_bundle'                => 0,
                'updated_at'               => date('Y-m-d H:i:s'),
                'created_at'               => date('Y-m-d H:i:s'),
            ]);
        }

        // ── Record payment transaction (online payments) ──────────
        if (!empty($body['transaction_id']) && $paymentStatus === 'payment_received') {
            $db->table('transactions')->insert([
                'payment_method'  => $paymentMethod,
                'payment_id'      => $body['transaction_id'],
                'order_id'        => $orderId,
                'user_id'         => $buyerId,
                'user_type'       => $buyerType,
                'currency'        => $currency,
                'payment_amount'  => number_format($grandTotal, 2, '.', ''),
                'payment_status'  => 'Completed',
                'checkout_token'  => '',
                'ip_address'      => $this->request->getIPAddress(),
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }

        // ── Build response ────────────────────────────────────────
        return $this->json([
            'status'         => 'success',
            'message'          => 'Order placed successfully',
            'order_number'     => $orderNumber,
            'order_id'         => $orderId,
            'payment_method'   => $paymentMethod,
            'payment_status'   => $paymentStatus,
            'order_status'     => $orderStatus,
            'subtotal'         => number_format($subtotal, 2, '.', ''),
            'shipping_charges' => number_format($shippingCharges, 2, '.', ''),
            'total'            => number_format($grandTotal, 2, '.', ''),
            'currency'         => $currency,
            'items_count'      => count($orderItems),
            'items'            => array_map(fn($i) => [
                'product_id'    => $i['product_id'],
                'product_title' => $i['product_title'],
                'quantity'      => $i['quantity'],
                'unit_price'    => number_format($i['unit_price'], 2, '.', ''),
                'total_price'   => number_format($i['total_price'], 2, '.', ''),
            ], $orderItems),
            'billing' => [
                'name'    => $billing['bFirstName'] . ' ' . $billing['bLastName'],
                'email'   => $billing['bEmail'],
                'phone'   => $billing['bPhoneNumber'],
                'address' => $billing['bAddress'],
                'city'    => $billing['bCity'],
                'state'   => $billing['bState'],
                'country' => $billing['bCountry'],
                'zip'     => $billing['bZipCode'],
            ],
            'shipping' => [
                'name'    => $shipping['sFirstName'] . ' ' . $shipping['sLastName'],
                'email'   => $shipping['sEmail'],
                'phone'   => $shipping['sPhoneNumber'],
                'address' => $shipping['sAddress'],
                'city'    => $shipping['sCity'],
                'state'   => $shipping['sState'],
                'country' => $shipping['sCountry'],
                'zip'     => $shipping['sZipCode'],
            ],
        ], 201);
    }

    private function formatProductDetail(object $p): array
    {
        $langId  = (int)$this->activeLang->id;
        $details = getProductDetails($p->id, $langId);

        // All images
        $images    = $this->fileModel->getProductImages($p->id);
        $imageList = [];
        foreach ($images as $img) {
            $size = !empty($img->image_medium) ? $img->image_medium : ($img->image_small ?? '');
            if (!empty($size)) {
                $imageList[] = getStorageFileUrl('uploads/images/' . $size, $img->storage ?? 'local');
            }
        }

        // Category
        $category = $this->categoryModel->getCategory($p->category_id);

        // Seller
        $db     = \Config\Database::connect();
        $seller = $db->table('users')->select('id, username, slug')->where('id', $p->user_id)->get()->getRow();

        // Tags
        $tagModel = new TagModel();
        $langId = 1; // or $this->activeLang->id

        $tagRows = $tagModel->getProductTags($p->id, $langId);
        // print_r($tagRows);

        $tagList = implode(', ', array_map(function ($row) {
            return $row->tag;
        }, $tagRows));

        return [
            'id'               => (int)$p->id,
            'title'            => $details->title ?? ($p->title ?? ''),
            'slug'             => $p->slug,
            'sku'              => $p->sku ?? '',
            'short_description'=> $details->short_description ?? '',
            'description'      => $details->description ?? '',
            'price'            => (float)$p->price,
            'price_discounted' => (float)$p->price_discounted,
            'discount_rate'    => (int)$p->discount_rate,
            'currency'         => $p->currency,
            'product_type'     => $p->product_type,
            'listing_type'     => $p->listing_type,
            'stock'            => (int)$p->stock,
            'rating'           => (float)$p->rating,
            'vat_rate'         => (float)($p->vat_rate ?? 0),
            'cgst_rate'        => round((float)($p->vat_rate ?? 0) / 2, 2),
            'sgst_rate'        => round((float)($p->vat_rate ?? 0) / 2, 2),
            'tax_amount'       => round((float)($p->price_discounted > 0 ? $p->price_discounted : $p->price) * (float)($p->vat_rate ?? 0) / 100, 2),
            'category'         => $category ? [
                'id'   => (int)$category->id,
                'name' => $category->cat_name ?? $category->slug,
                'slug' => $category->slug,
            ] : null,
            'seller'           => $seller ? [
                'id'       => (int)$seller->id,
                'username' => $seller->username,
                'url'      => generateProfileUrl($seller->slug),
            ] : null,
            'images'           => $imageList,
            'url'              => generateProductUrlBySlug($p->slug),
            'tags'             => $tagList,
            'is_bundle'        => !empty($p->is_bundle),
            'bundle_components'=> !empty($p->is_bundle) ? (new \App\Models\BundleModel())->getBundleComponents((int)$p->id, true) : [],
            'created_at'       => $p->created_at,
        ];
    }

    // ----------------------------------------------------------------
    // POST /api/cart/shipping-methods
    // No auth required
    // Body: {
    //   "product_ids": [1, 2],   — products to calculate shipping for
    //   "quantities":  [1, 2],   — matching quantities (optional, defaults to 1 each)
    //   "state_id": 1239,        — destination state ID (optional if country_id provided)
    //   "country_id": 99         — destination country ID (optional fallback when no state_id)
    // }
    //
    // state_id and country_id are both optional.
    // If neither is given, returns all methods with no location filtering.
    // Find state_id: POST /api/states with {"country_id": 99}
    // Find country_id: India = 99
    // ----------------------------------------------------------------
    public function cartShippingMethods()
    {
        $body       = $this->request->getJSON(true) ?? [];
        $stateId    = (int)($body['state_id']   ?? 0);
        $countryId  = (int)($body['country_id'] ?? 0);
        $productIds = $body['product_ids'] ?? [];
        $quantities = $body['quantities']  ?? [];

        if (empty($productIds) || !is_array($productIds)) {
            return $this->json(['status' => 'error', 'message' => 'product_ids array is required'], 422);
        }

        // If no state_id (or explicitly 0), try to resolve from country_id
        // Pick a real state from that country so the zone lookup works
        $db = \Config\Database::connect();
        if (empty($stateId) && !empty($countryId)) {
            // Validate country exists first
            $countryExists = $db->table('location_countries')
                ->where('id', $countryId)
                ->countAllResults();
            if (empty($countryExists)) {
                return $this->json(['status' => 'error', 'message' => 'Invalid country_id. Use POST /api/locations to get valid country IDs.'], 422);
            }
            $firstState = $db->table('location_states')
                ->where('country_id', $countryId)
                ->limit(1)
                ->get()->getRow();
            if (!empty($firstState)) {
                $stateId = (int)$firstState->id;
            }
        }

        // Check marketplace shipping is enabled
        if ($this->productSettings->marketplace_shipping != 1) {
            return $this->json([
                'status'           => 'success',
                'shipping_enabled' => false,
                'message'          => 'Shipping is not enabled. Set shipping_charges to 0 when placing order.',
                'sellers'          => [],
                'total_shipping'   => '0.00',
                'currency'         => $this->defaultCurrency->code ?? 'INR',
            ]);
        }

        $currencyCode  = $this->defaultCurrency->code ?? 'INR';
        $shippingModel = new \App\Models\ShippingModel();

        // Build fake cart items from provided product_ids
        $fakeItems = [];
        foreach ($productIds as $idx => $pid) {
            $pid = (int)$pid;
            $qty = (int)($quantities[$idx] ?? 1);
            if ($qty < 1) $qty = 1;

            $product = $db->table('products')
                ->where('id', $pid)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get()->getRow();

            if (empty($product)) {
                continue;
            }

            $unitPrice = (float)($product->price_discounted > 0 ? $product->price_discounted : $product->price);

            $item                    = new \stdClass();
            $item->product_id        = $pid;
            $item->quantity          = $qty;
            $item->product_type      = $product->product_type;
            $item->listing_type      = $product->listing_type;
            $item->seller_id         = $product->user_id;
            // If chargeable_weight is 0, use 0.5 as minimum so weight-tier shipping rules can match
            $item->chargeable_weight = (!empty($product->chargeable_weight) && $product->chargeable_weight > 0)
                ? (float)$product->chargeable_weight
                : 0.5;
            $item->unit_price        = $unitPrice;
            $item->total_price       = $unitPrice * $qty;
            $item->is_bundle         = 0;

            $fakeItems[] = $item;
        }

        if (empty($fakeItems)) {
            return $this->json([
                'status'  => 'error',
                'message' => 'No valid products found. Check that product_ids exist and are active.',
                'invalid_ids' => array_values(array_map('intval', $productIds)),
            ], 422);
        }

        // If still no state_id, try seller's own country from the shipping zone
        if (empty($stateId)) {
            // Get seller IDs from items
            $sellerIds = array_unique(array_column($fakeItems, 'seller_id'));
            foreach ($sellerIds as $sid) {
                $zoneLocation = $db->table('shipping_zone_locations szl')
                    ->join('shipping_zones sz', 'szl.zone_id = sz.id')
                    ->where('sz.user_id', $sid)
                    ->where('szl.country_id >', 0)
                    ->limit(1)
                    ->get()->getRow();
                if (!empty($zoneLocation)) {
                    $firstState = $db->table('location_states')
                        ->where('country_id', $zoneLocation->country_id)
                        ->limit(1)
                        ->get()->getRow();
                    if (!empty($firstState)) {
                        $stateId = (int)$firstState->id;
                        break;
                    }
                }
            }
        }

        // Get shipping methods grouped by seller
        $sellerMethods = $shippingModel->getSellerShippingMethodsArray($fakeItems, $stateId, $currencyCode);

        $totalShipping = 0;
        $sellers       = [];

        foreach ($sellerMethods as $seller) {
            $methods = [];
            foreach ($seller->methods ?? [] as $method) {
                $cost      = (float)($method->cost ?? 0);
                $methods[] = [
                    'method_id'   => $method->method_db_id ?? null,
                    'method_type' => $method->method       ?? '',
                    'name'        => $method->name         ?? '',
                    'cost'        => number_format($cost, 2, '.', ''),
                ];
            }

            // Pick cheapest available method cost
            $cheapestCost = 0;
            if (!empty($methods)) {
                $cheapestCost = min(array_map(fn($m) => (float)$m['cost'], $methods));
                $totalShipping += $cheapestCost;
            }

            $sellers[] = [
                'seller_id'     => $seller->shop_id   ?? 0,
                'seller_name'   => $seller->shop_name ?? '',
                'methods'       => $methods,
                'cheapest_cost' => number_format($cheapestCost, 2, '.', ''),
            ];
        }

        if (empty($sellers)) {
            return $this->json([
                'status'           => 'success',
                'shipping_enabled' => true,
                'message'          => 'No shipping methods configured for this location. Contact the seller or set shipping_charges to 0.',
                'state_id'         => $stateId,
                'sellers'          => [],
                'total_shipping'   => '0.00',
                'currency'         => $currencyCode,
            ]);
        }

        return $this->json([
            'status'           => 'success',
            'shipping_enabled' => true,
            'state_id'         => $stateId,
            'sellers'          => $sellers,
            'total_shipping'   => number_format($totalShipping, 2, '.', ''),
            'currency'         => $currencyCode,
            'note'             => 'Use total_shipping as shipping_charges when calling POST /api/orders',
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/coupons
    // No auth required — public endpoint
    // Body (all optional):
    //   { "page": 1, "per_page": 15, "q": "", "category_id": 0 }
    //
    // Returns active (non-expired, not-limit-reached) public coupons
    // with seller info, applicable products, and pagination.
    // ----------------------------------------------------------------
    public function listCoupons()
    {
        $body    = $this->request->getJSON(true) ?? [];
        $page    = max(1, (int)($body['page']     ?? 1));
        $perPage = (int)($body['per_page'] ?? 15);
        $q       = trim($body['q']         ?? '');
        $catId   = (int)($body['category_id'] ?? 0);

        if ($perPage < 1 || $perPage > 100) {
            return $this->json(['status' => 'error', 'message' => 'per_page must be between 1 and 100'], 422);
        }

        $offset   = ($page - 1) * $perPage;
        $langId   = (int)($this->activeLang->id ?? 1);
        $db       = \Config\Database::connect();
        $today    = date('Y-m-d H:i:s');

        // Base query — only active public coupons that are not expired and not over limit
        $builder = $db->table('coupons')
            ->select('coupons.*, users.username AS seller_username, users.slug AS seller_slug,
                (SELECT COUNT(cu.id) FROM coupons_used cu WHERE cu.coupon_code = coupons.coupon_code) AS used_count')
            ->join('users', 'users.id = coupons.seller_id', 'left')
            ->where('coupons.is_public', 1)
            ->where('coupons.expiry_date >', $today)
            ->where('coupons.coupon_count > 0');

        // Only coupons that still have uses remaining
        $builder->where('(SELECT COUNT(cu2.id) FROM coupons_used cu2 WHERE cu2.coupon_code = coupons.coupon_code) < coupons.coupon_count');

        // Optional search — coupon code or seller username
        if (!empty($q)) {
            $builder->groupStart()
                ->like('coupons.coupon_code', $q)
                ->orLike('users.username', $q)
                ->groupEnd();
        }

        // Optional category filter — coupons that have products in this category
        if (!empty($catId)) {
            $builder->where("FIND_IN_SET({$catId}, IFNULL(coupons.category_ids, '')) > 0");
        }

        // Count
        $countBuilder = clone $builder;
        $total        = $countBuilder->countAllResults();

        // Fetch page
        $rows = $builder
            ->orderBy('coupons.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResult();

        // Format each coupon
        $data = [];
        foreach ($rows as $row) {
            // Get applicable products with names
            $products = [];
            $prodRows = $db->table('coupon_products')
                ->select('coupon_products.product_id, COALESCE(pd.title, products.slug) AS product_name,
                          products.category_id, products.price, products.price_discounted')
                ->join('products', 'products.id = coupon_products.product_id', 'left')
                ->join('product_details AS pd', 'pd.product_id = products.id AND pd.lang_id = ' . $langId, 'left')
                ->where('coupon_products.coupon_id', (int)$row->id)
                ->where('products.is_active', 1)
                ->where('products.is_deleted', 0)
                ->get()->getResult();

            foreach ($prodRows as $pr) {
                $products[] = [
                    'product_id'   => (int)$pr->product_id,
                    'product_name' => $pr->product_name ?? '',
                    'category_id'  => (int)$pr->category_id,
                    'price'        => (float)($pr->price_discounted > 0 ? $pr->price_discounted : $pr->price),
                ];
            }

            // Parse category IDs
            $categoryIds = !empty($row->category_ids)
                ? array_values(array_filter(array_map('intval', explode(',', $row->category_ids))))
                : [];

            $couponsRemaining = max(0, (int)$row->coupon_count - (int)$row->used_count);

            $data[] = [
                'id'                => (int)$row->id,
                'coupon_code'       => $row->coupon_code,
                'discount_rate'     => (int)$row->discount_rate,
                'number_of_coupons' => (int)$row->coupon_count,
                'coupons_used'      => (int)$row->used_count,
                'coupons_remaining' => $couponsRemaining,
                'usage_type'        => $row->usage_type,
                'minimum_order'     => number_format((float)$row->minimum_order_amount, 2, '.', ''),
                'currency'          => $row->currency ?? $this->defaultCurrency->code ?? 'INR',
                'expiry_date'       => $row->expiry_date,
                'coupon_status'     => 'active',
                'date'              => $row->created_at,
                'seller'            => [
                    'username' => $row->seller_username ?? '',
                    'url'      => !empty($row->seller_slug) ? generateProfileUrl($row->seller_slug) : '',
                ],
                'category_ids'      => $categoryIds,
                'products'          => $products,
            ];
        }

        return $this->json([
            'status' => 'success',
            'data'   => $data,
            'pagination' => [
                'total'    => $total,
                'per_page' => $perPage,
                'page'     => $page,
                'pages'    => $total > 0 ? (int)ceil($total / $perPage) : 1,
            ],
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/cart/validate-coupon
    // Requires: Authorization: Bearer {token}
    // Body: { "coupon_code": "SUMMERCAMP50" }
    //
    // Validates the coupon against the authenticated user's cart.
    // Returns discount preview WITHOUT applying the coupon.
    // Response includes subtotal, discount_amount, estimated_total.
    // ----------------------------------------------------------------
    public function validateCoupon()
    {
        // 1. Require Bearer token — login is mandatory
        $authUser = $this->getAuthUser();
        if (empty($authUser)) {
            return $this->json(['status' => 'error', 'message' => 'Unauthorized. Please login first.'], 401);
        }

        // 2. Read and validate coupon_code
        $body       = $this->request->getJSON(true) ?? [];
        $couponCode = trim($body['coupon_code'] ?? '');

        if (empty($couponCode)) {
            return $this->json(['status' => 'error', 'message' => 'coupon_code is required'], 422);
        }

        $couponCode = removeSpecialCharacters($couponCode);

        // 3. Fetch the user's cart directly by user_id (bypasses session)
        $db      = \Config\Database::connect();
        $rawCart = $db->table('carts')->where('user_id', (int)$authUser->id)->get()->getRow();

        if (empty($rawCart)) {
            return $this->json(['status' => 'error', 'message' => 'No active cart found. Add items to cart first.'], 422);
        }

        // 4. Load cart items
        $cartItems = $db->table('cart_items')
            ->where('cart_id', $rawCart->id)
            ->get()->getResult();

        if (empty($cartItems)) {
            return $this->json(['status' => 'error', 'message' => 'Cart is empty. Add items to cart first.'], 422);
        }

        // 5. Build minimal cart object for CouponModel
        $cart                         = new \stdClass();
        $cart->id                     = $rawCart->id;
        $cart->user_id                = $rawCart->user_id;
        $cart->session_id             = $rawCart->session_id;
        $cart->currency_code          = $rawCart->currency_code;
        $cart->currency_code_base     = $rawCart->currency_code_base;
        $cart->currency_exchange_rate = (float)($rawCart->exchange_rate ?? 1);
        $cart->coupon_code            = $rawCart->coupon_code;
        $cart->shipping_cost          = (float)($rawCart->shipping_cost ?? 0);
        $cart->location_country_id    = $rawCart->location_country_id ?? 0;
        $cart->location_state_id      = $rawCart->location_state_id ?? 0;
        $cart->items                  = $cartItems;

        // 6. Get product IDs in cart for coupon product matching
        $cartProductIds = array_map(fn($i) => (int)$i->product_id, $cartItems);

        // 7. Look up coupon — must match at least one cart product
        $couponModel = new \App\Models\CouponModel();
        $coupon      = $couponModel->getCouponByCodeCart($couponCode, $cartProductIds);

        if (empty($coupon)) {
            return $this->json(['status' => 'error', 'message' => trans('msg_invalid_coupon') ?: 'Invalid or expired coupon code.'], 422);
        }

        // 8. Check expiry
        if (!empty($coupon->expiry_date) && date('Y-m-d H:i:s') > $coupon->expiry_date) {
            return $this->json(['status' => 'error', 'message' => trans('msg_invalid_coupon') ?: 'This coupon has expired.'], 422);
        }

        // 9. Check usage limit
        if ((int)$coupon->used_coupon_count >= (int)$coupon->coupon_count) {
            return $this->json(['status' => 'error', 'message' => trans('msg_coupon_limit') ?: 'Coupon usage limit reached.'], 422);
        }

        // 10. Single-use: check if this user already used it
        if ($coupon->usage_type === 'single') {
            if ($couponModel->isCouponUsed($authUser->id, $couponCode)) {
                return $this->json(['status' => 'error', 'message' => trans('msg_coupon_used') ?: 'You have already used this coupon.'], 422);
            }
        }

        // 11. Calculate cart subtotal and coupon discount
        $subtotal      = 0;
        $totalDiscount = 0;
        $exchangeRate  = $cart->currency_exchange_rate;

        foreach ($cartItems as $item) {
            // Get live product price
            $product  = $db->table('products')
                ->where('id', (int)$item->product_id)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get()->getRow();

            if (empty($product)) {
                continue;
            }

            $unitPrice = (float)($product->price_discounted > 0 ? $product->price_discounted : $product->price);

            // Apply currency conversion if enabled
            if (!empty($exchangeRate) && $exchangeRate != 1) {
                $unitPrice = $unitPrice * $exchangeRate;
            }

            $lineTotal  = $unitPrice * (int)$item->quantity;
            $subtotal  += $lineTotal;

            // Apply discount only to applicable coupon products
            if (in_array((int)$item->product_id, $coupon->product_ids ?? [], true)) {
                $totalDiscount += $lineTotal * $coupon->discount_rate / 100;
            }
        }

        // 12. Check minimum order amount
        $minAmount = (float)$coupon->minimum_order_amount;
        if (!empty($exchangeRate) && $exchangeRate != 1) {
            $minAmount = $minAmount * $exchangeRate;
        }

        // Calculate seller subtotal for minimum order check
        $sellerSubtotal = 0;
        foreach ($cartItems as $item) {
            $product = $db->table('products')
                ->select('price, price_discounted, user_id')
                ->where('id', (int)$item->product_id)
                ->get()->getRow();
            if ($product && (int)$product->user_id === (int)$coupon->seller_id) {
                $unitPrice = (float)($product->price_discounted > 0 ? $product->price_discounted : $product->price);
                if (!empty($exchangeRate) && $exchangeRate != 1) {
                    $unitPrice = $unitPrice * $exchangeRate;
                }
                $sellerSubtotal += $unitPrice * (int)$item->quantity;
            }
        }

        if ($minAmount > 0 && $sellerSubtotal < $minAmount) {
            $formattedMin = number_format($minAmount, 2, '.', '');
            return $this->json([
                'status'  => 'error',
                'message' => (trans('msg_coupon_cart_total') ?: 'Minimum order amount required:') . ' ' . $cart->currency_code . ' ' . $formattedMin,
            ], 422);
        }

        $estimatedTotal = max(0, $subtotal - $totalDiscount);
        $currency       = $rawCart->currency_code ?? $this->defaultCurrency->code ?? 'INR';

        // 13. Fetch full coupon row for extra fields (category_ids, coupon_count, created_at, is_public)
        $fullCoupon = $db->table('coupons')
            ->where('coupon_code', $couponCode)
            ->get()->getRow();

        // 14. Fetch applicable products with names and categories
        $applicableProductIds = $coupon->product_ids ?? [];
        $products = [];
        if (!empty($applicableProductIds)) {
            $langId   = (int)($this->activeLang->id ?? 1);
            $prodRows = $db->table('products')
                ->select('products.id, products.slug, products.category_id, product_details.title AS product_name, categories.cat_name AS category_name')
                ->join('product_details', 'product_details.product_id = products.id AND product_details.lang_id = ' . $langId, 'left')
                ->join('category_translations', 'category_translations.cat_id = products.category_id AND category_translations.lang_id = ' . $langId, 'left')
                ->join('categories', 'categories.id = products.category_id', 'left')
                ->whereIn('products.id', $applicableProductIds)
                ->get()->getResult();

            foreach ($prodRows as $pr) {
                $products[] = [
                    'product_id'    => (int)$pr->id,
                    'product_name'  => $pr->product_name ?? $pr->slug,
                    'category_id'   => (int)$pr->category_id,
                    'category_name' => $pr->category_name ?? '',
                ];
            }
        }

        // 15. Parse category IDs from coupon
        $couponCategoryIds = [];
        if (!empty($fullCoupon->category_ids)) {
            $couponCategoryIds = array_values(array_filter(array_map('intval', explode(',', $fullCoupon->category_ids))));
        }

        // 16. Fetch category names for coupon categories
        $couponCategories = [];
        if (!empty($couponCategoryIds)) {
            $langId   = (int)($this->activeLang->id ?? 1);
            $catRows  = $db->table('categories')
                ->select('categories.id, categories.slug')
                ->whereIn('categories.id', $couponCategoryIds)
                ->get()->getResult();

            foreach ($catRows as $cr) {
                $couponCategories[] = [
                    'category_id'   => (int)$cr->id,
                    'category_name' => $cr->slug ?? '',
                ];
            }
        }

        // 17. Determine status label
        $isExpired    = !empty($fullCoupon->expiry_date) && date('Y-m-d H:i:s') > $fullCoupon->expiry_date;
        $isLimitReached = (int)($coupon->used_coupon_count ?? 0) >= (int)($fullCoupon->coupon_count ?? 0);
        $statusLabel  = ($isExpired || $isLimitReached) ? 'inactive' : 'active';

        // 18. Return full preview — coupon NOT applied yet
        return $this->json([
            'status'           => 'success',
            'valid'            => true,
            'message'          => 'Coupon is valid',

            // Coupon details
            'coupon_code'      => $couponCode,
            'discount_rate'    => (int)$coupon->discount_rate,
            'number_of_coupons'=> (int)($fullCoupon->coupon_count ?? $coupon->coupon_count ?? 0),
            'coupons_used'     => (int)($coupon->used_coupon_count ?? 0),
            'coupons_remaining'=> max(0, (int)($fullCoupon->coupon_count ?? 0) - (int)($coupon->used_coupon_count ?? 0)),
            'usage_type'       => $coupon->usage_type,
            'minimum_order'    => number_format((float)($coupon->minimum_order_amount ?? 0), 2, '.', ''),
            'expiry_date'      => $coupon->expiry_date,
            'coupon_status'    => $statusLabel,
            'date'             => $fullCoupon->created_at ?? '',

            // Products and categories this coupon applies to
            'products'         => $products,
            'categories'       => $couponCategories,

            // Cart totals
            'discount_amount'  => number_format($totalDiscount, 2, '.', ''),
            'subtotal'         => number_format($subtotal, 2, '.', ''),
            'estimated_total'  => number_format($estimatedTotal, 2, '.', ''),
            'currency'         => $currency,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/pos/create-order
    // Requires: Authorization: Bearer {token}  (vendor role only)
    // Creates a new POS order — mirrors DashboardController::savePosOrder()
    //
    // Required body fields:
    //   customer_name, customer_phone, payment_date, payment_method,
    //   items (array), grand_total, amount_paid
    //
    // Optional body fields:
    //   customer_email, billing_address{}, shipping_address{},
    //   subtotal, total_tax, shipping_charges, payment_reference
    // ----------------------------------------------------------------
    public function posCreateOrder()
    {
        // 1. Resolve auth user
        $authUser = $this->getAuthUser();
        if (empty($authUser)) {
            return $this->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 2. Vendor role check
        if (!isVendor($authUser)) {
            return $this->json(['status' => 'error', 'message' => 'Vendor access required'], 403);
        }

        // 3. Read body
        $body = $this->request->getJSON(true) ?? [];

        // 4. Validate required fields
        $customerName  = trim($body['customer_name'] ?? '');
        $customerPhone = trim($body['customer_phone'] ?? '');
        $paymentDate   = trim($body['payment_date'] ?? '');
        $paymentMethod = trim($body['payment_method'] ?? '');
        $items         = $body['items'] ?? [];

        if (empty($customerName)) {
            return $this->json(['status' => 'error', 'message' => 'customer_name is required'], 422);
        }
        if (empty($customerPhone) || !preg_match('/^\+?[\d\s\-]{7,15}$/', $customerPhone)) {
            return $this->json(['status' => 'error', 'message' => 'A valid customer_phone is required (7-15 digits)'], 422);
        }
        if (empty($paymentDate)) {
            return $this->json(['status' => 'error', 'message' => 'payment_date is required (YYYY-MM-DD)'], 422);
        }
        if (empty($paymentMethod)) {
            return $this->json(['status' => 'error', 'message' => 'payment_method is required (UPI, Card, Cash, Bank Transfer)'], 422);
        }
        if (!in_array($paymentMethod, ['UPI', 'Card', 'Cash', 'Bank Transfer', 'Cheque'], true)) {
            return $this->json(['status' => 'error', 'message' => 'Invalid payment_method. Allowed: UPI, Card, Cash, Bank Transfer, Cheque'], 422);
        }
        if (empty($items) || !is_array($items)) {
            return $this->json(['status' => 'error', 'message' => 'items must be a non-empty array'], 422);
        }

        // 5. Validate items — each must have product_name, unit_price > 0, quantity > 0
        // Other fields (discount_price, tax_percent, tax_amount, line_total) are optional
        $validItems = array_filter($items, fn($i) =>
            !empty(trim($i['product_name'] ?? '')) &&
            (float)($i['unit_price'] ?? 0) > 0 &&
            (float)($i['quantity']   ?? 0) > 0
        );
        if (empty($validItems)) {
            return $this->json(['status' => 'error', 'message' => 'At least one valid item is required (product_name, unit_price > 0, quantity > 0)'], 422);
        }

        // Normalize items — fill in optional fields with defaults
        // Accept both 'discount_price' and 'discount' field names
        $validItems = array_map(function($i) {
            $unitPrice   = (float)($i['unit_price']    ?? 0);
            $quantity    = (float)($i['quantity']      ?? 1);
            // Accept 'discount_price' or 'discount' interchangeably
            $discountAmt = (float)($i['discount_price'] ?? $i['discount'] ?? 0);
            $taxPercent  = (float)($i['tax_percent']   ?? 0);
            $base        = $unitPrice * $quantity;
            $net         = max(0, $base - $discountAmt);
            $taxAmount   = isset($i['tax_amount'])
                ? (float)$i['tax_amount']
                : round($net * $taxPercent / 100, 2);
            $lineTotal   = isset($i['line_total'])
                ? (float)$i['line_total']
                : round($net + $taxAmount, 2);

            return [
                'product_name'   => trim($i['product_name'] ?? ''),
                'unit_price'     => $unitPrice,
                'quantity'       => $quantity,
                'discount_price' => $discountAmt,
                'tax_percent'    => $taxPercent,
                'tax_amount'     => $taxAmount,
                'line_total'     => $lineTotal,
            ];
        }, array_values($validItems));

        // 6. Validate email if provided
        $customerEmail = trim($body['customer_email'] ?? '');
        if (!empty($customerEmail) && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['status' => 'error', 'message' => 'Invalid customer_email format'], 422);
        }

        // 7. Server-side recalculation — never trust client totals
        $subtotal      = 0;
        $totalTax      = 0;
        $totalDiscount = 0;

        foreach ($validItems as $item) {
            $base          = (float)$item['unit_price'] * (float)$item['quantity'];
            $discAmt       = (float)($item['discount_price'] ?? 0);
            $net           = max(0, $base - $discAmt);
            $taxAmt        = round($net * (float)$item['tax_percent'] / 100, 2);
            $subtotal     += $net;
            $totalTax     += $taxAmt;
            $totalDiscount += $discAmt;
        }

        $shippingCharges = max(0, (float)($body['shipping_charges'] ?? 0));
        $grandTotal      = round($subtotal + $totalTax + $shippingCharges, 2);
        $amountPaid      = (float)($body['amount_paid'] ?? 0);
        $source          = trim($body['source']       ?? '');
        $convertedBy     = trim($body['converted_by'] ?? '');
        $remarks         = trim($body['remarks']      ?? '');

        // If total_discount is explicitly provided in body and > item-calculated discount, use body value
        // This handles order-level discounts passed directly
        $bodyDiscount = (float)($body['total_discount'] ?? -1);
        if ($bodyDiscount >= 0 && $bodyDiscount !== $totalDiscount) {
            $totalDiscount = $bodyDiscount;
            // Recalculate grand total using provided discount
            $grandTotal = round($subtotal + $totalTax + $shippingCharges - ($totalDiscount - array_sum(array_column($validItems, 'discount_price'))), 2);
            if ($grandTotal < 0) $grandTotal = 0;
        }

        // Validate: discount_price per item cannot exceed item base price
        foreach ($validItems as $item) {
            $base    = (float)$item['unit_price'] * (float)$item['quantity'];
            $discAmt = (float)($item['discount_price'] ?? 0);
            if ($discAmt > $base) {
                return $this->json([
                    'status'  => 'error',
                    'message' => "discount_price ({$discAmt}) cannot exceed item total ({$base}) for product: {$item['product_name']}",
                ], 422);
            }
        }

        if ($grandTotal <= 0) {
            return $this->json(['status' => 'error', 'message' => 'grand_total must be greater than 0'], 422);
        }

        if ($amountPaid > $grandTotal) {
            return $this->json(['status' => 'error', 'message' => "amount_paid ({$amountPaid}) cannot exceed grand_total ({$grandTotal})"], 422);
        }

        $balanceDue  = round(max(0, $grandTotal - $amountPaid), 2);
        $orderStatus = $balanceDue <= 0 ? 1 : 0;

        // Update validItems with recalculated tax_amount and line_total
        $validItems = array_map(function($item) {
            $base     = (float)$item['unit_price'] * (float)$item['quantity'];
            $discAmt  = (float)($item['discount_price'] ?? 0);
            $net      = max(0, $base - $discAmt);
            $taxAmt   = round($net * (float)$item['tax_percent'] / 100, 2);
            $item['tax_amount'] = $taxAmt;
            $item['line_total'] = round($net + $taxAmt, 2);
            return $item;
        }, $validItems);

        // 8. Build and insert the order record
        $db   = \Config\Database::connect();
        $data = [
            'order_number'      => 'POS-' . strtoupper(substr(uniqid(), -8)),
            'seller_id'         => (int)$authUser->id,
            'customer_name'     => $customerName,
            'customer_phone'    => $customerPhone,
            'customer_email'    => $customerEmail,
            'billing_address'   => json_encode($body['billing_address']  ?? (object)[]),
            'shipping_address'  => json_encode($body['shipping_address'] ?? (object)[]),
            'items'             => json_encode(array_values($validItems)),
            'subtotal'          => round($subtotal, 2),
            'total_tax'         => round($totalTax, 2),
            'total_discount'    => round($totalDiscount, 2),
            'shipping_charges'  => $shippingCharges,
            'grand_total'       => $grandTotal,
            'amount_paid'       => $amountPaid,
            'balance_due'       => $balanceDue,
            'payment_method'    => $paymentMethod,
            'payment_reference' => trim($body['payment_reference'] ?? ''),
            'payment_date'      => $paymentDate,
            'currency'          => $this->defaultCurrency->code ?? 'INR',
            'status'            => $orderStatus,
            'source'            => $source,
            'converted_by'      => $convertedBy,
            'remarks'           => $remarks,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $db->table('pos_orders')->insert($data);
        $posOrderId = $db->insertID();

        if (!$posOrderId) {
            return $this->json(['status' => 'error', 'message' => 'Failed to save order. Please try again.'], 500);
        }

        // 9. Record initial payment if amount_paid > 0
        if ($amountPaid > 0) {
            $db->table('pos_order_payments')->insert([
                'pos_order_id'      => $posOrderId,
                'amount'            => $amountPaid,
                'payment_method'    => $paymentMethod,
                'payment_reference' => $data['payment_reference'],
                'payment_date'      => $paymentDate,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // 10. Return success
        return $this->json([
            'status'         => 'success',
            'message'        => 'POS order created successfully',
            'order_id'       => $posOrderId,
            'order_number'   => $data['order_number'],
            'subtotal'       => number_format($subtotal, 2, '.', ''),
            'total_discount' => number_format($totalDiscount, 2, '.', ''),
            'total_tax'      => number_format($totalTax, 2, '.', ''),
            'shipping_charges'=> number_format($shippingCharges, 2, '.', ''),
            'grand_total'    => number_format($grandTotal, 2, '.', ''),
            'amount_paid'    => number_format($amountPaid, 2, '.', ''),
            'balance_due'    => number_format($balanceDue, 2, '.', ''),
            'payment_status' => $orderStatus === 1 ? 'paid' : 'partial',
        ], 201);
    }

    // ----------------------------------------------------------------
    // POST /api/pos/order-history
    // Requires: Authorization: Bearer {token}
    // Body (all optional): { "page": 1, "per_page": 15, "q": "" }
    //
    // Returns POS orders for the authenticated buyer matched by
    // customer_phone or customer_email from their user profile.
    // ----------------------------------------------------------------
    public function posOrderHistory()
    {
        $body = $this->request->getJSON(true) ?? [];

        $orderNumber  = trim($body['order_number'] ?? '');
        $orderNumbers = $body['order_numbers'] ?? [];

        $db = \Config\Database::connect();

        // 3. Build order query
        $builder = $db->table('pos_orders');

        // Single order number
        if (!empty($orderNumber)) {

            $builder->where('order_number', $orderNumber);

        }
        // Multiple order numbers
        elseif (!empty($orderNumbers) && is_array($orderNumbers)) {

            $builder->whereIn('order_number', $orderNumbers);

        }

        // No pagination for order search
        $rows = $builder
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        
        // --------------------------------------------------
        // Format orders
        // --------------------------------------------------

        $data = [];

        foreach ($rows as $row) {

            // Payments
            $payments = $db->table('pos_order_payments')
                ->where('pos_order_id', $row->id)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResult();


            $paymentList = array_map(function ($p) {

                return [
                    'id'                => (int)$p->id,
                    'amount'            => number_format((float)$p->amount, 2, '.', ''),
                    'payment_method'    => $p->payment_method,
                    'payment_reference' => $p->payment_reference ?? '',
                    'payment_date'      => $p->payment_date,
                ];

            }, $payments);



            $data[] = [

                'id'                 => (int)$row->id,
                'order_number'       => $row->order_number,

                'customer_name'      => $row->customer_name ?? '',
                'customer_email'     => $row->customer_email ?? '',
                'customer_phone'     => $row->customer_phone ?? '',


                'subtotal'           => number_format((float)$row->subtotal, 2, '.', ''),
                'total_tax'          => number_format((float)$row->total_tax, 2, '.', ''),
                'total_discount'     => number_format((float)($row->total_discount ?? 0), 2, '.', ''),
                'shipping_charges'   => number_format((float)$row->shipping_charges, 2, '.', ''),
                'grand_total'        => number_format((float)$row->grand_total, 2, '.', ''),

                'amount_paid'        => number_format((float)$row->amount_paid, 2, '.', ''),
                'balance_due'        => number_format((float)$row->balance_due, 2, '.', ''),


                'payment_status'     => ((int)$row->status === 1)
                    ? 'paid'
                    : 'partial',


                'payment_method'     => $row->payment_method ?? '',

                'fulfillment_status' => $row->fulfillment_status ?? 'processing',

                'currency'           => $row->currency ?? '',


                'items'              => json_decode($row->items ?? 'null'),

                'billing_address'    => json_decode($row->billing_address ?? 'null'),


                'payments'           => $paymentList,


                'delivery' => [

                    'tracking_code' => $row->delivery_tracking_code ?? '',

                    'tracking_url'  => $row->delivery_tracking_url ?? '',

                    'date'          => $row->delivery_date ?? '',

                    'name'          => $row->delivery_name ?? '',

                    'phone'         => $row->delivery_phone ?? '',

                ],


                'created_at' => $row->created_at,

            ];
        }

        // --------------------------------------------------
        // Response
        // --------------------------------------------------

        $response = [
            'status' => 'success',
            'data'   => $data
        ];

        return $this->json($response);
    }


    public function posOrderHistoryBB()
    {
        // 1. Require login
        $authUser = $this->getAuthUser();
        if (empty($authUser)) {
            return $this->json(['status' => 'error', 'message' => 'Unauthorized. Please login first.'], 401);
        }

        // 2. Read params
        $body    = $this->request->getJSON(true) ?? [];
        $page    = max(1, (int)($body['page']     ?? 1));
        $perPage = (int)($body['per_page'] ?? 15);
        $q       = trim($body['q']         ?? '');

        if ($perPage < 1 || $perPage > 100) {
            return $this->json(['status' => 'error', 'message' => 'per_page must be between 1 and 100'], 422);
        }

        $offset = ($page - 1) * $perPage;
        $db     = \Config\Database::connect();

        // 3. Get buyer's phone and email from their profile
        $buyerPhone = trim($authUser->phone_number ?? '');
        $buyerEmail = trim($authUser->email        ?? '');

        if (empty($buyerPhone) && empty($buyerEmail)) {
            return $this->json([
                'status'     => 'success',
                'data'       => [],
                'pagination' => ['total' => 0, 'per_page' => $perPage, 'page' => $page, 'pages' => 1],
                'message'    => 'No phone or email on profile to match orders',
            ]);
        }

        // 4. Build query — match by phone OR email
        $builder = $db->table('pos_orders');
        $builder->groupStart();
        if (!empty($buyerPhone)) {
            $builder->where('customer_phone', $buyerPhone);
        }
        if (!empty($buyerPhone) && !empty($buyerEmail)) {
            $builder->orWhere('customer_email', $buyerEmail);
        } elseif (!empty($buyerEmail)) {
            $builder->where('customer_email', $buyerEmail);
        }
        $builder->groupEnd();

        // 5. Optional keyword search by order_number
        if (!empty($q)) {
            $builder->groupStart()
                ->like('order_number', $q)
                ->groupEnd();
        }

        // 6. Count + paginate
        $countBuilder = clone $builder;
        $total        = $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResult();

        // 7. Format each order
        $data = [];
        foreach ($rows as $row) {
            // Get payments for this order
            $payments = $db->table('pos_order_payments')
                ->where('pos_order_id', $row->id)
                ->orderBy('id', 'ASC')
                ->get()->getResult();

            $paymentList = array_map(fn($p) => [
                'id'                => (int)$p->id,
                'amount'            => number_format((float)$p->amount, 2, '.', ''),
                'payment_method'    => $p->payment_method,
                'payment_reference' => $p->payment_reference ?? '',
                'payment_date'      => $p->payment_date,
            ], $payments);

            $data[] = [
                'id'                 => (int)$row->id,
                'order_number'       => $row->order_number,
                'subtotal'           => number_format((float)$row->subtotal, 2, '.', ''),
                'total_tax'          => number_format((float)$row->total_tax, 2, '.', ''),
                'total_discount'     => number_format((float)($row->total_discount ?? 0), 2, '.', ''),
                'shipping_charges'   => number_format((float)$row->shipping_charges, 2, '.', ''),
                'grand_total'        => number_format((float)$row->grand_total, 2, '.', ''),
                'amount_paid'        => number_format((float)$row->amount_paid, 2, '.', ''),
                'balance_due'        => number_format((float)$row->balance_due, 2, '.', ''),
                'payment_status'     => (int)$row->status === 1 ? 'paid' : 'partial',
                'payment_method'     => $row->payment_method,
                'fulfillment_status' => $row->fulfillment_status ?? 'processing',
                'currency'           => $row->currency ?? '',
                'items'              => json_decode($row->items ?? 'null'),
                'billing_address'    => json_decode($row->billing_address ?? 'null'),
                'payments'           => $paymentList,
                'created_at'         => $row->created_at,
                // Delivery info (if shipped/delivered)
                'delivery'           => [
                    'tracking_code' => $row->delivery_tracking_code ?? '',
                    'tracking_url'  => $row->delivery_tracking_url  ?? '',
                    'date'          => $row->delivery_date           ?? '',
                    'name'          => $row->delivery_name           ?? '',
                    'phone'         => $row->delivery_phone          ?? '',
                ],
            ];
        }

        return $this->json([
            'status' => 'success',
            'data'   => $data,
            'pagination' => [
                'total'    => $total,
                'per_page' => $perPage,
                'page'     => $page,
                'pages'    => $total > 0 ? (int)ceil($total / $perPage) : 1,
            ],
        ]);
    }

    // ----------------------------------------------------------------
    //GET /api/pos/order-fulfillment-status
    // Body: { "order_number": "POS-6315AE86" }
    public function orderFulfillmentStatus()
    {
        // Require login
        // $authUser = $this->getAuthUser();

        // if (empty($authUser)) {
        //     return $this->json([
        //         'status'  => 'error',
        //         'message' => 'Unauthorized. Please login first.'
        //     ], 401);
        // }


        // Request body
        $body = $this->request->getJSON(true) ?? [];

        $orderNumber = trim($body['order_number'] ?? '');


        if (empty($orderNumber)) {
            return $this->json([
                'status'  => 'error',
                'message' => 'order_number is required'
            ], 422);
        }


        $db = \Config\Database::connect();


        // Find order
        $order = $db->table('pos_orders')
            ->select([
                'id',
                'order_number',
                'fulfillment_status',
                'delivery_tracking_code',
                'delivery_tracking_url',
                'delivery_date',
                'created_at'
            ])
            ->where('order_number', $orderNumber)
            ->get()
            ->getRow();


        if (empty($order)) {
            return $this->json([
                'status'  => 'error',
                'message' => 'Order not found'
            ], 404);
        }


        return $this->json([
            'status' => 'success',
            'data' => [
                'order_number'       => $order->order_number,
                'fulfillment_status' => $order->fulfillment_status ?? 'processing',

                'tracking_code'      => $order->delivery_tracking_code ?? '',
                'tracking_url'       => $order->delivery_tracking_url ?? '',

                'delivery_date'      => $order->delivery_date ?? '',

                'created_at'         => $order->created_at
            ]
        ]);
    }



    // ----------------------------------------------------------------
    // POST /api/posorders
    // Body: { "page": 1, "per_page": 15, "q": "", "payment_status": "" }
    // Requires: Authorization: Bearer {token}  (vendor role only)
    // Returns the authenticated vendor's POS orders with pagination.
    // payment_status: "" = all, "0" = partial/unpaid, "1" = paid
    // ----------------------------------------------------------------
    public function posOrders()
    {
        // 1. Resolve auth user from Bearer token
        $authUser = $this->getAuthUser();
        if (empty($authUser)) {
            return $this->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 2. Vendor role check — must pass $authUser explicitly (session authCheck() won't work for API callers)
        if (!isVendor($authUser)) {
            return $this->json(['status' => 'error', 'message' => 'Vendor access required'], 403);
        }

        // 3. Read and validate input from JSON body
        $body          = $this->request->getJSON(true) ?? [];
        $page          = max(1, (int)($body['page'] ?? 1));
        $perPage       = (int)($body['per_page'] ?? 15);
        $q             = trim($body['q'] ?? '');
        $paymentStatus = isset($body['payment_status']) ? (string)$body['payment_status'] : '';

        if ($perPage < 1 || $perPage > 100) {
            return $this->json(['status' => 'error', 'message' => 'per_page must be between 1 and 100'], 422);
        }

        if (!in_array($paymentStatus, ['', '0', '1'], true)) {
            return $this->json(['status' => 'error', 'message' => "Invalid payment_status. Allowed: '', '0', '1'"], 422);
        }

        $offset = ($page - 1) * $perPage;

        // 4. Build base query — always scoped to this vendor only
        $db      = \Config\Database::connect();
        $builder = $db->table('pos_orders')
            ->where('seller_id', (int)$authUser->id);

        // 5. Optional keyword search across id, order_number, customer_name, customer_phone
        if (!empty($q)) {
            $builder->groupStart()
                ->like('id', $q)
                ->orLike('order_number', $q)
                ->orLike('customer_name', $q)
                ->orLike('customer_phone', $q)
                ->groupEnd();
        }

        // 6. Optional payment status filter
        if ($paymentStatus !== '') {
            $builder->where('status', (int)$paymentStatus);
        }

        // 7. Count total matching rows (clone before adding limit)
        $countBuilder = clone $builder;
        $total        = $countBuilder->countAllResults();

        // 8. Fetch paginated results
        $rows = $builder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResult();

        // 9. Format and return
        return $this->json([
            'status' => 'success',
            'data'   => array_map([$this, 'formatPosOrder'], $rows),
            'pagination' => [
                'total'    => $total,
                'per_page' => $perPage,
                'page'     => $page,
                'pages'    => $total > 0 ? (int)ceil($total / $perPage) : 1,
            ],
        ]);
    }

    /**
     * Map a pos_orders DB row to the API response shape.
     */
    private function formatPosOrder(object $row): array
    {
        return [
            'id'                 => (int)$row->id,
            'order_number'       => $row->order_number       ?? '',
            'customer_name'      => $row->customer_name      ?? '',
            'customer_phone'     => $row->customer_phone     ?? '',
            'customer_email'     => $row->customer_email     ?? '',
            'subtotal'           => $row->subtotal            ?? '0.00',
            'total_tax'          => $row->total_tax           ?? '0.00',
            'total_discount'     => $row->total_discount      ?? '0.00',
            'shipping_charges'   => $row->shipping_charges    ?? '0.00',
            'grand_total'        => $row->grand_total         ?? '0.00',
            'amount_paid'        => $row->amount_paid         ?? '0.00',
            'balance_due'        => $row->balance_due         ?? '0.00',
            'payment_status'     => (int)($row->status ?? 0) === 1 ? 'paid' : 'partial',
            'payment_method'     => $row->payment_method     ?? '',
            'payment_reference'  => $row->payment_reference  ?? '',
            'fulfillment_status' => $row->fulfillment_status ?? 'processing',
            'items'              => json_decode($row->items           ?? 'null'),
            'billing_address'    => json_decode($row->billing_address ?? 'null'),
            'created_at'         => $row->created_at          ?? '',
        ];
    }

    /**
     * Create a sample bundle product with 15 products/variants
     */
    public function seedSampleBundle()
    {
        $db = \Config\Database::connect();
        $bundleModel = new \App\Models\BundleModel();
        $productModel = new \App\Models\ProductModel();

        // Find primary seller/vendor account (prefer sales@myskill.club)
        $seller = $db->table('users')->where('email', 'sales@myskill.club')->get()->getRow();
        if (empty($seller)) {
            $seller = $db->table('users')->where('role_id !=', 3)->get()->getRow();
        }
        $sellerId = $seller ? $seller->id : 1;

        // Find primary category
        $category = $db->table('categories')->get()->getRow();
        $categoryId = $category ? $category->id : 1;

        // 1. Check or create the sample bundle product
        $existing = $db->table('products')->where('slug', 'mega-sample-package-15-items')->get()->getRow();
        if (!empty($existing)) {
            $bundleId = $existing->id;
            $db->table('products')->where('id', $bundleId)->update([
                'user_id'             => $sellerId,
                'category_id'         => $categoryId,
                'is_bundle'           => 1,
                'bundle_pricing_type' => 'fixed',
                'price'               => 299.00,
                'price_discounted'    => 199.00,
                'discount_rate'       => 33,
                'status'              => 1,
                'is_active'           => 1,
                'is_deleted'          => 0,
                'is_draft'            => 0,
                'visibility'          => 1,
                'stock'               => 100
            ]);
        } else {
            $now = date('Y-m-d H:i:s');
            $currency = $this->defaultCurrency->code ?? 'USD';

            $productData = [
                'slug'                => 'mega-sample-package-15-items',
                'sku'                 => 'BUNDLE-SAMPLE-15',
                'price'               => 299.00,
                'price_discounted'    => 199.00,
                'discount_rate'       => 33,
                'currency'            => $currency,
                'product_type'        => 'physical',
                'listing_type'        => 'sell_on_site',
                'user_id'             => $sellerId,
                'category_id'         => $categoryId,
                'status'              => 1,
                'is_active'           => 1,
                'is_deleted'          => 0,
                'is_draft'            => 0,
                'visibility'          => 1,
                'is_bundle'           => 1,
                'bundle_pricing_type' => 'fixed',
                'stock'               => 100,
                'created_at'          => $now,
                'updated_at'          => $now
            ];

            $db->table('products')->insert($productData);
            $bundleId = $db->insertID();
        }

        // Ensure product details exist for all languages
        $languages = $db->table('languages')->where('status', 1)->get()->getResultObject();
        if (empty($languages)) {
            $languages = [(object)['id' => 1]];
        }
        foreach ($languages as $lang) {
            $pDetail = $db->table('product_details')->where('product_id', $bundleId)->where('lang_id', $lang->id)->get()->getRow();
            if (empty($pDetail)) {
                $db->table('product_details')->insert([
                    'product_id'        => $bundleId,
                    'lang_id'           => $lang->id,
                    'title'             => 'Mega Sample Package (15 Products Combo Kit)',
                    'short_description' => 'A comprehensive 15-item starter & power bundle containing curated products and variations.',
                    'description'       => '<p>Experience the ultimate value combo! This mega bundle includes 15 hand-selected items and variations in one complete package.</p>'
                ]);
            } else {
                $db->table('product_details')->where('id', $pDetail->id)->update([
                    'title'             => 'Mega Sample Package (15 Products Combo Kit)',
                    'short_description' => 'A comprehensive 15-item starter & power bundle containing curated products and variations.'
                ]);
            }
        }

        // Ensure product has a main image
        $existingImage = $db->table('images')->where('product_id', $bundleId)->get()->getRow();
        if (empty($existingImage)) {
            $sampleImg = $db->table('images')->where('product_id !=', $bundleId)->orderBy('id', 'ASC')->get()->getRow();
            if (!empty($sampleImg)) {
                $db->table('images')->insert([
                    'product_id'      => $bundleId,
                    'image_small'     => $sampleImg->image_small,
                    'image_default'   => $sampleImg->image_default,
                    'image_big'       => $sampleImg->image_big,
                    'is_main'         => 1,
                    'is_option_image' => 0,
                    'storage'         => $sampleImg->storage ?? 'local'
                ]);
            }
        }

        // Update search indexes
        $productModel->syncProductSearchIndex($bundleId);

        // 2. Ensure we have at least 15 active products in database
        $sampleItemNames = [
            'Pro Ergonomic Gaming Mouse',
            'Mechanical RGB Keyboard (Blue Switch)',
            'Ultra HD 4K Webcam 60FPS',
            'Studio Condenser USB Microphone',
            'Memory Foam Wrist Rest Pad',
            'Braided Type-C Fast Charging Cable (2M)',
            '65W GaN Dual-Port Wall Charger',
            'Wireless Noise Cancelling Earbuds',
            'Anti-Glare Computer Glasses',
            'Hard-Shell Waterproof Laptop Sleeve',
            'Aluminum Multi-Angle Laptop Stand',
            'Desk Mat Extended Mouse Pad (XXL)',
            'Magnetic Cable Organizer Clips (5-Pack)',
            'Reusable Microfiber Cleaning Cloth Set',
            'Portable Bluetooth 5.3 Stereo Speaker'
        ];

        $seller = $db->table('users')->where('role_id !=', 3)->get()->getRow();
        $sellerId = $seller ? $seller->id : 1;
        $now = date('Y-m-d H:i:s');
        $currency = $this->defaultCurrency->code ?? 'USD';

        $existingProds = $db->table('products')
            ->select('id, sku, price, stock')
            ->where('is_deleted', 0)
            ->where('id !=', $bundleId)
            ->get()
            ->getResultObject();

        $activeProductIds = array_map(function($p) { return $p->id; }, $existingProds);

        // If we have fewer than 15 active products, create remaining needed
        $needed = 15 - count($activeProductIds);
        for ($k = 0; $k < $needed; $k++) {
            $name = $sampleItemNames[$k] ?? ('Sample Package Component #' . ($k + 1));
            $slug = 'bundle-comp-item-' . time() . '-' . ($k + 1);
            $sku = 'COMP-SKU-' . sprintf('%03d', $k + 1);
            $price = rand(15, 60);

            $db->table('products')->insert([
                'slug'             => $slug,
                'sku'              => $sku,
                'price'            => $price,
                'price_discounted' => $price,
                'discount_rate'    => 0,
                'currency'         => $currency,
                'product_type'     => 'physical',
                'listing_type'     => 'sell_on_site',
                'user_id'          => $sellerId,
                'status'           => 1,
                'is_active'        => 1,
                'is_deleted'       => 0,
                'is_draft'         => 0,
                'visibility'       => 1,
                'is_bundle'        => 0,
                'stock'            => rand(50, 200),
                'created_at'       => $now,
                'updated_at'       => $now
            ]);
            $newId = $db->insertID();

            $languages = $db->table('languages')->where('status', 1)->get()->getResultObject();
            if (empty($languages)) {
                $languages = [(object)['id' => 1]];
            }
            foreach ($languages as $lang) {
                $db->table('product_details')->insert([
                    'product_id'        => $newId,
                    'lang_id'           => $lang->id,
                    'title'             => $name,
                    'short_description' => 'High quality ' . $name . ' included in the mega bundle pack.',
                    'description'       => '<p>Premium quality item included with full manufacturer warranty.</p>'
                ]);
            }
            $activeProductIds[] = $newId;
        }

        // Fetch up to 15 distinct products
        $allProducts = $db->table('products')
            ->select('id, sku, price, stock')
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('id !=', $bundleId)
            ->limit(15)
            ->get()
            ->getResultObject();

        $bundleItems = [];
        $count = 0;

        foreach ($allProducts as $p) {
            if ($count >= 15) break;

            // Check if product has variants
            $variant = $db->table('product_option_variants')->where('product_id', $p->id)->where('is_active', 1)->get()->getRow();

            $bundleItems[] = [
                'component_product_id' => (int)$p->id,
                'variant_id'           => $variant ? (int)$variant->id : null,
                'quantity'             => ($count % 4 === 0) ? 2 : 1,
                'price_override'       => null
            ];
            $count++;
        }

        $bundleModel->saveBundleComponents($bundleId, $bundleItems);
        $metrics = $bundleModel->calculateBundleMetrics($bundleId);
        $components = $bundleModel->getBundleComponents($bundleId, true);

        return $this->json([
            'status'             => 'success',
            'message'            => 'Sample 15-item bundle package created and linked successfully!',
            'bundle_product_id'  => $bundleId,
            'title'              => 'Mega Sample Package (15 Products Combo Kit)',
            'edit_dashboard_url' => base_url('dashboard/product/product-details/' . $bundleId),
            'storefront_url'     => generateProductUrlBySlug('mega-sample-package-15-items'),
            'metrics'            => $metrics,
            'components_count'   => count($components),
            'components'         => $components
        ]);
    }
}
