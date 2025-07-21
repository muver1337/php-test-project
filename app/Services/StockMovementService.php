<?php

namespace App\Services;

use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockMovementService
{
    public function getFilteredMovements(Request $request)
    {
        $query = StockMovement::with(['product', 'warehouse']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }

        if ($request->filled('from')) {
            $from = Carbon::parse($request->input('from'))->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('to')) {
            $to = Carbon::parse($request->input('to'))->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        $perPage = (int)$request->input('per_page', 15);

        return $query->paginate($perPage);
    }
}
