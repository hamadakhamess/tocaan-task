<?php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginatedForOrder($orderId, $perPage = 10);

    public function store(array $data);
}
