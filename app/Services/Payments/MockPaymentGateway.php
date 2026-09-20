<?php

namespace App\Services\Payments;

class MockPaymentGateway implements PaymentGateway
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function charge(array $data): array
    {
        $amount = (float) ($data['amount'] ?? 0);

        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'The order total must be greater than zero.',
            ];
        }

        return [
            'success' => true,
            'transaction_id' => 'mock_'.uniqid(),
            'amount' => $amount,
            'currency' => $data['currency'] ?? 'USD',
            'payment_method' => $data['payment_method'] ?? 'card',
        ];
    }
}
