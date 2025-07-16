<?php

namespace App\Services;

use DomainException;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function decrementStock(int $productId, int $warehouseId, int $count, string $type, ?string $description = null): void
    {
        $currentStock = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->value('stock');

        if ($currentStock === null) {
            throw new DomainException("Запись о запасе не найдена для product_id={$productId}, warehouse_id={$warehouseId}");
        }

        if ($currentStock < $count) {
            throw new DomainException("Недостаточно товара на складе (product_id: $productId, warehouse_id: $warehouseId).");
        }

        DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->decrement('stock', $count);

        $newCount = $currentStock - $count;

        DB::table('stock_movements')->insert([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'quantity_change' => -$count,
            'count' => $newCount,
            'type' => $type,
            'description' => $description,
            'created_at' => now(),
        ]);
    }

    public function incrementStock(int $productId, int $warehouseId, int $count, string $type, ?string $description = null): void
    {
        // Блокируем запись
        $currentStock = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->value('stock');

        if ($currentStock === null) {
            throw new DomainException("Запись о запасе не найдена для product_id={$productId}, warehouse_id={$warehouseId}");
        }

        DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->increment('stock', $count);

        $newCount = $currentStock + $count;

        DB::table('stock_movements')->insert([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'quantity_change' => $count,
            'count' => $newCount,
            'type' => $type,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
