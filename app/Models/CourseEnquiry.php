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
        if (!$inv) {
            return [
                'grand_total'    => 0.0,
                'total_paid'     => 0.0,
                'advance_paid'   => 0.0,
                'balance_due'    => 0.0,
                'payment_status' => 'unpaid',
                'payments'       => [],
            ];
        }

        $grandTotal = (float) ($inv['grand_total'] ?? 0.0);
        $payments = (array) ($inv['payments'] ?? []);

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
