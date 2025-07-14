<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Add authorization logic if needed
    }

    public function rules()
{
    return [
        'saleable_type' => 'required|in:App\Models\Egg,App\Models\Bird',
        'saleable_id' => [
            'required',
            'exists:'.strtolower(class_basename($this->saleable_type)).'s,id',
        ],
        'quantity' => [
            'required',
            'numeric',
            'min:1',
            function ($attribute, $value, $fail) {
                $saleable = app($this->saleable_type)::find($this->saleable_id);
                if ($saleable && $value > $saleable->quantity) {
                    $fail("The quantity exceeds available stock ({$saleable->quantity}).");
                }
            },
        ],
        'unit_price' => 'required|numeric|min:0',
    ];
}

    public function messages()
    {
        return [
            'saleable_id.exists' => 'The selected item does not exist in the specified category.',
        ];
    }
}