<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'pincode',
        'city',
        'state',
        'country',
        'address',
        'address_type',
        'payment_method',
        'payment_id',
        'payment_status',
        'payment_verified_at',
        'order_status',
        'subtotal',
        'shipping_cost',
        'discount',
        'total_amount',
        'currency',
        'tracking_number',
        'courier_partner',
        'admin_notes',
    ];

    protected $casts = ['payment_verified_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(ShopOrderItem::class, 'shop_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedTotalAttribute()
    {
        return '₹ ' . number_format($this->total_amount, 2);
    }
}
