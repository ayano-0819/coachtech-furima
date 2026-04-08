<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method' => ['required', 'in:convenience,card'],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法を正しく選択してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            $postalCode = session('postal_code', $user?->postal_code);
            $address = session('address', $user?->address);

            $hasAddressError = empty($postalCode) || empty($address);
            $hasPaymentError = empty($this->payment_method);

            if ($hasPaymentError && $hasAddressError) {
                $validator->errors()->add('shipping_address', '配送先を入力してください');
            }
        });
    }
}
