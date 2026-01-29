<?php

namespace App\Services\Payments;

use App\Services\Payments\Gateways\CreditCardGateway;
use App\Services\Payments\Gateways\PayPalGateway;
use Exception;

class PaymentGatewayFactory
{
    protected static $gateways = [
        'credit_card' => CreditCardGateway::class,
        'paypal' => PayPalGateway::class,
    ];

    
    public static function make(string $method): PaymentGatewayInterface
    {
        if (!isset(self::$gateways[$method])) {
            throw new Exception("Payment method '{$method}' is not supported.");
        }

        $gatewayClass = self::$gateways[$method];
        return new $gatewayClass();
    }

    public static function registerGateway(string $method, string $class)
    {
        self::$gateways[$method] = $class;
    }
}
