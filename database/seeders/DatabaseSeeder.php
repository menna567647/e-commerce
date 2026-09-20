<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Site Admin',
                'password' => bcrypt('password123'),
                'is_admin' => true,
            ]
        );

        $admin->update(['is_admin' => true]);

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => bcrypt('password123'),
            ]
        );

        $categories = [
            ['name' => 'Electronics', 'description' => 'Smart devices and gadgets for every day use.'],
            ['name' => 'Fashion', 'description' => 'Modern outfits for men and women.'],
            ['name' => 'Home & Living', 'description' => 'Comfort-focused essentials for your home.'],
            ['name' => 'Accessories', 'description' => 'Style and utility add-ons for your daily routine.'],
        ];

        $categoryMap = [];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                ['name' => $categoryData['name'], 'description' => $categoryData['description']]
            );

            $categoryMap[$categoryData['name']] = $category->id;
        }

        $products = [
            [
                'category' => 'Electronics',
                'name' => 'Aero Wireless Headphones',
                'description' => 'High-fidelity over-ear headphones with active noise cancellation and long battery life.',
                'short_description' => 'Noise cancelling wireless audio.',
                'price' => 159.99,
                'compare_price' => 249.99,
                'stock' => 40,
                'sku' => 'AER-100',
                'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=900&q=80',
                'is_featured' => true,
            ],
            [
                'category' => 'Electronics',
                'name' => 'Nova Smartwatch Pro',
                'description' => 'Track workouts, receive alerts, and monitor health with the integrated GPS smartwatch.',
                'short_description' => 'Fitness-first wearable tech.',
                'price' => 199.99,
                'compare_price' => 299.99,
                'stock' => 22,
                'sku' => 'NOVA-220',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=80',
                'is_featured' => true,
            ],
            [
                'category' => 'Fashion',
                'name' => 'Luma Everyday Jacket',
                'description' => 'A lightweight everyday jacket designed to transition from work to weekend effortlessly.',
                'short_description' => 'Premium casual outerwear.',
                'price' => 129.00,
                'compare_price' => 189.00,
                'stock' => 18,
                'sku' => 'LUMA-410',
                'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80',
                'is_featured' => true,
            ],
            [
                'category' => 'Fashion',
                'name' => 'Summit Leather Tote',
                'description' => 'Handcrafted leather tote with multiple compartments and a soft matte finish for daily commute.',
                'short_description' => 'Structured everyday carry.',
                'price' => 89.00,
                'compare_price' => 129.00,
                'stock' => 32,
                'sku' => 'SUMMIT-610',
                'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80',
                'is_featured' => false,
            ],
            [
                'category' => 'Home & Living',
                'name' => 'Arc Lamp Accent',
                'description' => 'Minimal arc lamp with warm ambient lighting and a compact footprint for stylish interiors.',
                'short_description' => 'Room lighting with modern appeal.',
                'price' => 74.50,
                'compare_price' => 109.00,
                'stock' => 14,
                'sku' => 'ARC-770',
                'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
                'is_featured' => true,
            ],
            [
                'category' => 'Home & Living',
                'name' => 'Contour Ceramic Set',
                'description' => 'Set of premium ceramic bowls and serving platters for elevated home dining experiences.',
                'short_description' => 'Elegant dining essentials.',
                'price' => 54.00,
                'compare_price' => 79.00,
                'stock' => 26,
                'sku' => 'CONTOUR-330',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80',
                'is_featured' => false,
            ],
            [
                'category' => 'Accessories',
                'name' => 'Drift Travel Backpack',
                'description' => 'Water-resistant travel backpack with padded straps and dedicated tech section.',
                'short_description' => 'Built for daily movement.',
                'price' => 95.00,
                'compare_price' => 135.00,
                'stock' => 28,
                'sku' => 'DRIFT-890',
                'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80',
                'is_featured' => true,
            ],
            [
                'category' => 'Accessories',
                'name' => 'Pace Sunglasses',
                'description' => 'UV-protected everyday sunglasses with lightweight frames and scratch-resistant lenses.',
                'short_description' => 'Polarized protective style.',
                'price' => 48.00,
                'compare_price' => 69.00,
                'stock' => 30,
                'sku' => 'PACE-211',
                'image' => 'https://images.unsplash.com/photo-1577803947579-9fa9c4f5b3df?auto=format&fit=crop&w=900&q=80',
                'is_featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                [
                    'category_id' => $categoryMap[$productData['category']],
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'price' => $productData['price'],
                    'compare_price' => $productData['compare_price'],
                    'stock' => $productData['stock'],
                    'image' => $productData['image'],
                    'is_featured' => $productData['is_featured'],
                    'is_active' => true,
                ]
            );
        }

        Coupon::firstOrCreate(
            ['code' => 'SAVE10'],
            [
                'discount_type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 50,
                'active' => true,
                'expires_at' => now()->addMonth(),
            ]
        );
    }
}
