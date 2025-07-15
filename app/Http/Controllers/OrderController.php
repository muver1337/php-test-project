<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderFilterService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request, OrderFilterService $filterService)
    {
        $query = Order::query();
        $filteredQuery = $filterService->apply($query);
        $perPage = $request->input('per_page', 10);
        $orders = $filteredQuery->paginate($perPage)->appends($request->query());
        return response()->json([
            'data' => $orders,
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $order = $orderService->createOrder($request->validated());

        return response()->json([
            'message' => 'Заказ успешно создан',
            'order' => $order->load('items'),
        ], 201);
    }

    public function update(UpdateOrderRequest $request, Order $order, OrderService $orderService)
    {
        $orderService->updateOrder($order, $request->validated());

        return response()->json(['message' => 'Заказ обновлён']);
    }
}
