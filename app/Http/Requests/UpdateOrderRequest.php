<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        'customer' => 'required|string|max:255',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.count' => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('status')) {
                $validator->errors()->add('status', 'Поле "status" нельзя изменять.');
            }
        });
    }
}
