<?php

namespace App\Controllers;

use App\Libraries\Export;
use App\Models\BiddingModel;
use App\Models\CheckoutModel;
use App\Models\CouponModel;
use App\Models\EarningsModel;
use App\Models\FieldModel;
use App\Models\FileModel;
use App\Models\LocationModel;
use App\Models\MembershipModel;
use App\Models\OrderAdminModel;
use App\Models\OrderModel;
use App\Models\PageModel;
use App\Models\ProductAdminModel;
use App\Models\ProductModel;
use App\Models\ProfileModel;
use App\Models\PromoteModel;
use App\Models\ShippingModel;
use App\Models\ProductOptionsModel;

class DashboardController extends BaseController
{
    protected $orderAdminModel;
    protected $orderModel;
    protected $productAdminModel;
    protected $membershipModel;
    protected $shippingModel;
    protected $couponModel;
    protected $fileModel;
    public $db;
    protected $userId;
    protected $perPage;
    protected $isDashboard;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (!authCheck()) {
            redirectToUrl(langBaseUrl());
        }
        if (!isVendor() && !hasPermission('products')) {
            if ($this->generalSettings->membership_plans_system == 1) {
                redirectToUrl(generateUrl('start_selling'));
            }
            redirectToUrl(generateUrl('start_selling'));
        }

        $this->orderAdminModel = new OrderAdminModel();
        $this->orderModel = new OrderModel();
        $this->productAdminModel = new ProductAdminModel();
        $this->membershipModel = new MembershipModel();
        $this->shippingModel = new ShippingModel();
        $this->couponModel = new CouponModel();
        $this->fileModel = new FileModel();
        $this->db = db_connect();
        $this->userId = user()->id;
        $this->perPage = 15;
        $this->isDashboard = true;

