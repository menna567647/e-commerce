<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2])
            ->assertStatus(200)
            ->assertJsonPath('data.items.0.product.id', $product->id)
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_same_product_increases_quantity_instead_of_duplicate(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 3])
            ->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 5);

        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_cart(): void
    {
        $this->getJson('/api/v1/cart')->assertStatus(401);
    }

    public function test_quantity_cannot_exceed_available_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 5])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Only 3 item(s) of '.$product->name.' are available.');
    }

    public function test_update_cart_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/v1/cart/'.$product->id, ['quantity' => 4])
            ->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 4);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    }

    public function test_remove_cart_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/cart/'.$product->id)
            ->assertStatus(200)
            ->assertJsonPath('message', 'Cart cleared successfully.');

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_clear_cart(): void
    {
        $user = User::factory()->create();
        $productOne = Product::factory()->create(['stock' => 10, 'is_active' => true]);
        $productTwo = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $productOne->id, 'quantity' => 1]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $productTwo->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/cart')
            ->assertStatus(200)
            ->assertJsonPath('message', 'Cart cleared successfully.');

        $this->assertDatabaseCount('carts', 0);
    }

    public function test_user_isolation(): void
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($userOne, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($userTwo, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($userOne, 'sanctum')
            ->getJson('/api/v1/cart')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.items');

        $this->assertDatabaseHas('carts', ['user_id' => $userOne->id, 'product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseHas('carts', ['user_id' => $userTwo->id, 'product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_checkout_clears_persistent_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'price' => 100, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders/checkout', [
                'shipping_name' => 'Jane Doe',
                'shipping_email' => 'jane@example.com',
                'shipping_phone' => '123456789',
                'shipping_address' => '123 Main Street',
                'shipping_city' => 'Boston',
                'shipping_state' => 'MA',
                'shipping_postal_code' => '02108',
                'shipping_country' => 'US',
                'payment_method' => 'card',
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertDatabaseCount('carts', 0);
    }

    public function test_deleting_user_cascades_cart_records(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $user->delete();

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_deleting_product_cascades_related_cart_records(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', ['product_id' => $product->id, 'quantity' => 2]);

        $product->delete();

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
