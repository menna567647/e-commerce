<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_catalog_filter_returns_matching_products(): void
    {
        $products = Product::active()->filter([
            'search' => 'headphone',
        ])->get();

        $this->assertNotEmpty($products);
        $this->assertTrue($products->contains(fn ($product) => str_contains(strtolower($product->name), 'headphone')));
    }
}
