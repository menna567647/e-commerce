<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com');

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_user_can_login_and_get_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email' => 'api-user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $login = $this->postJson('/api/v1/login', [
            'email' => 'api-user@example.com',
            'password' => 'password123',
        ]);

        $login->assertStatus(200)
            ->assertJsonPath('data.email', 'api-user@example.com');

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/user')
            ->assertStatus(200)
            ->assertJsonPath('data.email', 'api-user@example.com');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertStatus(200)
            ->assertJsonPath('message', 'Logged out successfully.');
    }

    public function test_products_and_categories_are_available_via_api(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id, 'is_active' => true]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Widget Pro Deluxe',
            'price' => 99.99,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/categories')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);

        $this->getJson('/api/v1/products?search=Widget')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'price']]]);
    }

    public function test_products_can_be_filtered_by_search_and_price(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'ZT-DELUXE-ALPHA-77',
            'price' => 120.00,
            'stock' => 15,
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'BUDGET-SPEAKER-NOVA',
            'price' => 50.00,
            'stock' => 12,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/products?search=ZT-DELUXE-ALPHA-77&min_price=100&max_price=200');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->assertSame('ZT-DELUXE-ALPHA-77', $response->json('data.0.name'));
    }

    public function test_authenticated_user_can_manage_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2])
            ->assertStatus(200)
            ->assertJsonPath('data.items.0.product.id', $product->id)
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/v1/cart/'.$product->id, ['quantity' => 4])
            ->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 4);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/cart/'.$product->id)
            ->assertStatus(200)
            ->assertJsonPath('message', 'Cart cleared successfully.');
    }

    public function test_cart_rejects_stock_exceeding_available_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 5])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Only 3 item(s) of '.$product->name.' are available.');
    }

    public function test_coupon_validation_works_for_valid_and_invalid_codes(): void
    {
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE25',
            'discount_type' => 'percentage',
            'value' => 10,
            'minimum_amount' => 50,
            'active' => true,
            'expires_at' => now()->addDays(7),
        ]);

        $this->postJson('/api/v1/coupons/validate', ['code' => 'SAVE25', 'subtotal' => 100])
            ->assertStatus(200)
            ->assertJsonPath('data.discount', 10.0);

        $this->postJson('/api/v1/coupons/validate', ['code' => 'INVALID', 'subtotal' => 100])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Invalid or inactive coupon code.');
    }

    public function test_user_can_checkout_and_create_order(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum');
        session()->put('cart', [
            $product->id => ['product_id' => $product->id, 'quantity' => 2],
        ]);

        $response = $this->postJson('/api/v1/orders/checkout', [
            'shipping_name' => 'Jane Doe',
            'shipping_email' => 'jane@example.com',
            'shipping_phone' => '123456789',
            'shipping_address' => '123 Main Street',
            'shipping_city' => 'Boston',
            'shipping_state' => 'MA',
            'shipping_postal_code' => '02108',
            'shipping_country' => 'US',
            'payment_method' => 'card',
            'coupon_code' => null,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.total', 200);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertEquals(3, Product::find($product->id)->stock);
    }

    public function test_order_stock_validation_blocks_unavailable_inventory(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2, 'is_active' => true]);

        $this->actingAs($user, 'sanctum');
        session()->put('cart', [
            $product->id => ['product_id' => $product->id, 'quantity' => 3],
        ]);

        $this->postJson('/api/v1/orders/checkout', [
            'shipping_name' => 'Jane Doe',
            'shipping_email' => 'jane@example.com',
            'shipping_phone' => '123456789',
            'shipping_address' => '123 Main Street',
            'shipping_city' => 'Boston',
            'shipping_state' => 'MA',
            'shipping_postal_code' => '02108',
            'shipping_country' => 'US',
            'payment_method' => 'card',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Only 2 item(s) of '.$product->name.' are available.');
    }

    public function test_user_can_cancel_their_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders/'.$order->id.'/cancel')
            ->assertStatus(200)
            ->assertJsonPath('message', 'Order cancelled successfully.')
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_user_cannot_access_another_users_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser, 'sanctum')
            ->getJson('/api/v1/orders/'.$order->id)
            ->assertStatus(403);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->getJson('/api/v1/user')
            ->assertStatus(401);
    }
}
