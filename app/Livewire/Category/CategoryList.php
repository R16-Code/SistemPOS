<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kategori Menu - BrewPOS')]
class CategoryList extends Component
{
    public function render()
    {
        $categories = Category::withCount('products')->get();

        return view('livewire.category.category-list', [
            'categories' => $categories,
        ]);
    }
}
