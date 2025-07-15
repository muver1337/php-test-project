<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'created_at' => $this->created_at,
            'completed_at' => $this->completed_at,
            'warehouse_id' => $this->warehouse_id,
            'status' => $this->status,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
