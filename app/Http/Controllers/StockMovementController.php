<?php

namespace App\Http\Controllers;

use App\Services\StockMovementService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected StockMovementService $service;

    public function __construct(StockMovementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->service->getFilteredMovements($request);
    }
}
