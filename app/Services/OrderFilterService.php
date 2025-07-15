<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrderFilterService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->input('status'));
        }
        if ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->input('date_from'));
        }
        if ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->input('date_to'));
        }
        return $query;
    }
}
