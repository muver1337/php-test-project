<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use DomainException;
use App\Services\StockService;

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
            foreach ($order->items as $item) {
                $newCount = $data['items'][$item->product_id]['count'] ?? 0;
                $difference = $newCount - $item->count;

                if ($difference !== 0) {
                    $method = $difference > 0 ? 'decrementStock' : 'incrementStock';
                    $this->stockService->$method(
                        $item->product_id,
                        $order->warehouse_id,
                        abs($difference),
                        'order_update',
                        "Обновление заказа #{$order->id}"
                    );
                }
            }

            $order->items()->delete();
            $order->update(['customer' => $data['customer'], 'warehouse_id' => $data['warehouse_id']]);

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
                $this->stockService->decrementStock($item->product_id, $order->warehouse_id, $item->count,
                    'order_return',
                    "Возобновление заказа #{$order->id}");
            }

            $order->update(['status' => 'active']);

            return $order->load('items');
        });
    }
}
