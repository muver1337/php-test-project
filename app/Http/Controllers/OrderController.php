<?php

namespace App\Http\Controllers;

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

    public function store(Request $request, OrderService $orderService)
    {
        $validated = $request->validate([
            'customer' => 'required|string|max:255',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.count' => 'required|integer|min:1',
        ]);

        $order = $orderService->createOrder($validated);

        return response()->json([
            'message' => 'Заказ успешно создан',
            'order' => $order->load('items'),
        ], 201);
    }

    public function update(Request $request, Order $order, OrderService $orderService)
    {
        if ($request->has('status')) {
            return response()->json([
                'message' => 'Поле "status" нельзя изменять.'
            ], 422);
        }

        $validated = $request->validate([
            'customer' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.count' => 'required|integer|min:1',
        ]);

        $orderService->updateOrder($order, $validated);

        return response()->json(['message' => 'Заказ обновлён']);
    }
}
