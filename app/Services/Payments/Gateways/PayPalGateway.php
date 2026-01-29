<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\PaymentGatewayInterface;

class PayPalGateway implements PaymentGatewayInterface
{
    public function process(Order $order, float $amount): array
    {
        $config = config('payments.gateways.paypal');
        // $clientId = $config['client_id'];

        $success = true;//call api this for test
        $transactionId = 'PP-' . uniqid();

        return [
            'success' => $success,
            'transaction_id' => $transactionId,
            'message' => 'PayPal payment processed successfully.',
        ];
    }

    public function getName(): string
    {
        return 'paypal';
    }
}
