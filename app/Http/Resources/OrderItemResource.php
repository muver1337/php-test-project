<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'count' => $this->count,
            'product' => $this->whenLoaded('product', function() {
                return [
                    'name' => $this->product->name,
                    'price' => $this->product->price,
                ];
            }),
        ];
    }
}
