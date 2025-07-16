<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DomainException;

class OrderService
{
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer' => $data['customer'],
                'warehouse_id' => $data['warehouse_id'],
                'status' => 'active',
                'created_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                $this->stockService->decrementStock(
                    $item['product_id'],
                    $data['warehouse_id'],
                    $item['count'],
                    'order_create',
                    "Создание заказа #{$order->id}"
                );

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                ]);
            }

            return $order->load('items');
        });
    }

    public function updateOrder(Order $order, array $data): Order
    {
        if ($order->status !== 'active') {
            throw new DomainException('Можно обновлять только активный заказ');
        }

        return DB::transaction(function () use ($order, $data) {
            $newCounts = [];
            foreach ($data['items'] as $item) {
                $newCounts[$item['product_id']] = $item['count'];
            }

            foreach ($order->items as $item) {
                $newCount = $newCounts[$item->product_id] ?? 0;
                $difference = $newCount - $item->count;

                if ($difference !== 0) {
                    if ($difference > 0) {
                        $this->stockService->decrementStock(
                            $item->product_id,
                            $order->warehouse_id,
                            $difference,
                            'order_update',
                            "Увеличение количества товара в заказе #{$order->id}"
                        );
                    } else {
                        $this->stockService->incrementStock(
                            $item->product_id,
                            $order->warehouse_id,
                            abs($difference),
                            'order_update',
                            "Уменьшение количества товара в заказе #{$order->id}"
                        );
                    }
                }
            }

            // Удаляем старые items и создаём новые
            $order->items()->delete();
            $order->update([
                'customer' => $data['customer'],
                'warehouse_id' => $data['warehouse_id'] ?? $order->warehouse_id,
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                ]);
            }

            return $order->load('items');
        });
    }

    public function completeOrder(Order $order): Order
    {
        if ($order->status !== 'active') {
            throw new DomainException('Можно завершить только активный заказ');
        }

        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $order->load('items');
    }

    public function canceledOrder(Order $order): Order
    {
        if ($order->status !== 'active') {
            throw new DomainException('Можно отменить только активный заказ');
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->stockService->incrementStock(
                    $item->product_id,
                    $order->warehouse_id,
                    $item->count,
                    'order_cancel',
                    "Отмена заказа #{$order->id}"
                );
            }

            $order->update(['status' => 'canceled']);

            return $order->load('items');
        });
    }

    public function returnOrder(Order $order): Order
    {
        if ($order->status !== 'canceled') {
            throw new DomainException('Можно возобновить только отменённый заказ');
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->stockService->decrementStock(
                    $item->product_id,
                    $order->warehouse_id,
                    $item->count,
                    'order_return',
                    "Возобновление заказа #{$order->id}"
                );
            }

            $order->update(['status' => 'active']);

            return $order->load('items');
        });
    }

    public function getFilteredOrders(Request $request)
    {
        $query = Order::with('items.product');

        if ($request->filled('customer')) {
            $query->where('customer', 'like', '%' . $request->input('customer') . '%');
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to'));
        }

        $perPage = (int)$request->input('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
