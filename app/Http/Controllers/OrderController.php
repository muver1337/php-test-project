<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFilterService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request, OrderFilterService $filterService)
    {
        $query = Order::query();
        $filteredQuery = $filterService->apply($query);
        $perPage = $request->input('per_page', 10);
        $orders = $filteredQuery->paginate($perPage)->appends($request->query());
        return view('order', compact('orders'));
    }
}
