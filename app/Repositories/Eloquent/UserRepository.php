<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function register(array $data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function attemptLogin(array $credentials)
    {
        return Auth::guard('api')->attempt($credentials);
    }

    public function getCurrentUser()
    {
        return Auth::guard('api')->user();
    }

    public function logout()
    {
        return Auth::guard('api')->logout();
    }

    public function refresh()
    {
        return Auth::guard('api')->refresh();
    }
}
