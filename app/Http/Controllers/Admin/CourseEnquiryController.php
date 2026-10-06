<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourseEnquiryController extends Controller
{
    private const PLAN_SLUG_MAP = [
        'composite-skill-lab-basic'   => 'basic-skill-1',
        'composite-skill-lab-advance' => 'advance-skill-1',
        'composite-skill-lab-premium' => 'premium-skill-1',
    ];

    public function index(Request $request)
    {
        $query = CourseEnquiry::query();

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('name',         'like', "%{$kw}%")
                  ->orWhere('phone',       'like', "%{$kw}%")
                  ->orWhere('email',       'like', "%{$kw}%")
                  ->orWhere('school',      'like', "%{$kw}%")
                  ->orWhere('city',        'like', "%{$kw}%")
                  ->orWhere('course_title','like', "%{$kw}%");
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage    = (int) $request->get('par-page', 20);
        $enquiries  = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $sources    = CourseEnquiry::select('source')->distinct()->whereNotNull('source')->pluck('source');
        $title      = __('Course Enquiries');

        return view('admin.course-enquiries.index', compact('enquiries', 'sources', 'title'));
    }

    public function show($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        // Mark as read when first viewed
        if (!$enquiry->status || $enquiry->status === 'new') {
            $enquiry->update(['status' => 'read']);
        }

        $quotation      = $this->parseQuotation($enquiry);
        $proforma       = $this->parseProformaInvoice($enquiry);
        $invoice        = $this->parseTaxInvoice($enquiry);
        $paymentSummary = $enquiry->invoice_payment_summary;
        $title          = __('Enquiry Details');

        return view('admin.course-enquiries.show', compact('enquiry', 'quotation', 'proforma', 'invoice', 'paymentSummary', 'title'));
    }

    public function updateStatus(Request $request, $id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $request->validate(['status' => ['required', 'in:new,read,contacted,closed']]);
        $enquiry->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $enquiry->status]);
    }

    public function destroy($id)
    {
        CourseEnquiry::findOrFail($id)->delete();

        $notification = ['messege' => __('Deleted successfully'), 'alert-type' => 'success'];
        return redirect()->route('admin.course-enquiries')->with($notification);
    }

    // ──────────────────────────────────────────────────────────────
    // QUOTATION ACTIONS
    // ──────────────────────────────────────────────────────────────

    public function quotation($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $quotation = $this->parseQuotation($enquiry);

        // If no quotation items yet and enquiry belongs to a lab plan, pre-load bundle components
        if (empty($quotation['items']) && !empty($enquiry->source) && isset(self::PLAN_SLUG_MAP[$enquiry->source])) {
            $components = $this->fetchBundleComponents(self::PLAN_SLUG_MAP[$enquiry->source]);
            if (!empty($components)) {
                $subtotal = 0;
                $items = [];
                foreach ($components as $c) {
                    $qty = (int) ($c['required_quantity'] ?? 1);
                    $price = (float) ($c['unit_price'] ?? 0);
                    if ($qty <= 0 || empty($c['title'])) continue;
                    $total = $qty * $price;
                    $subtotal += $total;
                    $items[] = [
                        'category'   => $c['category_name'] ?? 'General',
                        'item'       => $c['title'],
                        'hsn'        => $c['hsn_code'] ?? '9023',
                        'qty'        => $qty,
                        'unit_price' => $price,
                        'total'      => $total,
                    ];
                }
                $quotation['items']       = $items;
                $quotation['subtotal']    = $subtotal;
                $taxRate                  = (float) ($quotation['tax_percent'] ?? 18);
                $quotation['tax_amount']  = round($subtotal * ($taxRate / 100), 2);
                $quotation['grand_total'] = round($subtotal + $quotation['tax_amount'], 2);
            }
        }

        $isLocked = $enquiry->isQuotationLocked();
        $title = $isLocked ? __('Quotation (Locked - Proforma Issued)') : __('Create / Edit Quotation');
        return view('admin.course-enquiries.quotation-form', compact('enquiry', 'quotation', 'title', 'isLocked'));
    }

    public function saveQuotation(Request $request, $id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);

        if ($enquiry->isQuotationLocked()) {
            return redirect()->route('admin.course-enquiry.quotation.view', $enquiry->id)
                ->with(['messege' => __('Quotation cannot be edited because a Proforma Invoice has already been issued.'), 'alert-type' => 'error']);
        }

        $request->validate([
            'quotation_number' => ['required', 'string', 'max:50'],
            'date'             => ['required', 'date'],
            'valid_until'      => ['nullable', 'date'],
            'client_name'      => ['required', 'string', 'max:255'],
            'email'            => ['nullable', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
        ]);

        $rawItems = $request->input('items', []);
        $items = [];
        $subtotal = 0;

        if (is_array($rawItems)) {
            foreach ($rawItems as $row) {
                $itemDesc = trim($row['item'] ?? '');
                if ($itemDesc === '') continue;

                $qty   = max(1, (int) ($row['qty'] ?? 1));
                $price = max(0, (float) ($row['unit_price'] ?? 0));
                $total = round($qty * $price, 2);
                $subtotal += $total;

                $items[] = [
                    'product_id' => !empty($row['product_id']) ? (int) $row['product_id'] : null,
                    'sku'        => trim($row['sku'] ?? ''),
                    'category'   => trim($row['category'] ?? 'General'),
                    'item'       => $itemDesc,
                    'hsn'        => trim($row['hsn'] ?? '9023'),
                    'qty'        => $qty,
                    'unit_price' => $price,
                    'total'      => $total,
                ];
            }
        }

        $discount    = max(0, (float) $request->input('discount', 0));
        $taxPercent  = max(0, (float) $request->input('tax_percent', 0));
        $shipping    = max(0, (float) $request->input('shipping', 0));

        $netSubtotal = max(0, $subtotal - $discount);
        $taxAmount   = round($netSubtotal * ($taxPercent / 100), 2);
        $grandTotal  = round($netSubtotal + $taxAmount + $shipping, 2);

        $data = [
            'quotation_number' => trim($request->input('quotation_number')),
            'date'             => $request->input('date'),
            'valid_until'      => $request->input('valid_until'),
            'client_name'      => trim($request->input('client_name')),
            'designation'      => trim($request->input('designation', '')),
            'school'           => trim($request->input('school', '')),
            'email'            => trim($request->input('email', '')),
            'phone'            => trim($request->input('phone', '')),
            'city'             => trim($request->input('city', '')),
            'address'          => trim($request->input('address', '')),
            'course_title'     => trim($request->input('course_title', $enquiry->course_title ?? '')),
            'items'            => $items,
            'subtotal'         => round($subtotal, 2),
            'discount'         => round($discount, 2),
            'tax_percent'      => $taxPercent,
            'tax_amount'       => $taxAmount,
            'shipping'         => round($shipping, 2),
            'grand_total'      => $grandTotal,
            'notes'            => trim($request->input('notes', '')),
            'terms'            => trim($request->input('terms', '')),
            'updated_at'       => now()->toDateTimeString(),
        ];

        $enquiry->update(['quotation' => json_encode($data, JSON_UNESCAPED_UNICODE)]);

        $notification = ['messege' => __('Quotation saved successfully!'), 'alert-type' => 'success'];

        if ($request->input('submit_action') === 'save_and_view') {
            return redirect()->route('admin.course-enquiry.quotation.view', $enquiry->id)->with($notification);
        }

        return redirect()->route('admin.course-enquiry.show', $enquiry->id)->with($notification);
    }

    public function viewQuotation($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $quotation = $this->parseQuotation($enquiry);

        if (empty($quotation['items'])) {
            $notification = ['messege' => __('Please create or edit quotation details first.'), 'alert-type' => 'info'];
            return redirect()->route('admin.course-enquiry.quotation', $enquiry->id)->with($notification);
        }

        $setting = Cache::get('setting');
        $grandTotal = (float) ($quotation['grand_total'] ?? 0);
        $amountInWords = self::amountInWords($grandTotal);
        $title = __('Quotation') . ' - ' . ($quotation['quotation_number'] ?? '#' . $enquiry->id);

        return view('admin.course-enquiries.quotation-view', compact('enquiry', 'quotation', 'setting', 'amountInWords', 'title'));
    }

    // ──────────────────────────────────────────────────────────────
    // PROFORMA INVOICE ACTIONS
    // ──────────────────────────────────────────────────────────────

    public function proformaInvoice($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $proforma = $this->parseProformaInvoice($enquiry);
        $isLocked = $enquiry->isProformaLocked();

        $title = $isLocked ? __('Proforma Invoice (Locked - Tax Invoice Issued)') : __('Create / Edit Proforma Invoice');
        return view('admin.course-enquiries.proforma-form', compact('enquiry', 'proforma', 'title', 'isLocked'));
    }

    public function saveProformaInvoice(Request $request, $id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);

        if ($enquiry->isProformaLocked()) {
            return redirect()->route('admin.course-enquiry.proforma.view', $enquiry->id)
                ->with(['messege' => __('Proforma Invoice is locked and cannot be edited because the final Tax Invoice has already been issued.'), 'alert-type' => 'error']);
        }

        $request->validate([
            'invoice_number' => ['required', 'string', 'max:50'],
            'date'           => ['required', 'date'],
            'due_date'       => ['nullable', 'date'],
            'client_name'    => ['required', 'string', 'max:255'],
            'school'         => ['nullable', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
        ]);

        $rawItems = $request->input('items', []);
        $items = [];
        $subtotal = 0;

        if (is_array($rawItems)) {
            foreach ($rawItems as $row) {
                $itemDesc = trim($row['item'] ?? '');
                if ($itemDesc === '') continue;

                $qty   = max(1, (int) ($row['qty'] ?? 1));
                $price = max(0, (float) ($row['unit_price'] ?? 0));
                $total = round($qty * $price, 2);
                $subtotal += $total;

                $items[] = [
                    'product_id' => !empty($row['product_id']) ? (int) $row['product_id'] : null,
                    'sku'        => trim($row['sku'] ?? ''),
                    'category'   => trim($row['category'] ?? 'General'),
                    'item'       => $itemDesc,
                    'hsn'        => trim($row['hsn'] ?? '9023'),
                    'qty'        => $qty,
                    'unit_price' => $price,
                    'total'      => $total,
                ];
            }
        }

        $discount    = max(0, (float) $request->input('discount', 0));
        $taxPercent  = max(0, (float) $request->input('tax_percent', 0));
        $shipping    = max(0, (float) $request->input('shipping', 0));

        $netSubtotal = max(0, $subtotal - $discount);
        $taxAmount   = round($netSubtotal * ($taxPercent / 100), 2);
        $grandTotal  = round($netSubtotal + $taxAmount + $shipping, 2);

        // Version tracking:
        $existingProforma = $enquiry->proforma_invoice_data;
        $version = 1;
        $versions = [];
        if (!empty($existingProforma)) {
            $prevVer = (int) ($existingProforma['version'] ?? 1);
            $versions = (array) ($existingProforma['versions'] ?? []);
            $versions[] = [
                'version'        => $prevVer,
                'invoice_number' => $existingProforma['invoice_number'] ?? '',
                'date'           => $existingProforma['date'] ?? '',
                'grand_total'    => (float) ($existingProforma['grand_total'] ?? 0),
                'items_count'    => count($existingProforma['items'] ?? []),
                'saved_at'       => $existingProforma['updated_at'] ?? now()->toDateTimeString(),
            ];
            $version = $prevVer + 1;
        }

        $data = [
            'invoice_number' => trim($request->input('invoice_number')),
            'date'           => $request->input('date'),
            'due_date'       => $request->input('due_date'),
            'version'        => $version,
            'versions'       => $versions,
            'client_name'    => trim($request->input('client_name')),
            'designation'    => trim($request->input('designation', '')),
            'school'         => trim($request->input('school', '')),
            'gstin'          => trim($request->input('gstin', '')),
            'email'          => trim($request->input('email', '')),
            'phone'          => trim($request->input('phone', '')),
            'city'           => trim($request->input('city', '')),
            'address'        => trim($request->input('address', '')),
            'course_title'   => trim($request->input('course_title', $enquiry->course_title ?? '')),
            'items'          => $items,
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'tax_percent'    => $taxPercent,
            'tax_amount'     => $taxAmount,
            'shipping'       => round($shipping, 2),
            'grand_total'    => $grandTotal,
            // Bank Remittance Info
            'bank_name'      => trim($request->input('bank_name', 'HDFC Bank')),
            'account_name'   => trim($request->input('account_name', 'Skillvation EdTech Pvt Ltd')),
            'account_number' => trim($request->input('account_number', '50200012345678')),
            'ifsc_code'      => trim($request->input('ifsc_code', 'HDFC0001234')),
            'branch'         => trim($request->input('branch', 'Bengaluru')),
            'upi_id'         => trim($request->input('upi_id', 'skillvation@hdfcbank')),
            'payment_terms'  => trim($request->input('payment_terms', '')),
            'notes'          => trim($request->input('notes', '')),
            'updated_at'     => now()->toDateTimeString(),
        ];

        $enquiry->update(['proforma_invoice' => json_encode($data, JSON_UNESCAPED_UNICODE)]);

        $notification = ['messege' => __('Proforma Invoice (Rev :rev) saved successfully!', ['rev' => $version]), 'alert-type' => 'success'];

        if ($request->input('submit_action') === 'save_and_view') {
            return redirect()->route('admin.course-enquiry.proforma.view', $enquiry->id)->with($notification);
        }

        return redirect()->route('admin.course-enquiry.show', $enquiry->id)->with($notification);
    }

    public function viewProformaInvoice($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $proforma = $this->parseProformaInvoice($enquiry);

        if (empty($proforma['items'])) {
            $notification = ['messege' => __('Please create or edit proforma invoice details first.'), 'alert-type' => 'info'];
            return redirect()->route('admin.course-enquiry.proforma', $enquiry->id)->with($notification);
        }

        $setting = Cache::get('setting');
        $grandTotal = (float) ($proforma['grand_total'] ?? 0);
        $amountInWords = self::amountInWords($grandTotal);
        $title = __('Proforma Invoice') . ' - ' . ($proforma['invoice_number'] ?? '#' . $enquiry->id);

        return view('admin.course-enquiries.proforma-view', compact('enquiry', 'proforma', 'setting', 'amountInWords', 'title'));
    }

    // ──────────────────────────────────────────────────────────────
    // FINAL TAX INVOICE ACTIONS & PAYMENT COLLECTION
    // ──────────────────────────────────────────────────────────────

    public function taxInvoice($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $invoice = $this->parseTaxInvoice($enquiry);
        $paymentSummary = $enquiry->invoice_payment_summary;

        $title = __('Final Tax Invoice');
        return view('admin.course-enquiries.invoice-form', compact('enquiry', 'invoice', 'title', 'paymentSummary'));
    }

    public function saveTaxInvoice(Request $request, $id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);

        $request->validate([
            'invoice_number' => ['required', 'string', 'max:50'],
            'date'           => ['required', 'date'],
            'due_date'       => ['nullable', 'date'],
            'client_name'    => ['required', 'string', 'max:255'],
            'school'         => ['nullable', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
        ]);

        $rawItems = $request->input('items', []);
        $items = [];
        $subtotal = 0;

        if (is_array($rawItems)) {
            foreach ($rawItems as $row) {
                $itemDesc = trim($row['item'] ?? '');
                if ($itemDesc === '') continue;

                $qty   = max(1, (int) ($row['qty'] ?? 1));
                $price = max(0, (float) ($row['unit_price'] ?? 0));
                $total = round($qty * $price, 2);
                $subtotal += $total;

                $items[] = [
                    'product_id' => !empty($row['product_id']) ? (int) $row['product_id'] : null,
                    'sku'        => trim($row['sku'] ?? ''),
                    'category'   => trim($row['category'] ?? 'General'),
                    'item'       => $itemDesc,
                    'hsn'        => trim($row['hsn'] ?? '9023'),
                    'qty'        => $qty,
                    'unit_price' => $price,
                    'total'      => $total,
                ];
            }
        }

        $discount    = max(0, (float) $request->input('discount', 0));
        $taxPercent  = max(0, (float) $request->input('tax_percent', 0));
        $shipping    = max(0, (float) $request->input('shipping', 0));

        $netSubtotal = max(0, $subtotal - $discount);
        $taxAmount   = round($netSubtotal * ($taxPercent / 100), 2);
        $grandTotal  = round($netSubtotal + $taxAmount + $shipping, 2);

        // Preserve payments already collected
        $existingInvoice = $enquiry->invoice_data;
        $payments = (array) ($existingInvoice['payments'] ?? []);

        $data = [
            'invoice_number' => trim($request->input('invoice_number')),
            'date'           => $request->input('date'),
            'due_date'       => $request->input('due_date'),
            'client_name'    => trim($request->input('client_name')),
            'designation'    => trim($request->input('designation', '')),
            'school'         => trim($request->input('school', '')),
            'gstin'          => trim($request->input('gstin', '')),
            'pan'            => trim($request->input('pan', '')),
            'email'          => trim($request->input('email', '')),
            'phone'          => trim($request->input('phone', '')),
            'city'           => trim($request->input('city', '')),
            'address'        => trim($request->input('address', '')),
            'course_title'   => trim($request->input('course_title', $enquiry->course_title ?? '')),
            'items'          => $items,
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'tax_percent'    => $taxPercent,
            'tax_amount'     => $taxAmount,
            'shipping'       => round($shipping, 2),
            'grand_total'    => $grandTotal,
            // Bank Info
            'bank_name'      => trim($request->input('bank_name', 'HDFC Bank')),
            'account_name'   => trim($request->input('account_name', 'Skillvation EdTech Pvt Ltd')),
            'account_number' => trim($request->input('account_number', '50200012345678')),
            'ifsc_code'      => trim($request->input('ifsc_code', 'HDFC0001234')),
            'branch'         => trim($request->input('branch', 'Bengaluru Main Branch')),
            'upi_id'         => trim($request->input('upi_id', 'skillvation@hdfcbank')),
            'notes'          => trim($request->input('notes', '')),
            'terms'          => trim($request->input('terms', '')),
            'payments'       => $payments,
            'updated_at'     => now()->toDateTimeString(),
        ];

        $enquiry->update(['invoice' => json_encode($data, JSON_UNESCAPED_UNICODE)]);

        $notification = ['messege' => __('Final Tax Invoice saved successfully!'), 'alert-type' => 'success'];

        if ($request->input('submit_action') === 'save_and_view') {
            return redirect()->route('admin.course-enquiry.invoice.view', $enquiry->id)->with($notification);
        }

        return redirect()->route('admin.course-enquiry.show', $enquiry->id)->with($notification);
    }

    public function viewTaxInvoice($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $invoice = $this->parseTaxInvoice($enquiry);

        if (empty($invoice['items'])) {
            $notification = ['messege' => __('Please create or edit tax invoice details first.'), 'alert-type' => 'info'];
            return redirect()->route('admin.course-enquiry.invoice', $enquiry->id)->with($notification);
        }

        $setting = Cache::get('setting');
        $grandTotal = (float) ($invoice['grand_total'] ?? 0);
        $amountInWords = self::amountInWords($grandTotal);
        $title = __('Tax Invoice') . ' - ' . ($invoice['invoice_number'] ?? '#' . $enquiry->id);

        $paymentSummary = $enquiry->invoice_payment_summary;

        return view('admin.course-enquiries.invoice-view', compact('enquiry', 'invoice', 'setting', 'amountInWords', 'title', 'paymentSummary'));
    }

    public function recordPayment(Request $request, $id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);

        $request->validate([
            'amount'          => ['required', 'numeric', 'min:1'],
            'type'            => ['required', 'in:advance,full,balance'],
            'payment_date'    => ['required', 'date'],
            'payment_method'  => ['required', 'string', 'max:100'],
            'transaction_ref' => ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $invoice = $this->parseTaxInvoice($enquiry);
        $payments = (array) ($invoice['payments'] ?? []);

        $receiptNo = 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $newPayment = [
            'id'              => uniqid('pay_'),
            'receipt_no'      => $receiptNo,
            'type'            => $request->input('type'),
            'amount'          => round((float) $request->input('amount'), 2),
            'payment_date'    => $request->input('payment_date'),
            'payment_method'  => trim($request->input('payment_method')),
            'transaction_ref' => trim($request->input('transaction_ref', '')),
            'notes'           => trim($request->input('notes', '')),
            'recorded_by'     => auth()->guard('admin')->user()?->name ?? 'Admin',
            'created_at'      => now()->toDateTimeString(),
        ];

        $payments[] = $newPayment;
        $invoice['payments'] = $payments;

        $enquiry->update(['invoice' => json_encode($invoice, JSON_UNESCAPED_UNICODE)]);

        $notification = [
            'messege'    => __('Payment of ₹:amount recorded successfully (:type)!', [
                'amount' => number_format($newPayment['amount'], 2),
                'type'   => ucfirst($newPayment['type']),
            ]),
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    public function deletePayment($id, $payment_id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $invoice = $this->parseTaxInvoice($enquiry);
        $payments = (array) ($invoice['payments'] ?? []);

        $filtered = array_values(array_filter($payments, function ($p) use ($payment_id) {
            return ($p['id'] ?? '') !== $payment_id;
        }));

        $invoice['payments'] = $filtered;
        $enquiry->update(['invoice' => json_encode($invoice, JSON_UNESCAPED_UNICODE)]);

        return redirect()->back()->with(['messege' => __('Payment record removed.'), 'alert-type' => 'success']);
    }

    // ──────────────────────────────────────────────────────────────
    // AJAX: LOAD DEFAULT BUNDLE ITEMS
    // ──────────────────────────────────────────────────────────────

    public function loadDefaultItems($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $slug = self::PLAN_SLUG_MAP[$enquiry->source ?? ''] ?? null;

        if (!$slug) {
            return response()->json([
                'success' => false,
                'message' => __('No preconfigured bundle found for source: :source', ['source' => $enquiry->source ?? 'N/A']),
            ]);
        }

        $components = $this->fetchBundleComponents($slug);
        if (empty($components)) {
            return response()->json([
                'success' => false,
                'message' => __('Could not load bundle components from server.'),
            ]);
        }

        $items = [];
        $subtotal = 0;
        foreach ($components as $c) {
            $qty = (int) ($c['required_quantity'] ?? 1);
            $price = (float) ($c['unit_price'] ?? 0);
            if ($qty <= 0 || empty($c['title'])) continue;
            $total = $qty * $price;
            $subtotal += $total;
            $items[] = [
                'product_id' => $c['product_id'] ?? null,
                'sku'        => $c['sku'] ?? '',
                'category'   => $c['category_name'] ?? 'General',
                'item'       => $c['title'],
                'hsn'        => $c['hsn_code'] ?? '9023',
                'qty'        => $qty,
                'unit_price' => $price,
                'total'      => $total,
            ];
        }

        return response()->json([
            'success'  => true,
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // HELPER: PARSE / INITIALIZE QUOTATION DATA
    // ──────────────────────────────────────────────────────────────

    private function parseQuotation(CourseEnquiry $enquiry): array
    {
        $stored = $enquiry->quotation ? json_decode($enquiry->quotation, true) : null;
        if (!is_array($stored)) {
            $stored = [];
        }

        $dateFormatted = $enquiry->created_at ? $enquiry->created_at->format('Y-m-d') : date('Y-m-d');
        $validUntil    = date('Y-m-d', strtotime($dateFormatted . ' +30 days'));
        $quoteNumber   = 'QT-' . ($enquiry->created_at ? $enquiry->created_at->format('Ym') : date('Ym')) . '-' . str_pad($enquiry->id, 4, '0', STR_PAD_LEFT);

        // Normalize items array
        $items = $stored['items'] ?? [];
        $subtotal = (float) ($stored['subtotal'] ?? 0);
        if ($subtotal <= 0 && !empty($items)) {
            $subtotal = array_sum(array_column($items, 'total'));
        }
        if ($subtotal <= 0 && !empty($stored['grand_total'])) {
            $subtotal = (float) $stored['grand_total'];
        }

        $discount   = (float) ($stored['discount'] ?? 0);
        $taxPercent = isset($stored['tax_percent']) ? (float) $stored['tax_percent'] : 18;
        $shipping   = (float) ($stored['shipping'] ?? 0);
        $taxAmount  = isset($stored['tax_amount']) ? (float) $stored['tax_amount'] : round(max(0, $subtotal - $discount) * ($taxPercent / 100), 2);
        $grandTotal = isset($stored['grand_total']) ? (float) $stored['grand_total'] : round(max(0, $subtotal - $discount) + $taxAmount + $shipping, 2);

        return [
            'quotation_number' => $stored['quotation_number'] ?? $quoteNumber,
            'date'             => $stored['date'] ?? $dateFormatted,
            'valid_until'      => $stored['valid_until'] ?? $validUntil,
            'client_name'      => $stored['client_name'] ?? $enquiry->name,
            'designation'      => $stored['designation'] ?? ($enquiry->designation ?? ''),
            'school'           => $stored['school'] ?? ($enquiry->school ?? ''),
            'email'            => $stored['email'] ?? ($enquiry->email ?? ''),
            'phone'            => $stored['phone'] ?? ($enquiry->phone ?? ''),
            'city'             => $stored['city'] ?? ($enquiry->city ?? ''),
            'address'          => $stored['address'] ?? ($enquiry->address ?? ''),
            'course_title'     => $stored['course_title'] ?? ($enquiry->course_title ?? ''),
            'items'            => $items,
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'tax_percent'      => $taxPercent,
            'tax_amount'       => $taxAmount,
            'shipping'         => $shipping,
            'grand_total'      => $grandTotal,
            'notes'            => $stored['notes'] ?? 'Thank you for your enquiry. We look forward to partnering with your institution to empower students with 21st-century experiential skills.',
            'terms'            => $stored['terms'] ?? "1. Quotation validity: 30 days from date of issuance.\n2. Payment terms: 50% advance along with purchase order, 50% upon delivery & installation.\n3. Taxes: GST as applicable.\n4. Delivery & setup: Within 2 to 3 weeks from receipt of confirmed PO and advance payment.\n5. Warranty & support: 1 Year comprehensive support and trainer orientation included.",
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // HELPER: PARSE / INITIALIZE PROFORMA INVOICE DATA
    // ──────────────────────────────────────────────────────────────

    private function parseProformaInvoice(CourseEnquiry $enquiry): array
    {
        $stored = $enquiry->proforma_invoice ? json_decode($enquiry->proforma_invoice, true) : null;
        if (is_array($stored) && !empty($stored)) {
            return $stored;
        }

        // If no PI exists yet, prefill from quotation or enquiry!
        $quotation = $this->parseQuotation($enquiry);
        $dateFormatted = date('Y-m-d');
        $dueDate       = date('Y-m-d', strtotime('+15 days'));
        $piNumber      = 'PI-' . ($enquiry->created_at ? $enquiry->created_at->format('Ym') : date('Ym')) . '-' . str_pad($enquiry->id, 4, '0', STR_PAD_LEFT);

        return [
            'invoice_number' => $piNumber,
            'date'           => $dateFormatted,
            'due_date'       => $dueDate,
            'client_name'    => $quotation['client_name'] ?? $enquiry->name,
            'designation'    => $quotation['designation'] ?? ($enquiry->designation ?? ''),
            'school'         => $quotation['school'] ?? ($enquiry->school ?? ''),
            'gstin'          => '',
            'email'          => $quotation['email'] ?? ($enquiry->email ?? ''),
            'phone'          => $quotation['phone'] ?? ($enquiry->phone ?? ''),
            'city'           => $quotation['city'] ?? ($enquiry->city ?? ''),
            'address'        => $quotation['address'] ?? ($enquiry->address ?? ''),
            'course_title'   => $quotation['course_title'] ?? ($enquiry->course_title ?? ''),
            'items'          => $quotation['items'] ?? [],
            'subtotal'       => $quotation['subtotal'] ?? 0,
            'discount'       => $quotation['discount'] ?? 0,
            'tax_percent'    => $quotation['tax_percent'] ?? 18,
            'tax_amount'     => $quotation['tax_amount'] ?? 0,
            'shipping'       => $quotation['shipping'] ?? 0,
            'grand_total'    => $quotation['grand_total'] ?? 0,
            // Default Bank Remittance Info
            'bank_name'      => 'HDFC Bank',
            'account_name'   => 'Skillvation EdTech Pvt Ltd',
            'account_number' => '50200012345678',
            'ifsc_code'      => 'HDFC0001234',
            'branch'         => 'Bengaluru Main Branch',
            'upi_id'         => 'skillvation@hdfcbank',
            'payment_terms'  => '100% advance against Proforma Invoice prior to dispatch and lab setup.',
            'notes'          => 'Subject to Bengaluru jurisdiction. Please mention the Proforma Invoice number during payment remittance and share payment receipt.',
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // HELPER: PARSE / INITIALIZE FINAL TAX INVOICE DATA
    // ──────────────────────────────────────────────────────────────

    private function parseTaxInvoice(CourseEnquiry $enquiry): array
    {
        $stored = $enquiry->invoice ? json_decode($enquiry->invoice, true) : null;
        if (is_array($stored) && !empty($stored)) {
            return $stored;
        }

        // Prefill from Proforma Invoice if available, else Quotation
        $source = $this->parseProformaInvoice($enquiry);
        if (empty($source['items'])) {
            $source = $this->parseQuotation($enquiry);
        }

        $dateFormatted = date('Y-m-d');
        $dueDate       = date('Y-m-d', strtotime('+7 days'));
        $invNumber     = 'INV-' . ($enquiry->created_at ? $enquiry->created_at->format('Ym') : date('Ym')) . '-' . str_pad($enquiry->id, 4, '0', STR_PAD_LEFT);

        return [
            'invoice_number' => $invNumber,
            'date'           => $dateFormatted,
            'due_date'       => $dueDate,
            'client_name'    => $source['client_name'] ?? $enquiry->name,
            'designation'    => $source['designation'] ?? ($enquiry->designation ?? ''),
            'school'         => $source['school'] ?? ($enquiry->school ?? ''),
            'gstin'          => $source['gstin'] ?? '',
            'pan'            => 'AAACS1234F',
            'email'          => $source['email'] ?? ($enquiry->email ?? ''),
            'phone'          => $source['phone'] ?? ($enquiry->phone ?? ''),
            'city'           => $source['city'] ?? ($enquiry->city ?? ''),
            'address'        => $source['address'] ?? ($enquiry->address ?? ''),
            'course_title'   => $source['course_title'] ?? ($enquiry->course_title ?? ''),
            'items'          => $source['items'] ?? [],
            'subtotal'       => $source['subtotal'] ?? 0,
            'discount'       => $source['discount'] ?? 0,
            'tax_percent'    => $source['tax_percent'] ?? 18,
            'tax_amount'     => $source['tax_amount'] ?? 0,
            'shipping'       => $source['shipping'] ?? 0,
            'grand_total'    => $source['grand_total'] ?? 0,
            // Default Bank Info
            'bank_name'      => $source['bank_name'] ?? 'HDFC Bank',
            'account_name'   => $source['account_name'] ?? 'Skillvation EdTech Pvt Ltd',
            'account_number' => $source['account_number'] ?? '50200012345678',
            'ifsc_code'      => $source['ifsc_code'] ?? 'HDFC0001234',
            'branch'         => $source['branch'] ?? 'Bengaluru Main Branch',
            'upi_id'         => $source['upi_id'] ?? 'skillvation@hdfcbank',
            'notes'          => 'Certified that the particulars given above are true and correct. Thank you for your business.',
            'terms'          => "1. All disputes subject to Bengaluru jurisdiction.\n2. Interest @ 18% p.a. will be charged if payment is delayed beyond due date.\n3. Goods supplied carry 1-year standard replacement/service warranty.",
            'payments'       => [],
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // HELPER: FETCH BUNDLE COMPONENTS (DIRECT DB + API FALLBACK)
    // ──────────────────────────────────────────────────────────────

    private function fetchBundleComponents(?string $slug): array
    {
        if (!$slug) return [];

        // 1. Try direct ClubShop database connection first
        $dbComponents = $this->fetchBundleComponentsFromDb($slug);
        if (!empty($dbComponents)) {
            return $dbComponents;
        }

        // 2. Fallback to API if DB did not yield items
        try {
            $localUrl = url('/club-shop/api/products/slug/' . $slug);
            $response = Http::timeout(6)->get($localUrl);

            if ($response->successful()) {
                return $response->json('data.bundle_components', []);
            }

            $apiBase  = rtrim(env('SHOP_API_BASE_URL', 'https://test.myskill.club/api/'), '/');
            $response = Http::timeout(6)->get($apiBase . '/products/slug/' . $slug);
            return $response->successful() ? $response->json('data.bundle_components', []) : [];
        } catch (\Throwable $e) {
            Log::warning('[AdminCourseEnquiry] Bundle fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    private function fetchBundleComponentsFromDb(string $slug): array
    {
        try {
            $bundle = DB::connection('clubshop')
                ->table('products')
                ->where('slug', $slug)
                ->where('is_deleted', 0)
                ->first();

            if (!$bundle) return [];

            $rows = DB::connection('clubshop')
                ->table('product_bundles as pb')
                ->join('products as p', 'p.id', '=', 'pb.component_product_id')
                ->leftJoin('product_details as pd', function ($j) {
                    $j->on('pd.product_id', '=', 'p.id')->where('pd.lang_id', '=', 1);
                })
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('category_lang as cl', function ($j) {
                    $j->on('cl.category_id', '=', 'c.id')->where('cl.lang_id', '=', 1);
                })
                ->where('pb.bundle_product_id', $bundle->id)
                ->where('p.is_deleted', 0)
                ->orderBy('pb.sort_order', 'asc')
                ->select([
                    'p.id as product_id',
                    'p.sku',
                    'p.price as regular_price',
                    'pb.quantity',
                    'pb.price_override',
                    'pd.title',
                    'cl.name as category_name',
                ])
                ->get();

            $components = [];
            foreach ($rows as $r) {
                $unitPrice = (float) (!empty($r->price_override) && (float) $r->price_override > 0 ? $r->price_override : $r->regular_price);
                $qty = (int) ($r->quantity ?? 1);
                if ($qty <= 0 || empty($r->title)) continue;

                $components[] = [
                    'product_id'        => (int) $r->product_id,
                    'sku'               => $r->sku ?? '',
                    'category_name'     => $r->category_name ?? 'General',
                    'title'             => $r->title,
                    'required_quantity' => $qty,
                    'unit_price'        => $unitPrice,
                    'hsn_code'          => '9023',
                ];
            }

            return $components;
        } catch (\Throwable $e) {
            Log::warning('[AdminCourseEnquiry] ClubShop DB bundle query failed: ' . $e->getMessage());
            return [];
        }
    }

    // ──────────────────────────────────────────────────────────────
    // CLUBSHOP AJAX: SEARCH PRODUCTS FOR LINE ITEMS
    // ──────────────────────────────────────────────────────────────

    public function searchClubShopProducts(Request $request)
    {
        $q = trim($request->input('q', ''));
        $categoryId = $request->input('category_id');

        try {
            $query = DB::connection('clubshop')
                ->table('products as p')
                ->leftJoin('product_details as pd', function ($j) {
                    $j->on('pd.product_id', '=', 'p.id')->where('pd.lang_id', '=', 1);
                })
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('category_lang as cl', function ($j) {
                    $j->on('cl.category_id', '=', 'c.id')->where('cl.lang_id', '=', 1);
                })
                ->where('p.is_deleted', 0)
                ->where('p.is_draft', 0);

            if ($q !== '') {
                $query->where(function ($sub) use ($q) {
                    $sub->where('pd.title', 'like', "%{$q}%")
                        ->orWhere('p.sku', 'like', "%{$q}%")
                        ->orWhere('cl.name', 'like', "%{$q}%");
                });
            }

            if (!empty($categoryId)) {
                $query->where('p.category_id', $categoryId);
            }

            $products = $query->select([
                'p.id as product_id',
                'p.sku',
                'p.price',
                'p.price_discounted',
                'pd.title',
                'cl.name as category_name',
                'c.id as category_id',
            ])
            ->orderBy('pd.title', 'asc')
            ->limit(50)
            ->get();

            $results = $products->map(function ($item) {
                $price = (float) (!empty($item->price_discounted) && (float) $item->price_discounted > 0 ? $item->price_discounted : $item->price);
                return [
                    'product_id'    => (int) $item->product_id,
                    'sku'           => $item->sku ?? '',
                    'title'         => $item->title ?? 'Untitled Product',
                    'category_name' => $item->category_name ?? 'General',
                    'unit_price'    => $price,
                    'hsn'           => '9023',
                ];
            });

            return response()->json(['success' => true, 'data' => $results]);
        } catch (\Throwable $e) {
            Log::error('[AdminCourseEnquiry] ClubShop search error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    public function getClubShopCatalog(Request $request)
    {
        try {
            $categories = DB::connection('clubshop')
                ->table('categories as c')
                ->leftJoin('category_lang as cl', function ($j) {
                    $j->on('cl.category_id', '=', 'c.id')->where('cl.lang_id', '=', 1);
                })
                ->select('c.id', 'cl.name')
                ->whereNotNull('cl.name')
                ->orderBy('cl.name', 'asc')
                ->get();

            $products = DB::connection('clubshop')
                ->table('products as p')
                ->leftJoin('product_details as pd', function ($j) {
                    $j->on('pd.product_id', '=', 'p.id')->where('pd.lang_id', '=', 1);
                })
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('category_lang as cl', function ($j) {
                    $j->on('cl.category_id', '=', 'c.id')->where('cl.lang_id', '=', 1);
                })
                ->where('p.is_deleted', 0)
                ->where('p.is_draft', 0)
                ->select([
                    'p.id as product_id',
                    'p.sku',
                    'p.price',
                    'p.price_discounted',
                    'pd.title',
                    'c.id as category_id',
                    'cl.name as category_name',
                ])
                ->orderBy('cl.name', 'asc')
                ->orderBy('pd.title', 'asc')
                ->get()
                ->map(function ($item) {
                    $price = (float) (!empty($item->price_discounted) && (float) $item->price_discounted > 0 ? $item->price_discounted : $item->price);
                    return [
                        'product_id'    => (int) $item->product_id,
                        'sku'           => $item->sku ?? '',
                        'title'         => $item->title ?? 'Untitled Product',
                        'category_id'   => $item->category_id,
                        'category_name' => $item->category_name ?? 'General',
                        'unit_price'    => $price,
                        'hsn'           => '9023',
                    ];
                });

            return response()->json([
                'success'    => true,
                'categories' => $categories,
                'products'   => $products,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // ──────────────────────────────────────────────────────────────
    // HELPER: AMOUNT IN WORDS (INDIAN SYSTEM)
    // ──────────────────────────────────────────────────────────────

    public static function amountInWords(float $amount): string
    {
        $amount   = round($amount, 2);
        $whole    = (int) floor($amount);
        $fraction = (int) round(($amount - $whole) * 100);

        $words = self::convertNumberToWordsIndian($whole);
        if (empty($words)) {
            $words = 'Zero';
        }
        $res = $words . ' Rupees';
        if ($fraction > 0) {
            $res .= ' and ' . self::convertNumberToWordsIndian($fraction) . ' Paise';
        }
        return $res . ' Only';
    }

    private static function convertNumberToWordsIndian(int $number): string
    {
        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
            14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
            18 => 'Eighteen', 19 => 'Nineteen',
        ];
        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
        ];

        if ($number === 0) return '';
        if ($number < 20) return $ones[$number];
        if ($number < 100) {
            return $tens[(int)($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        }
        if ($number < 1000) {
            return $ones[(int)($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . self::convertNumberToWordsIndian($number % 100) : '');
        }
        if ($number < 100000) {
            return self::convertNumberToWordsIndian((int)($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . self::convertNumberToWordsIndian($number % 1000) : '');
        }
        if ($number < 10000000) {
            return self::convertNumberToWordsIndian((int)($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . self::convertNumberToWordsIndian($number % 100000) : '');
        }
        return self::convertNumberToWordsIndian((int)($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . self::convertNumberToWordsIndian($number % 10000000) : '');
    }
}

