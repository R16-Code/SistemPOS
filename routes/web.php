<?php

use App\Livewire\Auth\Login;
use App\Livewire\Kitchen\KitchenOrders;
use App\Livewire\Order\OrderList;
use App\Livewire\Pos\Cashier;
use App\Livewire\Product\ProductList;
use App\Livewire\Report\ReportIndex;
use App\Livewire\Table\TableList;
use App\Livewire\User\UserList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Tamu / Belum Login
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Rute Terautentikasi (Harus Login)
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');

    // Kasir POS & Meja: Admin & Kasir
    Route::get('/', Cashier::class)->name('pos')->middleware('role:admin,cashier');
    Route::get('/tables', TableList::class)->name('tables')->middleware('role:admin,cashier');

    // Antrian Dapur / Kitchen Display System: Admin & Kitchen
    Route::get('/kitchen', KitchenOrders::class)->name('kitchen')->middleware('role:admin,kitchen');

    // Daftar Transaksi: Admin, Kasir, & Kitchen
    Route::get('/orders', OrderList::class)->name('orders')->middleware('role:admin,cashier,kitchen');

    // Khusus Admin / Owner: Kelola Menu Produk, Laporan Keuangan, & Manajemen User
    Route::get('/products', ProductList::class)->name('products')->middleware('role:admin');
    Route::get('/reports', ReportIndex::class)->name('reports')->middleware('role:admin');
    Route::get('/users', UserList::class)->name('users')->middleware('role:admin');
});
