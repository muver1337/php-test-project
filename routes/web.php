<?php

use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
