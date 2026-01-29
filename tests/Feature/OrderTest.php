<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class OrderTest extends TestCase
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

    public function test_can_create_order()
    {
        $payload = [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'items' => [
                ['product_name' => 'Laptop', 'quantity' => 1, 'price' => 1000],
                ['product_name' => 'Mouse', 'quantity' => 2, 'price' => 25],
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.total_amount', 1050);
        
        $this->assertDatabaseHas('orders', ['customer_email' => 'jane@example.com']);
        $this->assertDatabaseHas('order_items', ['product_name' => 'Laptop']);
    }

    public function test_can_filter_orders_by_status()
    {
        Order::create(['customer_name' => 'User 1', 'customer_email' => 'u1@ex.com', 'total_amount' => 100, 'status' => 'pending']);
        Order::create(['customer_name' => 'User 2', 'customer_email' => 'u2@ex.com', 'total_amount' => 200, 'status' => 'confirmed']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/orders?status=confirmed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.status', 'confirmed');
    }

    public function test_cannot_delete_order_with_payments()
    {
        $order = Order::create(['customer_name' => 'User 1', 'customer_email' => 'u1@ex.com', 'total_amount' => 100, 'status' => 'confirmed']);
        $order->payments()->create([
            'transaction_id' => 'TRANS-123',
            'status' => 'successful',
            'method' => 'paypal',
            'amount' => 100
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/orders/' . $order->id);

        $response->assertStatus(400);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }
}
