<?php

namespace App\Services\Payments;

class StripePaymentGateway implements PaymentGateway
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function charge(array $data): array
    {
        return [
            'success' => false,
            'message' => 'Stripe has not been configured for this demo project yet.',
        ];
    }
}
