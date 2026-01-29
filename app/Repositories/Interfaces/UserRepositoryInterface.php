<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function register(array $data);

    public function attemptLogin(array $credentials);

    public function getCurrentUser();

    public function logout();

    public function refresh();
}
