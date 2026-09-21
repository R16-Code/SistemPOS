<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users
        User::create([
            'name' => 'Admin Cafe',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Budi Kasir',
            'email' => 'kasir@pos.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dapur Barista',
            'email' => 'kitchen@pos.com',
            'password' => Hash::make('password'),
            'role' => 'kitchen',
            'is_active' => true,
        ]);

        // 2. Seed Categories
        $categories = [
            ['name' => 'Coffee', 'icon' => 'coffee'],
            ['name' => 'Non-Coffee', 'icon' => 'cup-soda'],
            ['name' => 'Bakery & Dessert', 'icon' => 'cake'],
            ['name' => 'Main Course', 'icon' => 'utensils'],
            ['name' => 'Snack', 'icon' => 'cookie'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $createdCat = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'is_active' => true,
            ]);
            $catMap[$cat['name']] = $createdCat->id;
        }

        // 3. Seed Tables
        $tables = [
            ['table_number' => 'Meja 01', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'Meja 02', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'Meja 03', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'Meja 04', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => 'Meja 05', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'VIP 1', 'capacity' => 8, 'status' => 'available'],
        ];

        foreach ($tables as $t) {
            Table::create($t);
        }

        // 4. Seed Products
        $products = [
            [
                'category_id' => $catMap['Coffee'],
                'name' => 'Kopi Susu Gula Aren',
                'sku' => 'CF-001',
                'price' => 22000,
                'description' => 'Espresso house blend + Susu Segar + Gula Aren Asli',
                'stock' => 100,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Regular', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Large', 'extra' => 5000],
                ],
            ],
            [
                'category_id' => $catMap['Coffee'],
                'name' => 'Americano',
                'sku' => 'CF-002',
                'price' => 18000,
                'description' => 'Double shot espresso dengan air mineral segar',
                'stock' => 100,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 2000],
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $catMap['Coffee'],
                'name' => 'Caramel Macchiato',
                'sku' => 'CF-003',
                'price' => 28000,
                'description' => 'Espresso, steamed milk, vanilla syrup & caramel drizzle',
                'stock' => 50,
                'variants' => [],
            ],
            [
                'category_id' => $catMap['Non-Coffee'],
                'name' => 'Matcha Latte',
                'sku' => 'NC-001',
                'price' => 25000,
                'description' => 'Pure Uji Matcha import dari Jepang & fresh milk',
                'stock' => 50,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $catMap['Bakery & Dessert'],
                'name' => 'Butter Croissant',
                'sku' => 'BK-001',
                'price' => 20000,
                'description' => 'Flaky French Butter Croissant fresh baked',
                'stock' => 30,
                'variants' => [],
            ],
            [
                'category_id' => $catMap['Main Course'],
                'name' => 'Nasi Goreng Special Cafe',
                'sku' => 'MC-001',
                'price' => 32000,
                'description' => 'Nasi goreng gurih dengan telur mata sapi & sosis premium',
                'stock' => 40,
                'variants' => [],
            ],
            [
                'category_id' => $catMap['Snack'],
                'name' => 'French Fries Sea Salt',
                'sku' => 'SN-001',
                'price' => 18000,
                'description' => 'Kentang goreng renyah bumbu sea salt & mayo dip',
                'stock' => 50,
                'variants' => [],
            ],
        ];

        foreach ($products as $pData) {
            $variants = $pData['variants'];
            unset($pData['variants']);

            $product = Product::create($pData);

            foreach ($variants as $var) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_group' => $var['group'],
                    'name' => $var['name'],
                    'extra_price' => $var['extra'],
                ]);
            }
        }

        $this->call(AdditionalProductsSeeder::class);
    }
}
