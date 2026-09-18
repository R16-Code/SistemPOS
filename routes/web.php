<?php

use App\Livewire\Category\CategoryList;
use App\Livewire\Order\OrderList;
use App\Livewire\Pos\Cashier;
use App\Livewire\Product\ProductList;
use App\Livewire\Report\ReportIndex;
use App\Livewire\Table\TableList;
use Illuminate\Support\Facades\Route;

// Kasir POS Route (Default Main Screen)
Route::get('/', Cashier::class)->name('pos');

// Additional POS Routes
Route::get('/orders', OrderList::class)->name('orders');
Route::get('/products', ProductList::class)->name('products');
Route::get('/categories', CategoryList::class)->name('categories');
Route::get('/tables', TableList::class)->name('tables');
Route::get('/reports', ReportIndex::class)->name('reports');
