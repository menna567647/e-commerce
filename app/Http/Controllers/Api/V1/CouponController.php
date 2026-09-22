<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CouponValidationRequest;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService)
    {
    }

    public function validateCoupon(CouponValidationRequest $request): JsonResponse
    {
        $subtotal = (float) $request->subtotal;
        $coupon = $this->couponService->resolveCoupon($request->code, $subtotal);

        if (! $coupon) {
            return response()->json([
                'message' => 'Invalid or inactive coupon code.',
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return response()->json([
            'message' => 'Coupon applied successfully.',
            'data' => [
                'code' => $coupon->code,
                'discount' => round($discount, 2),
                'subtotal' => round($subtotal, 2),
                'total' => round(max(0, $subtotal - $discount), 2),
            ],
        ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }
}
