<?php

namespace App\Services\Payments;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function charge(array $data): array;
}
