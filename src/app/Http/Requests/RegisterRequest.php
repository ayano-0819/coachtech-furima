<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            // 未入力
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
            'password_confirmation.required' => '確認用パスワードを入力してください',

            // メール形式
            'email.email' => 'メールアドレスはメール形式で入力してください',

            // パスワード文字数
            'password.min' => 'パスワードは8文字以上で入力してください',

            // 確認用パスワード不一致
            'password.confirmed' => 'パスワードと一致しません',
        ];
    }   
}