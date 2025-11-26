<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PurchaseCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coupon_id' => ['required', 'exists:coupons,id'],
            'delivery_methods' => ['required', 'array'],
            'delivery_methods.*' => ['in:sms,email,whatsapp,print'],
            'payment_method' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_id.required' => 'Please select a coupon',
            'coupon_id.exists' => 'The selected coupon is invalid',
            'delivery_methods.required' => 'Please select at least one delivery method',
            'delivery_methods.array' => 'Delivery methods must be an array',
            'delivery_methods.*.in' => 'Invalid delivery method selected',
        ];
    }
}
