<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class AdditionalProductsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name')->toArray();

        $products = [
            // Coffee Category
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Cafe Latte',
                'sku' => 'CF-004',
                'price' => 24000,
                'description' => 'Espresso arabika dipadu steamed fresh milk dengan foam halus',
                'stock' => 80,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 2000],
                    ['group' => 'Size', 'name' => 'Regular', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Large', 'extra' => 5000],
                ],
            ],
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Cappuccino',
                'sku' => 'CF-005',
                'price' => 24000,
                'description' => 'Espresso kaya rasa dengan lapisan busa susu tebal dan cocoa dusting',
                'stock' => 75,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 2000],
                ],
            ],
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Vanilla Latte',
                'sku' => 'CF-006',
                'price' => 27000,
                'description' => 'Espresso dengan sirup vanila Madagaskar dan steamed milk lembut',
                'stock' => 60,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Regular', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Large', 'extra' => 5000],
                ],
            ],
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Hazelnut Latte',
                'sku' => 'CF-007',
                'price' => 27000,
                'description' => 'Perpaduan espresso mantap, susu segar, dan sirup hazelnut harum',
                'stock' => 60,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Regular', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Large', 'extra' => 5000],
                ],
            ],
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Espresso Single / Double',
                'sku' => 'CF-008',
                'price' => 15000,
                'description' => 'Ekstrak murni biji kopi Arabica specialty dengan crema tebal',
                'stock' => 100,
                'variants' => [
                    ['group' => 'Shot', 'name' => 'Single', 'extra' => 0],
                    ['group' => 'Shot', 'name' => 'Double', 'extra' => 5000],
                ],
            ],
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Affogato',
                'sku' => 'CF-009',
                'price' => 23000,
                'description' => 'Satu scoop es krim vanilla premium disiram single shot espresso panas',
                'stock' => 40,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Coffee'] ?? 1,
                'name' => 'Cold Brew Classic',
                'sku' => 'CF-010',
                'price' => 26000,
                'description' => 'Ekstraksi dingin biji kopi arabika selama 16 jam, rasa smooth & fruity',
                'stock' => 45,
                'variants' => [
                    ['group' => 'Ice', 'name' => 'Normal Ice', 'extra' => 0],
                    ['group' => 'Ice', 'name' => 'Less Ice', 'extra' => 0],
                ],
            ],

            // Non-Coffee Category
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Chocolate Signature',
                'sku' => 'NC-002',
                'price' => 24000,
                'description' => 'Cokelat Belgia pekat dengan fresh milk creamy yang kaya rasa',
                'stock' => 70,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Regular', 'extra' => 0],
                    ['group' => 'Size', 'name' => 'Large', 'extra' => 5000],
                ],
            ],
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Red Velvet Latte',
                'sku' => 'NC-003',
                'price' => 25000,
                'description' => 'Minuman red velvet manis lembut dengan sentuhan vanila dan kakao',
                'stock' => 55,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Taro Milk Tea',
                'sku' => 'NC-004',
                'price' => 23000,
                'description' => 'Rasa taro manis gurih berpadu dengan susu segar dingin menyegarkan',
                'stock' => 60,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Earl Grey Milk Tea',
                'sku' => 'NC-005',
                'price' => 22000,
                'description' => 'Teh hitam Earl Grey aromatik bergamot disajikan dengan susu segar',
                'stock' => 60,
                'variants' => [
                    ['group' => 'Temperature', 'name' => 'Hot', 'extra' => 0],
                    ['group' => 'Temperature', 'name' => 'Ice', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Peach Iced Tea',
                'sku' => 'NC-006',
                'price' => 20000,
                'description' => 'Teh hitam seduh dingin dengan sari buah peach segar manis alami',
                'stock' => 80,
                'variants' => [
                    ['group' => 'Sugar', 'name' => 'Normal Sugar', 'extra' => 0],
                    ['group' => 'Sugar', 'name' => 'Less Sugar', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Lychee Iced Tea',
                'sku' => 'NC-007',
                'price' => 22000,
                'description' => 'Teh dingin aromatik dengan buah leci utuh segar',
                'stock' => 75,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Non-Coffee'] ?? 2,
                'name' => 'Lemon Mint Sparkling',
                'sku' => 'NC-008',
                'price' => 21000,
                'description' => 'Soda dingin menyegarkan dengan perasan jeruk lemon asli dan daun mint',
                'stock' => 50,
                'variants' => [],
            ],

            // Bakery & Dessert Category
            [
                'category_id' => $categories['Bakery & Dessert'] ?? 3,
                'name' => 'Almond Croissant',
                'sku' => 'BK-002',
                'price' => 26000,
                'description' => 'Croissant renyah dengan isian krim almond dan taburan irisan almond',
                'stock' => 25,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Bakery & Dessert'] ?? 3,
                'name' => 'Pain Au Chocolat',
                'sku' => 'BK-003',
                'price' => 24000,
                'description' => 'Pastry khas Prancis berlapis mentega dengan isian stik dark chocolate',
                'stock' => 30,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Bakery & Dessert'] ?? 3,
                'name' => 'Cinnamon Roll',
                'sku' => 'BK-004',
                'price' => 22000,
                'description' => 'Roti gulung kayu manis lembut dilapisi cream cheese glaze lumer',
                'stock' => 30,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Bakery & Dessert'] ?? 3,
                'name' => 'Fudgy Chocolate Brownie',
                'sku' => 'BK-005',
                'price' => 20000,
                'description' => 'Brownies cokelat panggang pekat tekstur fudgy lumer di mulut',
                'stock' => 35,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Bakery & Dessert'] ?? 3,
                'name' => 'Burnt Basque Cheesecake',
                'sku' => 'BK-006',
                'price' => 28000,
                'description' => 'Cheesecake lembut dengan permukaan karamel smoky khas Basque',
                'stock' => 20,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Bakery & Dessert'] ?? 3,
                'name' => 'Blueberry Muffin',
                'sku' => 'BK-007',
                'price' => 19000,
                'description' => 'Muffin empuk wangi mentega dengan taburan buah blueberry segar',
                'stock' => 30,
                'variants' => [],
            ],

            // Main Course Category
            [
                'category_id' => $categories['Main Course'] ?? 4,
                'name' => 'Spaghetti Carbonara',
                'sku' => 'MC-002',
                'price' => 35000,
                'description' => 'Pasta al dente saus krim kuning telur, keju parmesan, dan beef bacon gurih',
                'stock' => 35,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Main Course'] ?? 4,
                'name' => 'Spaghetti Aglio Olio',
                'sku' => 'MC-003',
                'price' => 32000,
                'description' => 'Pasta tumis bawang putih, minyak zaitun, cabai kering, dan potongan ayam',
                'stock' => 35,
                'variants' => [
                    ['group' => 'Spiciness', 'name' => 'Sedang', 'extra' => 0],
                    ['group' => 'Spiciness', 'name' => 'Pedas', 'extra' => 0],
                    ['group' => 'Spiciness', 'name' => 'Ekstra Pedas', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $categories['Main Course'] ?? 4,
                'name' => 'Chicken Katsu Curry Rice',
                'sku' => 'MC-004',
                'price' => 36000,
                'description' => 'Nasi hangat dengan fillet ayam renyah katsu dan kuah kari kental gurih',
                'stock' => 40,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Main Course'] ?? 4,
                'name' => 'Beef Teriyaki Donburi',
                'sku' => 'MC-005',
                'price' => 38000,
                'description' => 'Irisan daging sapi US shortplate saus teriyaki manis gurih di atas nasi hangat',
                'stock' => 30,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Main Course'] ?? 4,
                'name' => 'Nasi Ayam Geprek Sambal Bawang',
                'sku' => 'MC-006',
                'price' => 28000,
                'description' => 'Ayam krispi gurih digeprek sambal bawang pedas segar khas nusantara',
                'stock' => 45,
                'variants' => [
                    ['group' => 'Level', 'name' => 'Level 1 (Sedang)', 'extra' => 0],
                    ['group' => 'Level', 'name' => 'Level 3 (Pedas)', 'extra' => 0],
                    ['group' => 'Level', 'name' => 'Level 5 (Ekstra Pedas)', 'extra' => 0],
                ],
            ],
            [
                'category_id' => $categories['Main Course'] ?? 4,
                'name' => 'Creamy Mushroom Fettuccine',
                'sku' => 'MC-007',
                'price' => 34000,
                'description' => 'Fettuccine dengan saus krim lembut dan tumisan jamur champignon gurih',
                'stock' => 30,
                'variants' => [],
            ],

            // Snack Category
            [
                'category_id' => $categories['Snack'] ?? 5,
                'name' => 'Crispy Chicken Tenders',
                'sku' => 'SN-002',
                'price' => 24000,
                'description' => 'Filet dada ayam goreng tepung renyah dengan cocolan saus honey mustard',
                'stock' => 40,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Snack'] ?? 5,
                'name' => 'Truffle Cheese Fries',
                'sku' => 'SN-003',
                'price' => 25000,
                'description' => 'Kentang goreng renyah disiram minyak truffle aromatik dan parutan keju',
                'stock' => 40,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Snack'] ?? 5,
                'name' => 'Nachos Supreme Cheese Dip',
                'sku' => 'SN-004',
                'price' => 26000,
                'description' => 'Keripik tortilla jagung dengan saus keju cheddar leleh dan salsa segar',
                'stock' => 35,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Snack'] ?? 5,
                'name' => 'Pisang Goreng Crispy Keju Cokelat',
                'sku' => 'SN-005',
                'price' => 18000,
                'description' => 'Pisang manis goreng tepung kremes dengan taburan keju cheddar & susu cokelat',
                'stock' => 40,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Snack'] ?? 5,
                'name' => 'Cireng Krispi Bumbu Rujak',
                'sku' => 'SN-006',
                'price' => 16000,
                'description' => 'Cireng gurih renyah di luar kenyal di dalam dengan bumbu rujak pedas manis',
                'stock' => 50,
                'variants' => [],
            ],
            [
                'category_id' => $categories['Snack'] ?? 5,
                'name' => 'Roti Bakar Cokelat Keju',
                'sku' => 'SN-007',
                'price' => 20000,
                'description' => 'Roti panggang tebal isi mentega harum, meses cokelat pekat, dan keju melimpah',
                'stock' => 40,
                'variants' => [],
            ],
        ];

        foreach ($products as $pData) {
            $variants = $pData['variants'];
            unset($pData['variants']);

            $product = Product::firstOrCreate(
                ['sku' => $pData['sku']],
                $pData
            );

            foreach ($variants as $var) {
                ProductVariant::firstOrCreate([
                    'product_id' => $product->id,
                    'variant_group' => $var['group'],
                    'name' => $var['name'],
                ], [
                    'extra_price' => $var['extra'],
                ]);
            }
        }
    }
}
