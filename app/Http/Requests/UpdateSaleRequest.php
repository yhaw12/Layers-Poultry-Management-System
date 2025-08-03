<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check(); // Ensure the user is authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'saleable_type' => ['required', Rule::in(['App\Models\Bird', 'App\Models\Egg'])],
            'saleable_id' => ['required', 'integer', 'exists:'.($this->saleable_type === 'App\Models\Bird' ? 'birds' : 'eggs').',id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'sale_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'due_date' => ['nullable', 'date', 'after_or_equal:sale_date'],
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'saleable_type.required' => 'The saleable type is required.',
            'saleable_type.in' => 'The saleable type must be either Bird or Egg.',
            'saleable_id.required' => 'The saleable ID is required.',
            'saleable_id.exists' => 'The selected item does not exist.',
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
            'unit_price.required' => 'The unit price is required.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.min' => 'The unit price must be at least 0.01.',
            'sale_date.required' => 'The sale date is required.',
            'sale_date.date' => 'The sale date must be a valid date.',
            'customer_name.required' => 'The customer name is required.',
            'customer_name.string' => 'The customer name must be a string.',
            'customer_name.max' => 'The customer name may not be greater than 255 characters.',
            'customer_phone.max' => 'The customer phone may not be greater than 20 characters.',
            'customer_email.email' => 'The customer email must be a valid email address.',
            'customer_email.max' => 'The customer email may not be greater than 255 characters.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after_or_equal' => 'The due date must be on or after the sale date.',
        ];
    }
}