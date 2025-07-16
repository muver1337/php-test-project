<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/new', 'store')->name('store');
    Route::patch('/{order}', 'update')->name('update');
    Route::patch('/{order}/complete', 'complete')->name('complete');
    Route::patch('/{order}/canceled', 'canceled')->name('canceled');
    Route::patch('/{order}/return', 'return')->name('return');
});

Route::get('/movement', [StockMovementController::class, 'index']);
