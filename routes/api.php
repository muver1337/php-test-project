<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

Route::post('/orders/new', [OrderController::class, 'store'])->name('orders.store');
