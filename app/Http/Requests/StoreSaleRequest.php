<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Or add your permission check like $this->user()->can('create_sales')
    }

    public function rules()
    {
        return [
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'nullable|string|max:20',
            'customer_email'  => 'nullable|email|max:255',
            'saleable_type'   => 'required|string',
            'saleable_id'     => 'required|integer',
            'product_variant' => 'nullable|string',
            'quantity'        => 'required|integer|min:1',
            'unit_price'      => 'required|numeric|min:0',
            'sale_date'       => 'required|date',
            'payment_amount'  => 'nullable|numeric|min:0',
            'payment_method'  => 'nullable|string',
            'notes'           => 'nullable|string',
            'due_date'        => 'nullable|date|after_or_equal:sale_date',
        ];
    }
}