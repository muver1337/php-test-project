<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $order = Order::create([
                'customer' => $data['customer'],
                'warehouse_id' => $data['warehouse_id'],
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                ]);
            }

            return $order;
        });
    }

    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'customer' => $data['customer'],
            ]);

            $order->items()->delete();

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
            throw new \DomainException('Можно завершить только активный заказ');
        }

        $order->status = 'completed';
        $order->completed_at = now();
        $order->save();

        return $order->load('items');
    }

    public function canceledOrder(Order $order): Order
    {
        if ($order->status !== 'active') {
            throw new \DomainException('Можно отменить только активный заказ');
        }

        $order->status = 'canceled';
        $order->save();

        return $order->load('items');

    }

    public function returnOrder(Order $order): Order
    {
        if ($order->status !== 'canceled') {
            throw new \DomainException('Можно возобновить только отменённый заказ');
        }

        $order->status = 'active';
        $order->save();

        return $order->load('items');

    }
}
