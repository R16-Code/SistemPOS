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

    public function render()
    {
        $products = Product::with('category')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('sku', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.product.product-list', [
            'products' => $products,
        ]);
    }
}
