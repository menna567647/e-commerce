<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function resolveCoupon(?string $code, float $subtotal): ?Coupon
    {
        if (blank($code)) {
            return null;
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();

        if (! $coupon || ! $coupon->isValidFor($subtotal)) {
            return null;
        }

        return $coupon;
    }

    public function calculateDiscount(?string $code, float $subtotal): float
    {
        $coupon = $this->resolveCoupon($code, $subtotal);

        if (! $coupon) {
            return 0.0;
        }

        return (float) $coupon->calculateDiscount($subtotal);
    }
}
