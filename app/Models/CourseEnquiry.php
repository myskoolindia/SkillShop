<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnquiry extends Model
{
    protected $fillable = [
        'course_id',
        'api_course_id',
        'course_title',
        'name',
        'designation',
        'email',
        'phone',
        'school',
        'city',
        'address',
        'message',
        'quotation',
        'proforma_invoice',
        'invoice',
        'source',
        'status',
    ];

    /**
     * Get decoded quotation data array
     */
    public function getQuotationDataAttribute(): ?array
    {
        if (empty($this->quotation)) {
            return null;
        }
        $data = json_decode($this->quotation, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Get decoded proforma invoice data array
     */
    public function getProformaInvoiceDataAttribute(): ?array
    {
        if (empty($this->proforma_invoice)) {
            return null;
        }
        $data = json_decode($this->proforma_invoice, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Get decoded final invoice data array
     */
    public function getInvoiceDataAttribute(): ?array
    {
        if (empty($this->invoice)) {
            return null;
        }
        $data = json_decode($this->invoice, true);
        return is_array($data) ? $data : null;
    }

    public function hasQuotation(): bool
    {
        return !empty($this->quotation);
    }

    public function hasProformaInvoice(): bool
    {
        return !empty($this->proforma_invoice);
    }

    public function hasInvoice(): bool
    {
        return !empty($this->invoice);
    }

    /**
     * Quotation is locked once a Proforma Invoice or final Invoice has been created
     */
    public function isQuotationLocked(): bool
    {
        return $this->hasProformaInvoice() || $this->hasInvoice();
    }

    /**
     * Proforma Invoice is locked once a final Tax Invoice has been created
     */
    public function isProformaLocked(): bool
    {
        return $this->hasInvoice();
    }

    /**
     * Current version of Proforma Invoice
     */
    public function getProformaVersionAttribute(): int
    {
        return (int) ($this->proforma_invoice_data['version'] ?? 1);
    }

    /**
     * List of historical revisions/versions of the Proforma Invoice
     */
    public function getProformaVersionsListAttribute(): array
    {
        return $this->proforma_invoice_data['versions'] ?? [];
    }

    /**
     * Computes payment collection summary for the final invoice
     */
    public function getInvoicePaymentSummaryAttribute(): array
    {
        $inv = $this->invoice_data;
        $payments = (array) ($inv['payments'] ?? []);

        // Fallback: If no invoice payments recorded in DB yet, check linked shop order
        if (empty($payments)) {
            $cleanPhone = substr(preg_replace('/[^0-9]/', '', (string)$this->phone), -10);
            if (!empty($cleanPhone)) {
                try {
                    $linkedOrder = ShopOrder::where('customer_phone', 'like', "%{$cleanPhone}%")
                        ->latest('id')
                        ->first();
                    if ($linkedOrder && $linkedOrder->payment_status === 'paid' && (float)$linkedOrder->total_amount > 0) {
                        $paidAmt   = (float)$linkedOrder->total_amount;
                        $payDate   = $linkedOrder->created_at ? $linkedOrder->created_at->format('Y-m-d') : date('Y-m-d');
                        $payId     = 'pay_' . substr(md5($linkedOrder->id . $linkedOrder->order_number), 0, 8);
                        $method    = $linkedOrder->payment_method ? ucfirst($linkedOrder->payment_method) : 'Razorpay';
                        $txnId     = $linkedOrder->payment_id ?: $linkedOrder->order_number;
                        $receiptNo = 'RCP-' . date('Ymd', strtotime($payDate)) . '-' . strtoupper(substr(md5($linkedOrder->order_number), 0, 4));

                        $payments[] = [
                            'id'              => $payId,
                            'payment_id'      => $payId,
                            'receipt_no'      => $receiptNo,
                            'receipt_number'  => $receiptNo,
                            'amount'          => $paidAmt,
                            'date'            => $payDate,
                            'payment_date'    => $payDate,
                            'method'          => $method,
                            'payment_method'  => $method,
                            'transaction_id'  => $txnId,
                            'transaction_ref' => $txnId,
                            'type'            => 'advance',
                            'notes'           => 'Pre-Booking Advance Token via Shop Order #' . $linkedOrder->order_number,
                            'recorded_by'     => 'Online / Razorpay',
                            'recorded_at'     => $linkedOrder->created_at ? $linkedOrder->created_at->toDateTimeString() : now()->toDateTimeString(),
                        ];
                    }
                } catch (\Throwable $e) {}
            }
        }

        $grandTotal = (float) ($inv['grand_total'] ?? 0.0);
        if ($grandTotal <= 0 && !empty($this->proforma_invoice_data['grand_total'])) {
            $grandTotal = (float)$this->proforma_invoice_data['grand_total'];
        } elseif ($grandTotal <= 0 && !empty($this->quotation_data['grand_total'])) {
            $grandTotal = (float)$this->quotation_data['grand_total'];
        } elseif ($grandTotal <= 0 && !empty($payments)) {
            // Default lab package quote price: 150000 + 18% GST = 177000
            $grandTotal = 177000.0;
        }

        $totalPaid = 0.0;
        $advancePaid = 0.0;

        foreach ($payments as $p) {
            $amt = (float) ($p['amount'] ?? 0);
            $totalPaid += $amt;
            if (($p['type'] ?? '') === 'advance') {
                $advancePaid += $amt;
            }
        }

        $balanceDue = max(0.0, $grandTotal - $totalPaid);

        if ($totalPaid <= 0) {
            $status = 'unpaid';
        } elseif ($balanceDue <= 0.01) {
            $status = 'paid';
        } else {
            $status = 'partially_paid';
        }

        return [
            'grand_total'    => $grandTotal,
            'total_paid'     => $totalPaid,
            'advance_paid'   => $advancePaid,
            'balance_due'    => $balanceDue,
            'payment_status' => $status,
            'payments'       => $payments,
        ];
    }
}
