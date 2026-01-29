<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\PaymentGatewayInterface;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function process(Order $order, float $amount): array
    {
        $config = config('payments.gateways.credit_card');
    
        $success = true; //call api this for test
        $transactionId = 'CC-' . uniqid();

        return [
            'success' => $success,
            'transaction_id' => $transactionId,
            'message' => 'Credit card payment processed successfully.',
        ];
    }

    public function getName(): string
    {
        return 'credit_card';
    }
}
