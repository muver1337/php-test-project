<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/warehouses', [WarehouseController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);

Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/new', 'store');
    Route::get('/{order}', 'show');
    Route::patch('/{order}', 'update');
    Route::patch('/{order}/complete', 'complete');
    Route::patch('/{order}/canceled', 'canceled');
    Route::patch('/{order}/return', 'return');
});

Route::get('/movement', [StockMovementController::class, 'index']);
