<?php

namespace App\Livewire\Product;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kelola Produk - BrewPOS')]
class ProductList extends Component
{
    use WithPagination;

    public $search = '';

    // Modal state
    public $showFormModal = false;
    public $isEditMode = false;
    public $selectedProductId = null;

    // Form inputs
    public $name = '';
    public $sku = '';
    public $category_id = '';
    public $price = 0;
    public $stock = 0;
    public $description = '';
    public $is_available = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $this->selectedProductId,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        
        // Auto generate SKU
        $this->sku = 'CF-' . str_pad(Product::count() + 1, 3, '0', STR_PAD_LEFT);
        $firstCat = Category::first();
        if ($firstCat) {
            $this->category_id = $firstCat->id;
        }

        $this->showFormModal = true;
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $this->selectedProductId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->category_id = $product->category_id;
        $this->price = (float) $product->price;
        $this->stock = $product->stock;
        $this->description = $product->description;
        $this->is_available = $product->is_available;

        $this->isEditMode = true;
        $this->showFormModal = true;
    }

    public function saveProduct()
    {
        $this->validate();

        if ($this->isEditMode) {
            $product = Product::findOrFail($this->selectedProductId);
            $product->update([
                'name' => $this->name,
                'sku' => $this->sku,
                'category_id' => $this->category_id,
                'price' => $this->price,
                'stock' => $this->stock,
                'description' => $this->description,
                'is_available' => $this->is_available,
            ]);
        } else {
            Product::create([
                'name' => $this->name,
                'sku' => $this->sku,
                'category_id' => $this->category_id,
                'price' => $this->price,
                'stock' => $this->stock,
                'description' => $this->description,
                'is_available' => $this->is_available,
            ]);
        }

        $this->closeModal();
    }

    public function toggleAvailable($id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'is_available' => !$product->is_available,
        ]);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }

    public function closeModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->selectedProductId = null;
        $this->name = '';
        $this->sku = '';
        $this->category_id = '';
        $this->price = 0;
        $this->stock = 0;
        $this->description = '';
        $this->is_available = true;
        $this->resetValidation();
    }

    public function render()
    {
        $products = Product::with('category')
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view('livewire.product.product-list', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
