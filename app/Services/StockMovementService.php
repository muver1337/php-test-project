<?php

namespace App\Services;

use App\Models\StockMovement;
use Illuminate\Http\Request;

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
            $query->where('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to'));
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $allowedSortFields = ['created_at', 'count', 'type'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');

        $perPage = $request->input('per_page', 15);

        return $query->paginate($perPage);
    }
}
