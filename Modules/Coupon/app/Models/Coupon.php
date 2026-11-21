<?php

namespace Modules\Coupon\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Coupon\app\Models\CouponHistory;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'course_ids'           => 'array',
        'category_ids'         => 'array',
        'offer_percentage'     => 'float',
        'discount_amount'      => 'float',
        'max_discount_amount'  => 'float',
        'min_price'            => 'float',
        'times_used'           => 'integer',
        'usage_limit_total'    => 'integer',
        'usage_limit_per_user' => 'integer',
    ];

    public function isExpired(): bool
    {
        if (empty($this->expired_date)) {
            return false; // Lifetime / never expires
        }
        return $this->expired_date < date('Y-m-d');
    }

    public function hasStarted(): bool
    {
        if (empty($this->start_date)) {
            return true;
        }
        return $this->start_date <= date('Y-m-d');
    }

    public function hasReachedTotalLimit(): bool
    {
        if (!empty($this->usage_limit_total) && $this->usage_limit_total > 0) {
            return ($this->times_used ?? 0) >= $this->usage_limit_total;
        }
        return false;
    }

    public function hasReachedUserLimit($userId): bool
    {
        if (empty($userId) || empty($this->usage_limit_per_user) || $this->usage_limit_per_user <= 0) {
            return false;
        }
        $userUsage = CouponHistory::where('coupon_id', $this->id)
            ->where('user_id', $userId)
            ->count();
        return $userUsage >= $this->usage_limit_per_user;
    }

    public function histories()
    {
        return $this->hasMany(CouponHistory::class, 'coupon_id');
    }
}
