<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'operator_id' => ['sometimes', 'integer', 'min:1'],
            'country_id' => ['sometimes', 'integer', 'min:1'],
            'plan_type_id' => ['sometimes', 'integer', 'min:1'],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0'],
            'denomination' => ['sometimes', 'numeric', 'min:0'],
            'in_stock' => ['sometimes', 'boolean'],
            'sort_by' => ['sometimes', 'in:denomination,selling_price,created_at,popularity'],
            'sort_order' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'operator_id.integer' => 'Operator ID must be an integer.',
            'country_id.integer' => 'Country ID must be an integer.',
            'plan_type_id.integer' => 'Plan type ID must be an integer.',
            'min_price.numeric' => 'Minimum price must be a number.',
            'max_price.numeric' => 'Maximum price must be a number.',
            'sort_by.in' => 'Sort by must be one of: denomination, selling_price, created_at, popularity.',
            'sort_order.in' => 'Sort order must be asc or desc.',
            'per_page.integer' => 'Per page must be an integer.',
        ];
    }
}
