<?php

namespace App\Livewire\Product;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Kelola Produk - BrewPOS')]
class ProductList extends Component
{
    use WithFileUploads;
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

    // Image upload state
    public $image = null;

    public $existingImage = null;

    public $removeImage = false;

    // Variant options state
    public $hasVariants = false;

    public $variants = [];

    public function updatedHasVariants($value)
    {
        if ($value && empty($this->variants)) {
            $this->addVariantRow();
        }
    }

    public function addVariantRow($group = '', $name = '', $extraPrice = 0)
    {
        $this->variants[] = [
            'variant_group' => $group,
            'name' => $name,
            'extra_price' => $extraPrice,
        ];
    }

    public function removeVariantRow($index)
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addPreset($type)
    {
        $this->hasVariants = true;

        if ($type === 'temperature') {
            $this->addVariantRow('Suhu', 'Hot', 0);
            $this->addVariantRow('Suhu', 'Ice', 2000);
        } elseif ($type === 'size') {
            $this->addVariantRow('Ukuran', 'Regular', 0);
            $this->addVariantRow('Ukuran', 'Large', 4000);
        } elseif ($type === 'sugar') {
            $this->addVariantRow('Gula', 'Normal Sugar', 0);
            $this->addVariantRow('Gula', 'Less Sugar', 0);
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,'.$this->selectedProductId,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
            'hasVariants' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];

        if ($this->hasVariants) {
            $rules['variants'] = 'required|array|min:1';
            $rules['variants.*.variant_group'] = 'required|string|max:100';
            $rules['variants.*.name'] = 'required|string|max:100';
            $rules['variants.*.extra_price'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'variants.min' => 'Tambahkan minimal 1 baris opsi varian jika opsi varian diaktifkan.',
            'variants.*.variant_group.required' => 'Grup varian wajib diisi (misal: Suhu atau Ukuran).',
            'variants.*.name.required' => 'Nama opsi wajib diisi (misal: Hot, Ice, Large).',
            'variants.*.extra_price.required' => 'Tambahan harga wajib diisi (isi 0 jika gratis).',
            'variants.*.extra_price.numeric' => 'Tambahan harga harus berupa angka.',
            'variants.*.extra_price.min' => 'Tambahan harga minimal 0.',
            'image.image' => 'File harus berupa gambar (JPG, JPEG, PNG, atau WebP).',
            'image.max' => 'Ukuran file gambar maksimal 2MB.',
        ];
    }

    public function updatedCategoryId($value)
    {
        if (! $this->isEditMode && $value) {
            $this->generateSkuForCategory($value);
        }
    }

    public function generateSkuForCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if (! $category) {
            return;
        }

        $name = strtolower($category->name);
        $prefix = 'PR';

        if (str_contains($name, 'coffee') && ! str_contains($name, 'non')) {
            $prefix = 'CF';
        } elseif (str_contains($name, 'non-coffee') || str_contains($name, 'non coffee')) {
            $prefix = 'NC';
        } elseif (str_contains($name, 'bakery') || str_contains($name, 'dessert')) {
            $prefix = 'BK';
        } elseif (str_contains($name, 'main') || str_contains($name, 'course')) {
            $prefix = 'MC';
        } elseif (str_contains($name, 'snack')) {
            $prefix = 'SN';
        } else {
            $words = explode(' ', $name);
            if (count($words) >= 2) {
                $prefix = strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
            } else {
                $prefix = strtoupper(substr($name, 0, 2));
            }
        }

        $existingSkus = Product::where('sku', 'like', $prefix.'-%')->pluck('sku')->toArray();
        $number = 1;
        while (in_array($prefix.'-'.str_pad($number, 3, '0', STR_PAD_LEFT), $existingSkus)) {
            $number++;
        }

        $this->sku = $prefix.'-'.str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;

        $firstCat = Category::first();
        if ($firstCat) {
            $this->category_id = $firstCat->id;
            $this->generateSkuForCategory($firstCat->id);
        }

        $this->showFormModal = true;
    }

    public function editProduct($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $this->selectedProductId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->category_id = $product->category_id;
        $this->price = (float) $product->price;
        $this->stock = $product->stock;
        $this->description = $product->description;
        $this->is_available = (bool) $product->is_available;

        if ($product->variants->isNotEmpty()) {
            $this->hasVariants = true;
            $this->variants = $product->variants->map(function ($v) {
                return [
                    'variant_group' => $v->variant_group,
                    'name' => $v->name,
                    'extra_price' => (float) $v->extra_price,
                ];
            })->toArray();
        } else {
            $this->hasVariants = false;
            $this->variants = [];
        }

        $this->isEditMode = true;
        $this->existingImage = $product->image;
        $this->image = null;
        $this->removeImage = false;
        $this->showFormModal = true;
    }

    public function deleteExistingImage()
    {
        $this->removeImage = true;
        $this->existingImage = null;
    }

    public function clearNewImage()
    {
        $this->image = null;
    }

    public function saveProduct()
    {
        $this->validate();

        $imagePath = $this->existingImage;

        if ($this->image) {
            if ($this->existingImage && Storage::disk('public')->exists($this->existingImage)) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $imagePath = ImageOptimizer::optimizeAndStore($this->image, 'products', 800);
        } elseif ($this->removeImage) {
            if ($this->existingImage && Storage::disk('public')->exists($this->existingImage)) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $imagePath = null;
        }

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
                'image' => $imagePath,
            ]);
        } else {
            $product = Product::create([
                'name' => $this->name,
                'sku' => $this->sku,
                'category_id' => $this->category_id,
                'price' => $this->price,
                'stock' => $this->stock,
                'description' => $this->description,
                'is_available' => $this->is_available,
                'image' => $imagePath,
            ]);
        }

        if ($this->hasVariants) {
            $product->variants()->delete();
            foreach ($this->variants as $variant) {
                $product->variants()->create([
                    'variant_group' => trim($variant['variant_group']),
                    'name' => trim($variant['name']),
                    'extra_price' => (float) $variant['extra_price'],
                ]);
            }
        } else {
            $product->variants()->delete();
        }

        $this->closeModal();
    }

    public function toggleAvailable($id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'is_available' => ! $product->is_available,
        ]);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
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
        $this->hasVariants = false;
        $this->variants = [];
        $this->image = null;
        $this->existingImage = null;
        $this->removeImage = false;
        $this->resetValidation();
    }

    public function render()
    {
        $products = Product::with(['category', 'variants'])
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
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
