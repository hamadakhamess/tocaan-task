<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGatewayInterface
{
    
    public function process(Order $order, float $amount): array;

    public function getName(): string;
}
