<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_can_process_paypal_payment_for_confirmed_order()
    {
        $order = Order::create([
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'total_amount' => 1000,
            'status' => 'confirmed'
        ]);

        $payload = [
            'order_id' => $order->id,
            'method' => 'paypal',
            'amount' => 1000
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payments', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'successful')
            ->assertJsonPath('data.method', 'paypal');
        
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'method' => 'paypal']);
    }

    public function test_cannot_process_payment_for_pending_order()
    {
        $order = Order::create([
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'total_amount' => 1000,
            'status' => 'pending'
        ]);

        $payload = [
            'order_id' => $order->id,
            'method' => 'paypal',
            'amount' => 1000
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payments', $payload);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Payments can only be processed for confirmed orders.');
    }

    public function test_cannot_process_payment_with_invalid_method()
    {
        $order = Order::create([
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'total_amount' => 1000,
            'status' => 'confirmed'
        ]);

        $payload = [
            'order_id' => $order->id,
            'method' => 'invalid_method',
            'amount' => 1000
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payments', $payload);

        $response->assertStatus(400)
            ->assertJson(['message' => "Payment method 'invalid_method' is not supported."]);
    }
}
