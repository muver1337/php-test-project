<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
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
        $orders = $filteredQuery->with(['items.product'])->paginate($perPage)->appends($request->query());
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $order = $orderService->createOrder($request->validated());
        $order->load('items.product');
        return response()->json([
            'message' => 'Заказ успешно создан',
            'order' => new OrderResource($order),
        ], 201);
    }

    public function update(UpdateOrderRequest $request, Order $order, OrderService $orderService)
    {
        $orderService->updateOrder($order, $request->validated());

        return response()->json(['message' => 'Заказ обновлён']);
    }

    public function complete(Order $order, OrderService $orderService)
    {
        try {
            $completedOrder = $orderService->completeOrder($order);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'Заказ завершён',
            'order' => new OrderResource($completedOrder),
        ]);
    }
}
