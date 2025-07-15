<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use DomainException;

class OrderService
{
    private function decrementStock(int $productId, int $warehouseId, int $count): void
    {
        $affected = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('stock', '>=', $count)
            ->decrement('stock', $count);

        if (!$affected) {
            throw new DomainException("Недостаточно товара на складе (product_id: $productId, warehouse_id: $warehouseId).");
        }
    }

    private function incrementStock(int $productId, int $warehouseId, int $count): void
    {
        DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->increment('stock', $count);
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer' => $data['customer'],
                'warehouse_id' => $data['warehouse_id'],
                'status' => 'active',
            ]);

            foreach ($data['items'] as $item) {
                $this->decrementStock($item['product_id'], $data['warehouse_id'], $item['count']);

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
                $this->incrementStock($item->product_id, $order->warehouse_id, $item->count);
            }

            $order->items()->delete();

            $order->update([
                'customer' => $data['customer'],
            ]);

            foreach ($data['items'] as $item) {
                $this->decrementStock($item['product_id'], $order->warehouse_id, $item['count']);

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

    public function canceledOrder (Order $order): Order
    {
        if ($order->status !== 'active') {
            throw new DomainException('Можно отменить только активный заказ');
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->incrementStock($item->product_id, $order->warehouse_id, $item->count);
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
                $this->decrementStock($item->product_id, $order->warehouse_id, $item->count);
            }

            $order->update(['status' => 'active']);

            return $order->load('items');
        });
    }
}
