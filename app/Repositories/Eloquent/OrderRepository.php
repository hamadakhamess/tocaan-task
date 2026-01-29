<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository extends EloquentBaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function getPaginatedWithStatus($status, $perPage = 10)
    {
        return $this->model->with('items')
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate($perPage);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = collect($data['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            $order = $this->model->create([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }

            return $order->load('items');
        });
    }
}
