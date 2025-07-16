<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\StockMovementService;
use App\Models\StockMovement;
use Illuminate\Http\Request;


class StockMovementController extends Controller
{
    public function __construct(StockMovementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->service->getFilteredMovements($request);
    }

    protected StockMovementService $service;

}
