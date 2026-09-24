<?php

namespace Tests\Feature;

use App\Livewire\Product\ProductList;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_product_image_when_creating_product(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'icon' => 'coffee',
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->image('cappuccino.jpg', 500, 500);

        Livewire::actingAs($user)
            ->test(ProductList::class)
            ->set('name', 'Hot Cappuccino')
            ->set('sku', 'CF-001')
            ->set('category_id', $category->id)
            ->set('price', 25000)
            ->set('stock', 20)
            ->set('image', $file)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $product = Product::where('sku', 'CF-001')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_can_update_product_and_replace_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'icon' => 'coffee',
            'is_active' => true,
        ]);

        $oldFile = UploadedFile::fake()->image('old_photo.jpg', 400, 400);
        $oldPath = $oldFile->store('products', 'public');

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Latte',
            'sku' => 'CF-002',
            'price' => 28000,
            'stock' => 15,
            'image' => $oldPath,
            'is_available' => true,
        ]);

        $newFile = UploadedFile::fake()->image('new_latte.jpg', 600, 600);

        Livewire::actingAs($user)
            ->test(ProductList::class)
            ->call('editProduct', $product->id)
            ->set('image', $newFile)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $product->refresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_image_validation_rejects_files_larger_than_2mb(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'icon' => 'coffee',
            'is_active' => true,
        ]);

        // 3MB image
        $largeFile = UploadedFile::fake()->create('huge.jpg', 3000, 'image/jpeg');

        Livewire::actingAs($user)
            ->test(ProductList::class)
            ->set('name', 'Latte Big')
            ->set('sku', 'CF-003')
            ->set('category_id', $category->id)
            ->set('price', 30000)
            ->set('stock', 10)
            ->set('image', $largeFile)
            ->call('saveProduct')
            ->assertHasErrors(['image' => 'max']);
    }
}
