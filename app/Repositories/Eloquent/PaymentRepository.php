<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\Payments\PaymentGatewayFactory;
use Exception;

class PaymentRepository extends EloquentBaseRepository implements PaymentRepositoryInterface
{
    protected $orderRepository;

    public function __construct(
        Payment $payment,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($payment);
        $this->orderRepository = $orderRepository;
    }

    public function getPaginatedForOrder($orderId, $perPage = 10)
    {
        return $this->model->when($orderId, function ($query, $orderId) {
            return $query->where('order_id', $orderId);
        })->paginate($perPage);
    }

    public function store(array $data)
    {
        $order = $this->orderRepository->find($data['order_id']);

        if ($order->status !== 'confirmed') {
            throw new Exception('Payments can only be processed for confirmed orders.');
        }

        $gateway = PaymentGatewayFactory::make($data['method']);
        $result = $gateway->process($order, $data['amount']);

        return $this->create([
            'order_id' => $order->id,
            'transaction_id' => $result['transaction_id'],
            'status' => $result['success'] ? 'successful' : 'failed',
            'method' => $data['method'],
            'amount' => $data['amount'],
        ]);
    }
}
