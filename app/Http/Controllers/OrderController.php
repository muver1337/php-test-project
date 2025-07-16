<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request, OrderService $orderService)
    {
        $orders = $orderService->getFilteredOrders($request);
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

    public function show(Order $order)
    {
        $order->load('items.product');

        return response()->json([
            'order' => new OrderResource($order),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order, OrderService $orderService)
    {
        try {
            $orderService->updateOrder($order, $request->validated());

            return response()->json(['message' => 'Заказ обновлён']);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function complete(Order $order, OrderService $orderService)
    {
        try {
            $completedOrder = $orderService->completeOrder($order);

            return response()->json([
                'message' => 'Заказ завершён',
                'order' => new OrderResource($completedOrder),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function canceled(Order $order, OrderService $orderService)
    {
        try {
            $canceledOrder = $orderService->canceledOrder($order);

            return response()->json([
                'message' => 'Заказ отменён',
                'order' => new OrderResource($canceledOrder),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function return(Order $order, OrderService $orderService)
    {
        try {
            $returnOrder = $orderService->returnOrder($order);

            return response()->json([
                'message' => 'Заказ возобновлён',
                'order' => new OrderResource($returnOrder),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
