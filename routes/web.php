<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
