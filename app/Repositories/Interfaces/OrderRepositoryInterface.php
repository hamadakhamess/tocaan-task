<?php

namespace App\Repositories\Interfaces;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginatedWithStatus($status, $perPage = 10);

    public function store(array $data);
}