        checkVendorCommissionDept();
    }

    /**
     * Index
     */
    public function index()
    {
        $data = setPageMeta(getUsername(user()));
        $data['user'] = user();
        $data["activeTab"] = 'products';

        $data['activeSalesCount'] = $this->orderAdminModel->getActiveSalesCountBySeller($this->userId);
        $data['completedSalesCount'] = $this->orderAdminModel->getCompletedSalesCountBySeller($this->userId);
        $data['totalSalesCount'] = $data['activeSalesCount'] + $data['completedSalesCount'];
        $data['balance'] = priceFormatted(user()->balance, $this->defaultCurrency->code);
        $data['productsCount'] = $this->productModel->getSellerTotalProductsCount($this->userId);
        $data['pendingProductsCount'] = $this->productModel->getSellerTotalProductsCount($this->userId, 'pending');

        $data['latestSales'] = $this->orderModel->getSalesBySellerLimited($this->userId, 6);
        $data['latestComments'] = $this->commonModel->getVendorCommentsPaginated($this->userId, 6, 0);
        $data['panelSettings'] = getPanelSettings();
        $data['latestReviews'] = $this->commonModel->getUserReviewsPaginated($this->userId, 6, 0);
        $data['salesSum'] = $this->orderAdminModel->getSalesSumByMonth($this->userId);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/index', $data);
        echo view('dashboard/includes/_footer');
    }

    /*
     * --------------------------------------------------------------------
     * Products
     * --------------------------------------------------------------------
     */

    /**
     * Add Product
     */
    public function addProduct()
    {
        $data = $this->setMetaData(trans("add_product"));
        $data['images'] = $this->fileModel->getSessProductImagesArray();
        $data["fileManagerImages"] = $this->fileModel->getUserFileManagerImages($this->userId);
        $view = !$this->membershipModel->isAllowedAddingProduct() ? 'plan_expired' : 'add_product';
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['panelSettings'] = getPanelSettings();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/' . $view, $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Product Post
     */
    public function addProductPost()
    {
        if (!$this->membershipModel->isAllowedAddingProduct()) {
            setErrorMessage(trans("msg_plan_expired"));
            redirectToBackUrl();
        }
        //validate title
        if (empty(trim(inputPost('title_' . selectedLangId()) ?? ''))) {
            setErrorMessage(trans("msg_error"));
            redirectToBackUrl();
        }
        //add product
        if ($insertId = $this->productModel->addProduct()) {
            //add product title and desc
            $this->productModel->addProductTitleDesc($insertId);
            //update slug
            $this->productModel->updateSlug($insertId);
            //add product images
            $this->fileModel->addProductImages($insertId);
            //update search index
            $this->productModel->syncProductSearchIndex($insertId);
            return redirect()->to(generateDashUrl('product', 'product_details') . '/' . $insertId);
        } else {
            setErrorMessage(trans("msg_error"));
            redirectToBackUrl();
        }
    }

    /**
     * Edit Product
     */
    public function editProduct($id)
    {
        $product = $this->productAdminModel->getProduct($id);
        if (empty($product)) {
            return redirect()->to(dashboardUrl());
        }
        if ($product->is_deleted == 1) {
            if (!hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
        }
        if ($product->user_id != $this->userId && !hasPermission('products')) {
            return redirect()->to(dashboardUrl());
        }
        $title = $product->is_draft == 1 ? trans('add_product') : trans('edit_product');
        $data = $this->setMetaData($title);
        $data['product'] = $product;
        $data['category'] = $this->categoryModel->getCategory($product->category_id);
        $data['productImages'] = $this->fileModel->getProductImages($product->id);
        $data['fileManagerImages'] = $this->fileModel->getUserFileManagerImages($this->userId);
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['panelSettings'] = getPanelSettings();

        // echo '<pre>';
        // print_r($data);
        // exit();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/edit_product', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Product Post
     */
    public function editProductPost()
    {
        $productId = inputPost('id');
        $userId = 0;
        $product = $this->productAdminModel->getProduct($productId);
        if (!empty($product)) {
            if ($product->user_id != $this->userId && !hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
            if ($this->productSettings->is_product_image_required == 1 && countItems(getProductImages($product->id)) < 1) {
                setErrorMessage(trans("error_product_image_required"));
                return redirect()->to(generateDashUrl('edit_product') . '/' . $product->id);
            }
            //validate title
            $title = inputPost('title_' . selectedLangId());
            if (empty(trim($title ?? ''))) {
                setErrorMessage(trans("msg_error"));
                redirectToBackUrl();
            }
            //check slug is unique
            $slug = $product->slug;
            if (isAdmin() || hasPermission('products')) {
                $slug = inputPost('slug');
                if (empty($slug)) {
                    $slug = strSlug($title) . '-' . $product->id;
                }
                if (!$this->productAdminModel->isProductSlugUnique($product->id, $slug)) {
                    setErrorMessage(trans("msg_product_slug_used"));
                    redirectToBackUrl();
                }
            }
            if ($this->productModel->editProduct($product, $slug)) {
                setProductAsEdited($product->id);
                //edit product title and desc
                $this->productModel->editProductTitleDesc($product->id);
                //update search index
                $this->productModel->syncProductSearchIndex($product->id);
                if ($product->is_draft == 1) {
                    return redirect()->to(generateDashUrl('product', 'product_details') . '/' . $product->id);
                } else {
                    setSuccessMessage(trans("msg_updated"));
                    resetCacheDataOnChange();
                    redirectToBackUrl();
                }
            }
        }
        setErrorMessage(trans("msg_error"));
        redirectToBackUrl();
    }

    /**
     * Edit Product Details
     */
    public function editProductDetails($id)
    {
        $product = $this->productAdminModel->getProduct($id);
        if (empty($product)) {
            return redirect()->to(dashboardUrl());
        }
        if ($product->is_deleted == 1) {
            if (!hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
        }
        if ($product->user_id != $this->userId && !hasPermission('products')) {
            return redirect()->to(dashboardUrl());
        }
        if ($this->productSettings->is_product_image_required == 1 && countItems(getProductImages($product->id)) < 1) {
            setErrorMessage(trans("error_product_image_required"));
            return redirect()->to(generateDashUrl('edit_product') . '/' . $product->id);
        }
        $category = getCategory($product->category_id);
        $title = $product->is_draft == 1 ? trans('add_product') : trans('edit_product');
        $data = $this->setMetaData($title);
        $data['product'] = $product;
        $fieldModel = new FieldModel();
        $data["customFields"] = $fieldModel->getCustomFieldsByCategory($product->category_id);

        //product options
        $productOptionsModel = new ProductOptionsModel();
        $data['initialProductData_json'] = null;
        $productOptionsData = $productOptionsModel->loadProductOptionsData($product);
        if (!empty($productOptionsData)) {
            $data['initialProductData_json'] = json_encode($productOptionsData, JSON_UNESCAPED_UNICODE);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data['initialProductData_json'] = null;
            }
        }

        $data['licenseKeys'] = $this->productModel->getProductLicenseKeys($product->id);
        $data['productVideo'] = $this->fileModel->getProductVideo($product->id);
        $data['productAudio'] = $this->fileModel->getProductAudio($product->id);
        $shippingModel = new ShippingModel();
        $data['shippingStatus'] = $this->productSettings->marketplace_shipping;
        $data['brands'] = $this->commonModel->getBrands($this->activeLang->id, $category->id ?? '');
        $data['panelSettings'] = getPanelSettings();
        if ($data['product']->listing_type == 'ordinary_listing' || $data['product']->product_type != 'physical') {
            $data['shippingStatus'] = 0;
        }
        $data['shippingDeliveryTimes'] = $shippingModel->getShippingDeliveryTimes($product->user_id);
        $shippingZones = $shippingModel->getShippingZones($product->user_id);
        $data['showShippingOptionsWarning'] = false;
        if ($data['shippingStatus'] == 1 && empty($shippingZones)) {
            $data['showShippingOptionsWarning'] = true;
        }
        $data['commissionRate'] = $this->orderModel->getProductCommissionRate($product->id);
        $data['productImages'] = $this->fileModel->getProductImages($product->id, true);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/edit_product_details', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Product Details Post
     */
    public function editProductDetailsPost()
    {
        $productId = inputPost('id');
        $product = $this->productAdminModel->getProduct($productId);
        if (empty($product)) {
            return redirect()->to(dashboardUrl());
        }
        if ($product->is_deleted == 1) {
            if (!hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
        }
        if ($product->user_id != $this->userId && !hasPermission('products')) {
            return redirect()->to(dashboardUrl());
        }

        //save product options
        if (!empty(inputPost('is_options_updated'))) {
            $optionsDataJson = inputPost('submitted_options_data') ?: '[]';
            $variantsDataJson = inputPost('submitted_variants_data') ?: '[]';
            $optionsToSave = json_decode($optionsDataJson, true);
            $variantsToSave = json_decode($variantsDataJson, true);

            //basic validation for JSON decoding
            $isValidJson = true;
            if (json_last_error() !== JSON_ERROR_NONE && $optionsDataJson !== '[]') {
                $isValidJson = false;
            }
            if (json_last_error() !== JSON_ERROR_NONE && $variantsDataJson !== '[]') {
                $isValidJson = false;
            }
            if ($isValidJson) {
                $productOptionsModel = new ProductOptionsModel();
                $productOptionsModel->saveProductOptionsData($product->id, $optionsToSave, $variantsToSave);
            }
        }

        // Process Bundle Items
        $isBundle = !empty(inputPost('is_bundle')) ? 1 : 0;
        $bundleItems = inputPost('bundle_items');
        if ($isBundle && is_array($bundleItems)) {
            $this->bundleModel->saveBundleComponents($product->id, $bundleItems);
        } elseif (!$isBundle) {
            $this->db->table('product_bundles')->where('bundle_product_id', $product->id)->delete();
            $this->db->table('products')->where('id', $product->id)->update(['is_bundle' => 0]);
        }

        //check digital file
        $digitalFileUploaded = true;
        if ($product->product_type == 'digital' && $product->listing_type != 'license_key') {
            if ($this->productSettings->digital_external_link == 1) {
                if (empty($this->fileModel->getProductDigitalFile($product->id)) && empty(trim(inputPost('digital_file_download_link') ?? ''))) {
                    $digitalFileUploaded = false;
                }
            } else {
                if (empty($this->fileModel->getProductDigitalFile($product->id))) {
                    $digitalFileUploaded = false;
                }
            }
        }
        if ($digitalFileUploaded == false) {
            setErrorMessage(trans("digital_file_required"));
            redirectToBackUrl();
        }

        if ($this->productModel->editProductDetails($product->id)) {
            $this->productModel->syncProductSearchIndex($product->id);
            setProductAsEdited($product->id);
            //edit custom fields
            $this->productModel->updateProductCustomFields($product->id);
            //reset cache
            resetCacheDataOnChange();
            if ($product->is_draft != 1) {
                setSuccessMessage(trans("msg_updated"));
                redirectToBackUrl();
            } else {
                //if draft
                if (inputPost('submit') == 'save_as_draft') {
                    setSuccessMessage(trans("draft_added"));
                } else {
                    if ($this->generalSettings->approve_before_publishing == 1 && !isAdmin()) {
                        setSuccessMessage(trans("product_added") . " " . trans("product_approve_published") . " <a href='" . generateProductUrl($product) . "' class='link-view-product'>" . trans("view_product") . "</a>");
                    } else {
                        setSuccessMessage(trans("product_added") . " <a href='" . generateProductUrl($product) . "' class='link-view-product' target='_blank'>" . trans("view_product") . "</a>");
                    }
                    //send email
                    if (getEmailOptionStatus($this->generalSettings, 'new_product') == 1) {
                        $emailData = [
                            'email_type' => 'new_product',
                            'email_address' => $this->generalSettings->mail_options_account,
                            'email_subject' => trans("email_text_new_product"),
                            'template_path' => 'email/main',
                            'email_data' => serialize([
                                'content' => trans("email_text_see_product"),
                                'url' => generateProductUrl($product),
                                'buttonText' => trans("view_product")
                            ])
                        ];
                        addToEmailQueue($emailData);
                    }
                }
                return redirect()->to(generateDashUrl('add_product'));
            }
        } else {
            setErrorMessage(trans('msg_error'));
            redirectToBackUrl();
        }
    }

    /**
     * Products
     */
    public function products()
    {
        $st = inputGet('st');
        $status = 'active';
        $page = trans("products");
        if (!empty($st)) {
            if ($st == 'pending') {
                $status = 'pending';
                $page = trans("pending_products");
            }
            if ($st == 'hidden') {
                $status = 'hidden';
                $page = trans("hidden_products");
            }
            if ($st == 'sold') {
                $status = 'sold';
                $page = trans("sold_products");
            }
            if ($st == 'draft') {
                $status = 'draft';
                $page = trans("drafts");
            }
        }
        $data = $this->setMetaData($page);
        $data['numRows'] = $this->productModel->getVendorProductsCount($this->userId, $status);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['products'] = $this->productModel->getVendorProductsPaginated($this->userId, $status, $this->perPage, $data['pager']->offset);
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['panelSettings'] = getPanelSettings();
        $data['productListStatus'] = $status;
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/products', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Duplicate Product
     */
    public function duplicateProductPost()
    {
        $productId = inputPost('product_id');

        if ($this->productModel->duplicateProduct($productId)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        exit();
    }

    /**
     * Delete Product
     */
    public function deleteProduct()
    {
        $id = inputPost('id');
        $userId = 0;
        $result = false;
        $product = $this->productAdminModel->getProduct($id);
        if (!empty($product)) {
            $userId = $product->user_id;
            if (hasPermission('products') || $this->userId == $userId) {
                if ($product->is_draft == 1) {
                    $result = $this->productAdminModel->deleteProductPermanently($id);
                } else {
                    $result = $this->productAdminModel->deleteProduct($id);
                }
            }
            if ($result) {
                setSuccessMessage(trans("msg_deleted"));
                resetCacheDataOnChange();
            } else {
                setErrorMessage(trans("msg_error"));
            }
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Barcode Print
     */
    public function productBarcode($id, $qty)
    {
        $product = $this->productModel->getActiveProduct($id);

        $data = [
            'product' => $product,
            'qty'     => $qty
        ];

        // echo view('dashboard/includes/_header');
        echo view('dashboard/product/_barcode_print', $data);
        // echo view('dashboard/includes/_footer');
    }


    //get subcategories
    public function getSubCategories()
    {
        $parentId = inputPost('parent_id');
        $data = ['result' => 0];
        if (!empty($parentId)) {
            $subCategories = $this->categoryModel->getSubCategoriesByParentId($parentId);
            $options = '';
            if (!empty($subCategories)) {
                foreach ($subCategories as $item) {
                    $options .= '<option value="' . $item->id . '">' . esc($item->cat_name) . '</option>';
                }
            }
        }
        if (!empty($options)) {
            $data = ['result' => 1, 'options' => $options];
        }
        return jsonResponse($data);
    }

    /*
     * --------------------------------------------------------------------
     * License Keys
     * --------------------------------------------------------------------
     */

    /**
     * Add License Keys
     */
    //
    public function addLicenseKeys()
    {
        $productId = inputPost('product_id');
        $product = getProduct($productId);
        $data = ['result' => 0];
        if (!empty($product)) {
            if ($this->userId == $product->user_id || hasPermission('products')) {
                $this->productModel->addLicenseKeys($productId);
                $data = [
                    'result' => 1,
                    'message' => trans("msg_add_license_keys")
                ];
            }
        }
        return jsonResponse($data);
    }

    //delete license key
    public function deleteLicenseKey()
    {
        $id = inputPost('id');
        $productId = inputPost('product_id');
        $product = getProduct($productId);
        if (!empty($product)) {
            if ($this->userId == $product->user_id || hasPermission('products')) {
                $this->productModel->deleteLicenseKey($id);
            }
        }
        return jsonResponse();
    }

    //load license keys list
    public function loadLicenseKeysList()
    {
        $productId = inputPost('product_id');
        $vars['product'] = getProduct($productId);
        if (!empty($vars['product'])) {
            if ($this->userId == $vars['product']->user_id || hasPermission('products')) {
                $vars['licenseKeys'] = $this->productModel->getProductLicenseKeys($productId);
                $data = [
                    'result' => 1,
                    'htmlContent' => view('dashboard/product/license/_license_keys_list', $vars)
                ];
            }
        } else {
            $data = ['result' => 0];
        }
        return jsonResponse($data);
    }

    /*
     * --------------------------------------------------------------------
     * Bulk Product Upload
     * --------------------------------------------------------------------
     */

    /**
     * Bulk Product Upload
     */
    public function bulkProductUpload()
    {
        $data = $this->setMetaData(trans("bulk_product_upload"));
        $view = !$this->membershipModel->isAllowedAddingProduct() ? 'plan_expired' : 'bulk_product_upload';
        if (!hasPermission('products') && $this->generalSettings->vendor_bulk_product_upload != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data['parentCategories'] = $this->categoryModel->getParentCategories();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/' . $view, $data);
        echo view('dashboard/includes/_footer');
    }

    /*
     * --------------------------------------------------------------------
     * Promote
     * --------------------------------------------------------------------
     */

    /**
     * Promote Product Post
     */
    public function promoteProductPost()
    {
        $productId = inputPost('product_id');
        $product = getProduct($productId);
        if (!empty($product)) {
            if ($product->user_id != $this->userId) {
                setErrorMessage(trans("invalid_attempt"));
                redirectToBackUrl();
            }
            $planType = inputPost('plan_type');
            $pricePerDay = numToDecimal($this->paymentSettings->price_per_day);
            $pricePerMonth = numToDecimal($this->paymentSettings->price_per_month);
            $dayCount = inputPost('day_count');
            $monthCount = inputPost('month_count');
            $totalAmount = 0;
            if ($planType == 'daily') {
                $totalAmount = $dayCount * $pricePerDay;
                $purchasedPlan = trans("daily_plan") . ' (' . $dayCount . ' ' . trans("days") . ')';
            }
            if ($planType == 'monthly') {
                $dayCount = $monthCount * 30;
                $totalAmount = $monthCount * $pricePerMonth;
                $purchasedPlan = trans("monthly_plan") . ' (' . $dayCount . ' ' . trans("days") . ')';
            }

            $serviceData = new \stdClass();
            $serviceData->paymentType = 'promote';
            $serviceData->paymentName = trans("product_promoting_payment");
            $serviceData->planType = $planType;
            $serviceData->productId = $productId;
            $serviceData->dayCount = $dayCount;
            $serviceData->monthCount = $monthCount;
            $serviceData->purchasedPlan = $purchasedPlan;
            $serviceData->subtotal = numToDecimal($totalAmount);
            $serviceData->grandTotal = numToDecimal($totalAmount);
            $serviceData->currency = $this->defaultCurrency->code;

            if ($this->paymentSettings->free_product_promotion == 1) {
                $promoteModel = new PromoteModel();
                $promoteModel->addToPromotedProducts($serviceData);
                redirectToBackUrl();
            } else {

                $data = new \stdClass();
                $data->planType = $planType;
                $data->productId = $productId;
                $data->dayCount = $dayCount;
                $data->monthCount = $monthCount;
                $data->purchasedPlan = $purchasedPlan;

                $checkoutModel = new CheckoutModel();
                $checkoutModel->setServicePaymentSession('promote', trans("product_promoting_payment"), $totalAmount, $data);

                return redirect()->to(generateUrl('cart', 'payment_method'));
            }
        }
        setErrorMessage(trans("invalid_attempt"));
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Sales
     * --------------------------------------------------------------------
     */

    /**
     * Label print
     */
    public function labelprint($orderNumber)
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }


        $type = inputGet('type');
        

        $orderModel = new OrderModel();
        $data['order'] = $orderModel->getOrderByOrderNumber($orderNumber);
        if (empty($data['order'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['invoice'] = $orderModel->getInvoiceByOrderNumber($orderNumber);
        if (empty($data['invoice'])) {
            $orderModel->addInvoice($data['order']->id);
        }

        if (empty($data['invoice'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['invoiceItems'] = unserializeData($data['invoice']->invoice_items);
        $data['orderProducts'] = $orderModel->getOrderItems($data['order']->id);

        
            // echo '<pre>';
            // print_r($data);
            // exit();
        if ($type == 'labelprint') {
            echo view('dashboard/sales/_label_print', $data);
        }
    }
    public function productupdateStock()
    {
        // Allow only AJAX
        // if (!$this->request->isAJAX()) {
        //     return $this->response->setJSON([
        //         'status' => 0,
        //         'message' => 'Invalid request'
        //     ]);
        // }

        $data = $this->request->getJSON(true);

        $productId = (int) ($data['product_id'] ?? 0);
        $qty       = (int) ($data['qty'] ?? 0);

        if ($productId <= 0 || $qty <= 0) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'Invalid data'
            ]);
        }

        $product = $this->productModel->getActiveProduct($productId);

        if (!$product) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'Product not found'
            ]);
        }

        // ✅ ADD stock
        $newStock = $product->stock + $qty;

        $this->db->table('products')
            ->where('id', $productId)
            ->update([
                'stock' => $newStock
            ]);

        return $this->response->setJSON([
            'status' => 1,
            'new_stock' => $newStock,
            'message' => 'Stock added successfully'
        ]);
    }

    /**
     * Sales
     */
    public function sales()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $st = inputGet('st');
        $page = 'sales';
        $status = 'active';
        if ($st == 'completed') {
            $page = 'completed_sales';
            $status = 'completed';
        } elseif ($st == 'cancelled') {
            $page = 'cancelled_sales';
            $status = 'cancelled';
        }
        $data = $this->setMetaData(trans($page));
        $data['page'] = $page;
        $data['numRows'] = $this->orderModel->getSalesCount($status, $this->userId);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['sales'] = $this->orderModel->getSalesPaginated($status, $this->userId, $this->perPage, $data['pager']->offset);
        $data['panelSettings'] = getPanelSettings();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/sales/sales', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Sale
     */
    public function sale($orderNumber)
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("sale"));
        $data['order'] = $this->orderModel->getOrderByOrderNumber($orderNumber);
        if (empty($data['order'])) {
            return redirect()->to(dashboardUrl());
        }
        if (!$this->orderModel->checkOrderSeller($data['order']->id)) {
            return redirect()->to(dashboardUrl());
        }
        $data['orderProducts'] = $this->orderModel->getOrderItems($data['order']->id);
        $data['panelSettings'] = getPanelSettings();
         // echo '<pre>';
                        // print_r($data);
                        // exit();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/sales/sale', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Update Order Product Status Post
     */
    public function updateOrderProductStatusPost()
    {
        $id = inputPost('id');
        $orderProduct = $this->orderModel->getOrderProduct($id);
        if ($this->userId != $orderProduct->seller_id) {
            return redirect()->to(dashboardUrl());
        }
        if (!empty($orderProduct)) {
            if ($this->orderModel->updateOrderProductStatus($id)) {
                $this->orderAdminModel->updateOrderStatusIfCompleted($orderProduct->order_id);
            }
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Quote Requests
     * --------------------------------------------------------------------
     */

    /**
     * Quote Requests
     */
    public function quoteRequests()
    {
        if ($this->generalSettings->bidding_system != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("quote_requests"));
        $biddingModel = new BiddingModel();
        $data['numRows'] = $biddingModel->getVendorQuoteRequestsCount($this->userId);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['quoteRequests'] = $biddingModel->getVendorQuoteRequestsPaginated($this->userId, $this->perPage, $data['pager']->offset);
        $data['panelSettings'] = getPanelSettings();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/quote_requests', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Submit Quote
     */
    public function submitQuotePost()
    {
        $biddingModel = new BiddingModel();
        $id = inputPost('id');
        $quoteRequest = $biddingModel->getQuoteRequest($id);
        if ($biddingModel->submitQuote($quoteRequest)) {
            //send email
            $buyer = getUser($quoteRequest->buyer_id);
            if (!empty($buyer) && getEmailOptionStatus($this->generalSettings, 'bidding_system') == 1) {
                $emailData = [
                    'email_type' => 'quote',
                    'email_address' => $buyer->email,
                    'email_subject' => trans("quote_request"),
                    'template_path' => 'email/main',
                    'email_data' => serialize([
                        'content' => trans("your_quote_request_replied") . "<br>" . trans("quote") . ": " . "<strong>#" . $quoteRequest->id . "</strong>",
                        'url' => generateUrl('quote_requests'),
                        'buttonText' => trans("view_details")
                    ])
                ];
                addToEmailQueue($emailData);
            }
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Cash On Delivery
     * --------------------------------------------------------------------
     */

    /**
     * Cash On Delivery
     */
    public function cashOnDelivery()
    {
        if ($this->paymentSettings->cash_on_delivery_enabled != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("cash_on_delivery"));
        $earningsModel = new EarningsModel();
        $data['numRows'] = $earningsModel->getCodEarningsCount($this->userId);
        $data['pager'] = paginate($this->baseVars->perPage, $data['numRows']);
        $data['earnings'] = $earningsModel->getCodEarningsPaginated($this->userId, $this->baseVars->perPage, $data['pager']->offset);
        $data['panelSettings'] = getPanelSettings();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/cash_on_delivery', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Cash On Delivery Settings Post
     */
    public function cashOnDeliverySettingsPost()
    {
        $profileModel = new ProfileModel();
        if ($profileModel->updateCashOnDelivery()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        return redirect()->to(generateDashUrl('cash_on_delivery'));
    }


    /*
     * --------------------------------------------------------------------
     * Coupons
     * --------------------------------------------------------------------
     */

    /**
     * Coupons
     */
    public function coupons()
    {
        $data = $this->setMetaData(trans("coupons"));
        $data['numRows'] = $this->couponModel->getVendorCouponsCount($this->userId);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['coupons'] = $this->couponModel->getVendorCouponsPaginated($this->userId, $this->perPage, $data['pager']->offset);
        $data['panelSettings'] = getPanelSettings();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/coupons', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Coupon
     */
    public function addCoupon()
    {
        $data = $this->setMetaData(trans("add_coupon"));
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/add_coupon', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Coupon Post
     */
    public function addCouponPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('coupon_code', trans("coupon_code"), 'required|max_length[49]');
        $val->setRule('discount_rate', trans("discount_rate"), 'required');
        $val->setRule('coupon_count', trans("number_of_coupons"), 'required');
        $val->setRule('expiry_date', trans("expiry_date"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $code = inputPost('coupon_code');
            if (!empty($this->couponModel->getCouponByCode($code))) {
                setErrorMessage(trans("msg_coupon_code_added_before"));
                $this->session->setFlashdata('selectedProductsIds', $this->couponModel->getSelectedProductsArray());
                return redirect()->back()->withInput();
            }
            if ($this->couponModel->addCoupon()) {
                setSuccessMessage(trans("msg_added"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Edit Coupon
     */
    public function editCoupon($id)
    {
        $data = $this->setMetaData(trans("edit_coupon"));
        $data['coupon'] = $this->couponModel->getCoupon($id);
        if (empty($data['coupon'])) {
            return redirect()->to(generateDashUrl('coupons'));
        }
        if ($data['coupon']->seller_id != $this->userId) {
            return redirect()->to(generateDashUrl('coupons'));
        }

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/edit_coupon', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Coupon Post
     */
    public function editCouponPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('coupon_code', trans("coupon_code"), 'required|max_length[49]');
        $val->setRule('discount_rate', trans("discount_rate"), 'required');
        $val->setRule('coupon_count', trans("number_of_coupons"), 'required');
        $val->setRule('expiry_date', trans("expiry_date"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $couponId = inputPost('id');
            $coupon = $this->couponModel->getCoupon($couponId);
            if (empty($coupon) || ($coupon->seller_id != $this->userId)) {
                return redirect()->to(generateDashUrl('coupons'));
            }
            $code = inputPost('coupon_code');
            $couponByCode = $this->couponModel->getCouponByCode($code);
            if (!empty($couponByCode) && $couponByCode->id != $coupon->id) {
                setErrorMessage(trans("msg_coupon_code_added_before"));
                redirectToBackUrl();
            }
            if ($this->couponModel->editCoupon($couponId)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Delete Coupon Post
     */
    public function deleteCouponPost()
    {
        $id = inputPost('id');
        $coupon = $this->couponModel->getCoupon($id);
        if (empty($coupon)) {
            exit();
        }
        if ($coupon->seller_id != $this->userId) {
            exit();
        }
        if ($this->couponModel->deleteCoupon($coupon)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        exit();
    }

    /**
     * Coupon Products
     */
    public function couponProducts($id)
    {
        $coupon = $this->couponModel->getCoupon($id);
        if (empty($coupon) || $coupon->seller_id != $this->userId) {
            return redirect()->to(generateDashUrl('coupons'));
        }
        $data = $this->setMetaData(trans("coupon") . " " . trans("select_products"));
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['sellerCategories'] = $this->categoryModel->getSellerCategoriesResultArray($this->userId);
        if (!empty($data['sellerCategories'])) {
            usort($data['sellerCategories'], function ($a, $b) {
                return strcmp($a['slug'], $b['slug']);
            });
        }
        $this->perPage = 30;
        $data['numRows'] = $this->productModel->getSellerProductsCouponCount($this->userId);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['products'] = [];
        if ($data['numRows'] > 0) {
            $data['products'] = $this->productModel->getSellerProductsCouponPaginated($this->userId, $coupon->id, $this->perPage, $data['pager']->offset);
        }
        $data['coupon'] = $coupon;

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/coupon_products', $data);
        echo view('dashboard/includes/_footer');
    }

    /*
     * --------------------------------------------------------------------
     * Refund
     * --------------------------------------------------------------------
     */

    /**
     * Refund Requests
     */
    public function refundRequests()
    {
        if ($this->generalSettings->refund_system != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("refund_requests"));
        $data['numRows'] = $this->orderModel->getRefundRequestCount($this->userId, 'seller');
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['refundRequests'] = $this->orderModel->getRefundRequestsPaginated($this->userId, 'seller', $this->perPage, $data['pager']->offset);
        $data['panelSettings'] = getPanelSettings();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/refund/refund_requests', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Refund
     */
    public function refund($id)
    {
        if ($this->generalSettings->refund_system != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("refund"));
        $data['refundRequest'] = $this->orderModel->getRefundRequest($id);
        if (empty($data['refundRequest']) || $data['refundRequest']->seller_id != $this->userId) {
            return redirect()->to(generateDashUrl('refund_requests'));
        }
        $data['product'] = getOrderProduct($data['refundRequest']->order_product_id);
        if (empty($data['product'])) {
            return redirect()->to(generateDashUrl('refund_requests'));
        }
        $data['messages'] = $this->orderModel->getRefundMessages($id);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/refund/refund', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Approve or Decline Refund Request
     */
    public function approveDeclineRefund()
    {
        if ($this->orderModel->approveDeclineRefund()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Payment History
     * --------------------------------------------------------------------
     */

    /**
     * Payments
     */
    public function payments()
    {
        $payment = inputGet('payment');
        if ($payment == 'membership') {
            if ($this->generalSettings->membership_plans_system != 1) {
                return redirect()->to(dashboardUrl());
            }
            $data = $this->setMetaData(trans("membership_payments"));
            $data['numRows'] = $this->membershipModel->getMembershipTransactionsCount($this->userId);
            $data['pager'] = paginate($this->perPage, $data['numRows']);
            $data['transactions'] = $this->membershipModel->getMembershipTransactionsPaginated($this->perPage, $data['pager']->offset, $this->userId);
            echo view('dashboard/includes/_header', $data);
            echo view('dashboard/payments/membership_payments', $data);
            echo view('dashboard/includes/_footer');
        } elseif ($payment == 'promotion') {
            $data = $this->setMetaData(trans("promotion_payments"));
            $promoteModel = new PromoteModel();
            $data['numRows'] = $promoteModel->getTransactionsCount($this->userId);
            $data['pager'] = paginate($this->perPage, $data['numRows']);
            $data['transactions'] = $promoteModel->getTransactionsPaginated($this->userId, $this->perPage, $data['pager']->offset);
            echo view('dashboard/includes/_header', $data);
            echo view('dashboard/payments/promotion_payments', $data);
            echo view('dashboard/includes/_footer');
        } else {
            return redirect()->to(dashboardUrl());
        }
    }

    /**
     * Affiliate Program
     */
    public function affiliateProgram()
    {
        if ($this->affiliateSettings->status != 1 || $this->affiliateSettings->type != 'seller_based') {
            return redirect()->to(dashboardUrl());
        }

        $data['title'] = trans("affiliate_program");
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['categories'] = $this->categoryModel->getSellerCategories($this->userId, null);
        $data['categoryIds'] = array();
        if (!empty($data['categories']) && !empty($data['categories'][0])) {
            foreach ($data['categories'] as $item) {
                array_push($data['categoryIds'], $item->id);
            }
        }
        $data['selectedCategories'] = explode(',', '1,2,3');
        if (empty($data['selectedCategories'])) {
            $data['selectedCategories'] = array();
        }

        $data['selectedProducts'] = array();
        if (!empty($selectedProducts)) {
            foreach ($selectedProducts as $item) {
                array_push($data['selectedProducts'], $item->product_id);
            }
        }

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/affiliate_program', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Affiliate Program Post
     */
    public function affiliateProgramPost()
    {
        if ($this->affiliateSettings->status != 1 || $this->affiliateSettings->type != 'seller_based') {
            return redirect()->to(dashboardUrl());
        }

        if ($this->authModel->updateAffiliateSettings()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Add Remove Affiliate Product
     */
    public function addRemoveAffiliateProductPost()
    {
        $productId = inputPost('product_id');
        if ($this->productAdminModel->addRemoveAffiliateProduct($productId)) {
            setSuccessMessage(trans("msg_updated"));
            resetCacheDataOnChange();
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Comments & Reviews
     * --------------------------------------------------------------------
     */

    /**
     * Comments
     */
    public function comments()
    {
        if ($this->generalSettings->product_comments != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("comments"));
        $data['numRows'] = $this->commonModel->getVendorCommentsCount($this->userId);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['comments'] = $this->commonModel->getVendorCommentsPaginated($this->userId, $this->perPage, $data['pager']->offset);
        $data['panelSettings'] = getPanelSettings();
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/comments', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Reviews
     */
    public function reviews()
    {
        if ($this->generalSettings->reviews != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("reviews"));
        $data['numRows'] = $this->commonModel->getUserReviewsCount($this->userId);
        $data['pager'] = paginate($this->perPage, $data['numRows']);
        $data['reviews'] = $this->commonModel->getUserReviewsPaginated($this->userId, $this->perPage, $data['pager']->offset);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/reviews', $data);
        echo view('dashboard/includes/_footer');
    }

    /*
     * --------------------------------------------------------------------
     * Shop Settings
     * --------------------------------------------------------------------
     */

    /**
     * Shop Settings
     */
    public function shopSettings()
    {
        $data = $this->setMetaData(trans("shop_settings"));
        $data['userPlan'] = $this->membershipModel->getUserPlanByUserId($this->userId);
        $data['daysLeft'] = $this->membershipModel->getUserPlanRemainingDaysCount($data['userPlan']);
        $data['adsLeft'] = $this->membershipModel->getUserPlanRemainingAdsCount($data['userPlan']);
        $data['states'] = array();
        $data['cities'] = array();
        $data['panelSettings'] = getPanelSettings();
        $locationModel = new LocationModel();
        if (!empty(user()->country_id)) {
            $data['states'] = $locationModel->getStatesByCountry(user()->country_id);
        }
        if (!empty(user()->state_id)) {
            $data['cities'] = $locationModel->getCitiesByState(user()->state_id);
        }

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shop_settings', $data);
        echo view('dashboard/includes/_footer');

    }

    /**
     * Shop Settings Post
     */
    public function shopSettingsPost()
    {
        $submit = inputPost('submit');
        $profileModel = new ProfileModel();
        if ($submit == 'vat') {
            if ($profileModel->updateVendorVatRates()) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        } else {
            $shopName = '';
            if ($submit == 'update') {
                $shopName = removeSpecialCharacters(inputPost('shop_name'));
                if (!$this->authModel->isUniqueUsername($shopName, $this->userId)) {
                    setErrorMessage(trans("msg_shop_name_unique_error"));
                    return redirect()->to(generateDashUrl('shop_settings'));
                }
            }
            if ($profileModel->updateShopSettings($shopName)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        return redirect()->to(generateDashUrl('shop_settings'));
    }

    /**
     * Shop Policies
     */
    public function shopPolicies()
    {
        $data = $this->setMetaData(trans("shop_policies"));
        $pageModel = new PageModel();
        $data['pages'] = $pageModel->getVendorPagesByUserId($this->userId);
        if (empty($data['pages'])) {
            $pageModel->addVendorPages($this->userId);
            $data['pages'] = $pageModel->getVendorPagesByUserId($this->userId);
        }
        if (empty($data['pages'])) {
            return redirect()->to(dashboardUrl());
        }
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shop_policies', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Shop Policies Post
     */
    public function shopPoliciesPost()
    {
        $pageModel = new PageModel();
        if ($pageModel->editVendorPages($this->userId)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        return redirect()->to(generateDashUrl('shop_policies'));
    }

    /*
     * --------------------------------------------------------------------
     * Shipping Settings
     * --------------------------------------------------------------------
     */

    /**
     * Shipping Settings
     */
    public function shippingSettings()
    {
        if (!$this->baseVars->isSaleActive || $this->generalSettings->physical_products_system != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("shipping_settings"));
        $data['shippingZones'] = $this->shippingModel->getShippingZones($this->userId);
        $data['shippingDeliveryTimes'] = $this->shippingModel->getShippingDeliveryTimes($this->userId, 'DESC');
        $data['panelSettings'] = getPanelSettings();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shipping/shipping_settings', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Shipping Zone
     */
    public function addShippingZone()
    {
        $data = $this->setMetaData(trans("add_shipping_zone"));
        $data['continents'] = getAppDefault('continents');
        $data['countries'] = $this->locationModel->getCountries();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shipping/add_shipping_zone', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Shipping Zone Post
     */
    public function addShippingZonePost()
    {
        $zoneId = $this->shippingModel->addShippingZone();
        if (!empty($zoneId)) {
            setSuccessMessage(trans("msg_added"));
            return redirect()->to(generateDashUrl('edit_shipping_zone') . '/' . $zoneId);
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Zone
     */
    public function editShippingZone($id)
    {
        $data = $this->setMetaData(trans("edit_shipping_zone"));
        $data['shippingZone'] = $this->shippingModel->getShippingZone($id);
        if (empty($data['shippingZone']) || user()->id != $data['shippingZone']->user_id) {
            return redirect()->to(generateDashUrl('shipping_settings'));
        }

        $data['continents'] = getAppDefault('continents');
        $data['countries'] = $this->locationModel->getCountries();
        $data['methods'] = $this->shippingModel->getShippingMethodsByZone($data['shippingZone']->id);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shipping/edit_shipping_zone', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Shipping Zone Post
     */
    public function editShippingZonePost()
    {
        $zoneId = inputPost('zone_id');

        $shippingZone = $this->shippingModel->getShippingZone($zoneId);
        if (empty($shippingZone) || user()->id != $shippingZone->user_id) {
            return redirect()->to(generateDashUrl('shipping_settings'));
        }

        if ($this->shippingModel->editShippingZone($zoneId)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Add Shipping Method Post
     */
    public function addShippingMethodPost()
    {
        $zoneId = inputPost('zone_id');
        $shippingMethod = inputPost('shipping_method');
        $shippingZone = $this->shippingModel->getShippingZone($zoneId);
        if (empty($shippingZone) || user()->id != $shippingZone->user_id) {
            redirectToBackUrl();
        }

        if ($this->shippingModel->addShippingMethod($zoneId, $shippingMethod)) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Method Post
     */
    public function editShippingMethodPost()
    {
        $methodId = inputPost('method_id');

        if ($this->shippingModel->editShippingMethod($methodId)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Shipping Location
     */
    public function deleteShippingLocationPost()
    {
        $id = inputPost('id');
        $this->shippingModel->deleteShippingLocation($id);
        return jsonResponse();
    }

    //select shipping method
    public function selectShippingMethod()
    {
        $selectedOption = inputPost('selected_option');
        $vars = ['selectedOption' => $selectedOption, 'optionUniqueId' => uniqid()];
        $htmlContent = view('dashboard/shipping/_response_shipping_method', $vars);
        $data = [
            'result' => 1,
            'htmlContent' => $htmlContent
        ];
        return jsonResponse($data);
    }

    /**
     * Add Shipping Delivery Time Post
     */
    public function addShippingDeliveryTimePost()
    {
        if ($this->shippingModel->addShippingDeliveryTime()) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Delivery Time Post
     */
    public function editShippingDeliveryTimePost()
    {
        $id = inputPost('id');
        if ($this->shippingModel->editShippingDeliveryTime($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Shipping Method
     */
    public function deleteShippingMethodPost()
    {
        $id = inputPost('id');
        $this->shippingModel->deleteShippingMethod($id);
        return jsonResponse();
    }

    /**
     * Delete Shipping Delivery Time
     */
    public function deleteShippingDeliveryTimePost()
    {
        $id = inputPost('id');
        if ($this->shippingModel->deleteShippingDeliveryTime($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Delete Shipping Zone
     */
    public function deleteShippingZonePost()
    {
        $id = inputPost('id');
        $this->shippingModel->deleteShippingZone($id);
    }

    //set meta data
    private function setMetaData($title)
    {
        return [
            'title' => $title,
            'description' => $title . ' - ' . $this->baseVars->appName,
            'keywords' => $title . ',' . $this->baseVars->appName,
        ];
    }

    /**
     * Export Table Data Post
     */
    public function exportTableDataPost()
    {
        $langId = inputPost('lang_id');
        $language = getLanguage($langId);
        $isRTL = false;
        if (!empty($language)) {
            setContextValue('activeLang', $language);
            if ($language->text_direction == 'rtl') {
                $isRTL = true;
            }
        }
        $dataExportType = inputPost('data_export_type');
        $dataExportFileType = inputPost('data_export_file_type');
        if ($dataExportFileType != 'excel' && $dataExportFileType != 'csv' && $dataExportFileType != 'xml') {
            $dataExportFileType = 'excel';
        }
        $fileName = '';
        $fields = array();
        $rows = array();
        if ($dataExportType == 'vendor_products') {
            $list = inputPost('st');
            $model = new ProductModel();
            $fileName = 'Products';
            $fields = ['id', 'title', 'slug', 'sku', 'listing_type', 'product_type', 'category_id', 'category_name'];
            if (!empty($this->generalSettings->promoted_products)) {
                array_push($fields, 'is_promoted');
                array_push($fields, 'promote_start_date');
                array_push($fields, 'promote_end_date');
                array_push($fields, 'purchased_plan');
            }
            $fields = array_merge($fields, ['is_special_offer', 'stock_status', 'price', 'price_discounted', 'currency', 'discount_rate', 'vat_rate', 'pageviews', 'demo_url', 'external_link', 'rating', 'is_free_product', 'visibility', 'status', 'location', 'short_description', 'description', 'tags', 'images', 'date']);
            $rows = $model->getVendorProductsExport($this->userId, $list);
        } elseif ($dataExportType == 'vendor_sales') {
            $list = inputPost('st');
            $status = 'active';
            if ($list == 'completed') {
                $list = 'completed_sales';
                $status = 'completed';
            } elseif ($list == 'cancelled') {
                $list = 'cancelled_sales';
                $status = 'cancelled';
            } else {
                $list = 'sales';
            }
            $model = new OrderModel();
            $fileName = trans($list);
            $fields = ['sale', 'total', 'currency', 'payment_status', 'status', 'date'];
            $rows = $model->getSalesExport($status, $this->userId);
        }

        $export = new Export($fields, $rows, $fileName, $dataExportType, $isRTL);
        if ($dataExportFileType == 'excel') {
            $export->exportAsExcel();
        } elseif ($dataExportFileType == 'csv') {
            $export->exportAsCsv();
        } elseif ($dataExportFileType == 'xml') {
            $export->exportAsXml();
        }
        redirectToBackUrl();
    }

    /**
     * Orders
     */
    public function orders()
    {
        $data['title'] = trans("orders");
        $sellerId = (user()->role_id != 1) ? $this->userId : null;
        $numRows = $this->orderAdminModel->getOrdersCount($sellerId);
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['orders'] = $this->orderAdminModel->getOrdersPaginated($this->perPage, $data['pager']->offset, $sellerId);
        $data['panelSettings'] = getPanelSettings();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/order/orders', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Order Details
     */
    public function orderDetails($id)
    {
        // checkPermission('orders');
        $data['title'] = trans("order");

        $data['order'] = getOrder($id);
        // if (empty($data['order'])) {
        //     return redirect()->to(adminUrl('orders'));
        // }
        $data['orderProducts'] = $this->orderAdminModel->getOrderItems($id);
        $data['transaction'] = $this->orderAdminModel->getTransactionByOrderId($id);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/order/order_details', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Transactions
     */
    public function transactions()
    {
        $data['title'] = trans("transactions");
        $sellerId = (user()->role_id != 1) ? $this->userId : null;
        $numRows = $this->orderAdminModel->getTransactionsCount($sellerId);
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['transactions'] = $this->orderAdminModel->getTransactionsPaginated($this->perPage, $data['pager']->offset, $sellerId);
        $data['panelSettings'] = getPanelSettings();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/order/transactions', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * POS Orders List
     */
    public function posbb()
    {
        $db   = \Config\Database::connect();
        $data = $this->setMetaData('POS Orders');
        $data['activeTab'] = 'pos-sales';

        $numRows = $db->table('pos_orders')
            ->where('seller_id', $this->userId)
            ->countAllResults();

        $data['numRows'] = $numRows;
        $data['pager']   = paginate($this->perPage, $numRows);
        $data['orders']  = $db->table('pos_orders')
            ->where('seller_id', $this->userId)
            ->orderBy('id', 'DESC')
            ->limit($this->perPage, $data['pager']->offset)
            ->get()->getResult();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/pos_orders', $data);
        echo view('dashboard/includes/_footer');
    }

    public function pos()
    {
        $db   = \Config\Database::connect();
        $data = $this->setMetaData('POS Orders');
        $data['activeTab'] = 'pos-sales';

        // ✅ Get filters from GET
        $search             = trim($this->request->getGet('q'));
        $paymentStatus      = $this->request->getGet('payment_status');
        $fulfillmentStatus  = $this->request->getGet('fulfillment_status');

        $data['search']             = $search;
        $data['payment_status']     = $paymentStatus;
        $data['fulfillment_status'] = $fulfillmentStatus;

        $builder = $db->table('pos_orders')
            ->where('seller_id', $this->userId);

        // ✅ Search filter (q)
        if (!empty($search)) {
            $builder->groupStart()
                ->like('id', $search)
                ->orLike('order_number', $search)
                ->orLike('customer_name', $search)
                ->orLike('customer_phone', $search)
                ->groupEnd();
        }

        // ✅ Payment status filter
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $builder->where('status', $paymentStatus);
        }

        // ✅ Fulfillment status filter
        if ($fulfillmentStatus !== null && $fulfillmentStatus !== '') {
            if ($fulfillmentStatus === 'processing') {
                $builder->groupStart()
                    ->where('fulfillment_status', 'processing')
                    ->orWhere('fulfillment_status', null)
                    ->orWhere('fulfillment_status', '')
                    ->groupEnd();
            } else {
                $builder->where('fulfillment_status', $fulfillmentStatus);
            }
        }

        // ✅ Clone for count
        $countBuilder = clone $builder;
        $numRows = $countBuilder->countAllResults();

        $data['numRows'] = $numRows;
        $data['pager']   = paginate($this->perPage, $numRows);

        // ✅ Fetch paginated results
        $data['orders'] = $builder
            ->orderBy('id', 'DESC')
            ->limit($this->perPage, $data['pager']->offset)
            ->get()
            ->getResult();

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/pos_orders', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * POS Print (invoice / receipt / address) — opens in new tab, no header/footer
     */
    public function posPrint()
    {
        $db      = \Config\Database::connect();
        $orderId = (int)(inputGet('id') ?? 0);
        $type    = inputGet('type') ?? 'receipt'; // invoice | receipt | address

        $order = $db->table('pos_orders')
            ->where('id', $orderId)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return redirect()->to(dashboardUrl('pos-sales'));
        }

        $payments = $db->table('pos_order_payments')
            ->where('pos_order_id', $orderId)
            ->orderBy('id', 'ASC')
            ->get()->getResult();

        $data['order']           = $order;
        $data['payments']        = $payments;
        $data['type']            = $type;
        $data['currency']        = $this->defaultCurrency;
        $data['generalSettings'] = $this->generalSettings;
        $data['baseSettings']    = $this->settings;

        echo view('dashboard/pos_print', $data);
    }

    /**
     * POS Balance Payment Page
     */
    public function posBalance()
    {
        $db      = \Config\Database::connect();
        $orderId = (int)(inputGet('id') ?? 0);

        $order = $db->table('pos_orders')
            ->where('id', $orderId)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return redirect()->to(dashboardUrl('pos-sales'));
        }

        // Always recalculate from actual payments to avoid stale data
        $totalPaid = (float)$db->table('pos_order_payments')
            ->selectSum('amount')
            ->where('pos_order_id', $orderId)
            ->get()->getRow()->amount;

        $order->amount_paid = $totalPaid;
        $order->balance_due = max(0, (float)$order->grand_total - $totalPaid);
        $order->status      = $order->balance_due <= 0 ? 1 : 0;

        // Sync DB if out of sync
        $db->table('pos_orders')->where('id', $orderId)->update([
            'amount_paid' => $order->amount_paid,
            'balance_due' => $order->balance_due,
            'status'      => $order->status,
        ]);

        $data = $this->setMetaData('Collect Balance Payment');
        $data['activeTab']  = 'pos-sales';
        $data['currency']   = $this->defaultCurrency;
        $data['order']      = $order;

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/pos_balance', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * POS New Order Form
     */
    public function posNewOrder()
    {
        $data = $this->setMetaData('New POS Order');
        $data['activeTab'] = 'pos-sales';
        $data['currency']  = $this->defaultCurrency;

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/pos', $data);
        echo view('dashboard/includes/_footer');
    }

    public function posEditOrder($id)
    {
        $db    = \Config\Database::connect();
        $order = $db->table('pos_orders')
            ->where('id', (int)$id)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return redirect()->to(dashboardUrl('pos-sales'));
        }

        // Sum all payments collected for this order
        $totalPaid = (float)$db->table('pos_order_payments')
            ->selectSum('amount')
            ->where('pos_order_id', (int)$id)
            ->get()->getRow()->amount;

        $payments = $db->table('pos_order_payments')
            ->where('pos_order_id', (int)$id)
            ->orderBy('id', 'ASC')
            ->get()->getResult();

        $data = $this->setMetaData('Edit POS Order');
        $data['activeTab']   = 'pos-sales';
        $data['currency']    = $this->defaultCurrency;
        $data['editOrder']   = $order;
        $data['totalPaid']   = $totalPaid;
        $data['payments']    = $payments;

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/pos_edit', $data);
        echo view('dashboard/includes/_footer');
    }

    public function updatePosOrder()
    {
        $db   = \Config\Database::connect();
        $body = $this->request->getJSON(true) ?? [];

        $orderId = (int)($body['order_id'] ?? 0);
        $order   = $db->table('pos_orders')
            ->where('id', $orderId)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Order not found']);
        }

        $items = $body['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'No items provided']);
        }

        $grandTotal = (float)($body['grand_total'] ?? 0);

        // Always use the real total paid from DB to avoid overwriting collected payments
        $totalPaid = (float)$db->table('pos_order_payments')
            ->selectSum('amount')
            ->where('pos_order_id', $orderId)
            ->get()->getRow()->amount;

        $balanceDue  = max(0, $grandTotal - $totalPaid);
        $orderStatus = $balanceDue <= 0 ? 1 : 0;

        $db->table('pos_orders')->where('id', $orderId)->update([
            'customer_name'     => $body['customer_name'] ?? $order->customer_name,
            'customer_phone'    => $body['customer_phone'] ?? $order->customer_phone,
            'customer_email'    => $body['customer_email'] ?? $order->customer_email,
            'billing_address'   => safeJsonEncode($body['billing_address'] ?? []),
            'shipping_address'  => safeJsonEncode($body['shipping_address'] ?? []),
            'items'             => safeJsonEncode($items),
            'subtotal'          => (float)($body['subtotal'] ?? 0),
            'total_tax'         => (float)($body['total_tax'] ?? 0),
            'total_discount'    => (float)($body['total_discount'] ?? 0),
            'shipping_charges'  => (float)($body['shipping_charges'] ?? 0),
            'grand_total'       => $grandTotal,
            'amount_paid'       => $totalPaid,
            'balance_due'       => $balanceDue,
            'payment_method'    => $body['payment_method'] ?? $order->payment_method,
            'payment_reference' => $body['payment_reference'] ?? $order->payment_reference,
            'payment_date'      => $body['payment_date'] ?? $order->payment_date,
            'status'            => $orderStatus,
            'updated_at'        => date('Y-m-d H:i:s'),
            // Cheque fields — only update if cheque data provided
            ...(!empty($body['cheque_bank_name']) ? [
                'cheque_bank_name' => trim($body['cheque_bank_name']),
                'cheque_no'        => trim($body['cheque_no'] ?? ''),
                'cheque_date'      => $body['cheque_date'] ?? null,
                'cheque_amount'    => (float)($body['cheque_amount'] ?? 0),
            ] : []),
        ]);

        return $this->response->setJSON([
            'status'       => 1,
            'order_number' => $order->order_number,
            'balance_due'  => $balanceDue,
        ]);
    }

    public function updatePosPayment()
    {
        $db        = \Config\Database::connect();
        $body      = $this->request->getJSON(true) ?? [];
        $paymentId = (int)($body['payment_id'] ?? 0);
        $orderId   = (int)($body['order_id'] ?? 0);

        // Verify ownership
        $order = $db->table('pos_orders')->where('id', $orderId)->where('seller_id', $this->userId)->get()->getRow();
        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Not found']);
        }

        $payment = $db->table('pos_order_payments')->where('id', $paymentId)->where('pos_order_id', $orderId)->get()->getRow();
        if (empty($payment)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Payment not found']);
        }

        $amount = (float)($body['amount'] ?? 0);
        if ($amount <= 0) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Invalid amount']);
        }

        $totalPaidExceptThis = (float)$db->table('pos_order_payments')
            ->selectSum('amount')
            ->where('pos_order_id', $orderId)
            ->where('id !=', $paymentId)
            ->get()->getRow()->amount;

        $balanceDue = max(0, (float)$order->grand_total - $totalPaidExceptThis);
        if ($amount > $balanceDue + 0.001) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Amount cannot exceed balance due']);
        }

        $db->table('pos_order_payments')->where('id', $paymentId)->update([
            'amount'            => $amount,
            'payment_method'    => $body['payment_method'] ?? $payment->payment_method,
            'payment_reference' => $body['payment_reference'] ?? $payment->payment_reference,
            'payment_date'      => $body['payment_date'] ?? $payment->payment_date,
        ]);

        // Recalculate totals
        $totalPaid  = (float)$db->table('pos_order_payments')->selectSum('amount')->where('pos_order_id', $orderId)->get()->getRow()->amount;
        $balanceDue = max(0, (float)$order->grand_total - $totalPaid);
        $db->table('pos_orders')->where('id', $orderId)->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $balanceDue,
            'status'      => $balanceDue <= 0 ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 1, 'total_paid' => $totalPaid, 'balance_due' => $balanceDue]);
    }

    public function deletePosPayment()
    {
        $db        = \Config\Database::connect();
        $body      = $this->request->getJSON(true) ?? [];
        $paymentId = (int)($body['payment_id'] ?? 0);
        $orderId   = (int)($body['order_id'] ?? 0);

        $order = $db->table('pos_orders')->where('id', $orderId)->where('seller_id', $this->userId)->get()->getRow();
        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Not found']);
        }

        $db->table('pos_order_payments')->where('id', $paymentId)->where('pos_order_id', $orderId)->delete();

        $totalPaid  = (float)$db->table('pos_order_payments')->selectSum('amount')->where('pos_order_id', $orderId)->get()->getRow()->amount;
        $balanceDue = max(0, (float)$order->grand_total - $totalPaid);
        $db->table('pos_orders')->where('id', $orderId)->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $balanceDue,
            'status'      => $balanceDue <= 0 ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 1, 'total_paid' => $totalPaid, 'balance_due' => $balanceDue]);
    }

    /**
     * POS Update Fulfillment Status (POST)
     */
    public function posUpdateFulfillmentStatus()
    {
        $db      = \Config\Database::connect();
        $body    = $this->request->getJSON(true) ?? [];
        $orderId = (int)($body['order_id'] ?? 0);
        $status  = $body['fulfillment_status'] ?? '';

        $allowed = ['processing', 'shipped', 'delivered'];
        if (!$orderId || !in_array($status, $allowed)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Invalid request']);
        }

        $order = $db->table('pos_orders')
            ->where('id', $orderId)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Order not found']);
        }

        $db->table('pos_orders')->where('id', $orderId)->update([
            'fulfillment_status' => $status,
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 1, 'fulfillment_status' => $status]);
    }

    /**
     * POS Update Delivery Details (POST)
     */
    public function posUpdateDeliveryDetails()
    {
        $db      = \Config\Database::connect();
        $body    = $this->request->getJSON(true) ?? [];
        $orderId = (int)($body['order_id'] ?? 0);

        $order = $db->table('pos_orders')
            ->where('id', $orderId)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Order not found']);
        }

        $shipping = [
            'line1'   => trim($body['shipping_line1'] ?? ''),
            'city'    => trim($body['shipping_city'] ?? ''),
            'state'   => trim($body['shipping_state'] ?? ''),
            'pincode' => trim($body['shipping_pincode'] ?? ''),
        ];

        $db->table('pos_orders')->where('id', $orderId)->update([
            'fulfillment_status'     => !empty($body['fulfillment_status']) ? $body['fulfillment_status'] : $order->fulfillment_status,
            'delivery_tracking_code' => trim($body['tracking_code'] ?? ''),
            'delivery_tracking_url'  => trim($body['tracking_url']  ?? ''),
            'delivery_date'          => !empty($body['date']) ? $body['date'] : null,
            'delivery_name'          => trim($body['name']   ?? ''),
            'delivery_phone'         => trim($body['phone']  ?? ''),
            'delivery_amount'        => (float)($body['amount'] ?? 0),
            'shipping_address'       => safeJsonEncode($shipping),
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 1]);
    }

    /**
     * POS Product Search (POST)
     */
    public function posProductSearch()
    {
        $body  = $this->request->getJSON(true) ?? [];
        $query = trim($body['query'] ?? '');
        if (empty($query)) {
            return $this->response->setJSON(['products' => []]);
        }

        $db     = \Config\Database::connect();
        $langId = $this->activeLang->id ?? 1;

        $rows = $db->table('products p')
            ->select('p.id, p.price, p.price_discounted, p.sku, p.stock, pd.title')
            ->join('product_details pd', 'pd.product_id = p.id AND pd.lang_id = ' . (int)$langId)
            ->where('p.is_active', 1)
            ->where('p.is_deleted', 0)
            ->where('p.user_id', $this->userId)
            ->groupStart()
                ->like('pd.title', $query)
                ->orLike('p.sku', $query)
            ->groupEnd()
            ->limit(15)
            ->get()->getResult();

        $products = [];
        foreach ($rows as $r) {
            $products[] = [
                'id'    => $r->id,
                'name'  => $r->title,
                'price' => (float)($r->price_discounted > 0 ? $r->price_discounted : $r->price),
                'sku'   => $r->sku,
                'stock' => $r->stock,
            ];
        }

        return $this->response->setJSON(['products' => $products]);
    }

    /**
     * Save POS Order (POST)
     */
    public function savePosOrder()
    {
        $db   = \Config\Database::connect();
        $body = $this->request->getJSON(true) ?? [];

        $items = $body['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'No items provided']);
        }

        $grandTotal  = (float)($body['grand_total'] ?? 0);
        $amountPaid  = (float)($body['amount_paid'] ?? 0);
        $balanceDue  = max(0, $grandTotal - $amountPaid);
        $orderStatus = $balanceDue <= 0 ? 1 : 0;

        $data = [
            'order_number'      => 'POS-' . strtoupper(substr(uniqid(), -8)),
            'seller_id'         => $this->userId,
            'customer_name'     => $body['customer_name'] ?? '',
            'customer_phone'    => $body['customer_phone'] ?? '',
            'customer_email'    => $body['customer_email'] ?? '',
            'billing_address'   => safeJsonEncode($body['billing_address'] ?? []),
            'shipping_address'  => safeJsonEncode($body['shipping_address'] ?? []),
            'items'             => safeJsonEncode($items),
            'subtotal'          => (float)($body['subtotal'] ?? 0),
            'total_tax'         => (float)($body['total_tax'] ?? 0),
            'total_discount'    => (float)($body['total_discount'] ?? 0),
            'shipping_charges'  => (float)($body['shipping_charges'] ?? 0),
            'grand_total'       => $grandTotal,
            'amount_paid'       => $amountPaid,
            'balance_due'       => $balanceDue,
            'payment_method'    => $body['payment_method'] ?? '',
            'payment_reference' => $body['payment_reference'] ?? '',
            'payment_date'      => $body['payment_date'] ?? date('Y-m-d'),
            'currency'          => $this->defaultCurrency->code ?? 'USD',
            'status'            => $orderStatus,
            'source'		    => 'Website',
            // Cheque fields
            'cheque_bank_name'  => $body['payment_method'] === 'Cheque' ? trim($body['cheque_bank_name'] ?? '') : null,
            'cheque_no'         => $body['payment_method'] === 'Cheque' ? trim($body['cheque_no']        ?? '') : null,
            'cheque_date'       => $body['payment_method'] === 'Cheque' && !empty($body['cheque_date']) ? $body['cheque_date'] : null,
            'cheque_amount'     => $body['payment_method'] === 'Cheque' ? (float)($body['cheque_amount'] ?? 0) : null,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $db->table('pos_orders')->insert($data);
        $posOrderId = $db->insertID();

        if (!$posOrderId) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to save order']);
        }

        if ($amountPaid > 0) {
            $db->table('pos_order_payments')->insert([
                'pos_order_id'      => $posOrderId,
                'amount'            => $amountPaid,
                'payment_method'    => $data['payment_method'],
                'payment_reference' => $data['payment_reference'],
                'payment_date'      => $data['payment_date'],
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON([
            'status'       => 1,
            'order_id'     => $posOrderId,
            'order_number' => $data['order_number'],
            'balance_due'  => $balanceDue,
        ]);
    }

    /**
     * Load POS Order (POST)
     */
    public function loadPosOrder()
    {
        $db      = \Config\Database::connect();
        $body    = $this->request->getJSON(true) ?? [];
        $orderId = $body['order_id'] ?? '';

        $order = $db->table('pos_orders')
            ->where('seller_id', $this->userId)
            ->groupStart()
                ->where('id', (int)$orderId)
                ->orWhere('order_number', $orderId)
            ->groupEnd()
            ->get()->getRow();

        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Order not found']);
        }

        $payments  = $db->table('pos_order_payments')
            ->where('pos_order_id', $order->id)
            ->orderBy('id', 'ASC')
            ->get()->getResult();

        $totalPaid = 0;
        foreach ($payments as $p) {
            $totalPaid += (float)$p->amount;
        }

        return $this->response->setJSON([
            'status'   => 1,
            'order'    => [
                'id'             => $order->id,
                'order_number'   => $order->order_number,
                'customer_name'  => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'grand_total'    => (float)$order->grand_total,
                'amount_paid'    => $totalPaid,
                'balance_due'    => max(0, (float)$order->grand_total - $totalPaid),
                'currency'       => $order->currency,
                'items'          => safeJsonDecode($order->items),
                'payment_status' => $order->status == 1 ? 'paid' : 'partial',
            ],
            'payments' => $payments,
        ]);
    }

    /**
     * POS Balance Payment (POST)
     */
    public function posBalancePayment()
    {
        $db      = \Config\Database::connect();
        $body    = $this->request->getJSON(true) ?? [];
        $orderId = (int)($body['order_id'] ?? 0);

        $order = $db->table('pos_orders')
            ->where('id', $orderId)
            ->where('seller_id', $this->userId)
            ->get()->getRow();

        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Order not found']);
        }

        $amount = (float)($body['amount'] ?? 0);
        if ($amount <= 0) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Invalid amount']);
        }

        $balanceDue = max(0, (float)$order->grand_total - (float)$order->amount_paid);
        if ($amount > $balanceDue + 0.001) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Amount cannot exceed balance due']);
        }

        $db->table('pos_order_payments')->insert([
            'pos_order_id'      => $orderId,
            'amount'            => $amount,
            'payment_method'    => $body['payment_method'] ?? '',
            'payment_reference' => $body['payment_reference'] ?? '',
            'payment_date'      => $body['payment_date'] ?? date('Y-m-d'),
            'notes'             => $body['notes'] ?? '',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // Recalculate from actual payments table
        $newTotalPaid = (float)$db->table('pos_order_payments')
            ->selectSum('amount')
            ->where('pos_order_id', $orderId)
            ->get()->getRow()->amount;

        $newBalance = max(0, (float)$order->grand_total - $newTotalPaid);

        $db->table('pos_orders')->where('id', $orderId)->update([
            'amount_paid' => $newTotalPaid,
            'balance_due' => $newBalance,
            'status'      => $newBalance <= 0 ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'      => 1,
            'new_paid'    => $newTotalPaid,
            'new_balance' => $newBalance,
            'is_paid'     => $newBalance <= 0,
        ]);
    }
}
