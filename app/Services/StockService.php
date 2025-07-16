<?php

namespace App\Services;

use DomainException;
use Illuminate\Support\Facades\DB;

class StockService
{
    public
    function decrementStock(int $productId, int $warehouseId, int $count, string $type, ?string $description = null): void
    {
        $affected = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('stock', '>=', $count)
            ->decrement('stock', $count);

        if (!$affected) throw new DomainException("Недостаточно товара на складе (product_id: $productId, warehouse_id: $warehouseId).");

        $newCount = DB::table('stocks')
            ->where('product_id', $productId)
            ->value('stock');

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

    public
    function incrementStock(int $productId, int $warehouseId, int $count, string $type, ?string $description = null): void
    {
        DB::table('stocks')
            ->where('product_id', $productId)
            ->increment('stock', $count);

        $newCount = DB::table('stocks')
            ->where('product_id', $productId)
            ->value('stock');

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
