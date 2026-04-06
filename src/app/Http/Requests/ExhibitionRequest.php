<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'price' => str_replace(',', '', $this->price),
        ]);
    }

    public function rules()
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'name' => ['required', 'string'],
            'brand_name' => ['nullable', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '商品画像を選択してください',
            'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',

            'categories.required' => 'カテゴリーを選択してください',
            'categories.array' => 'カテゴリーを正しく選択してください',
            'categories.*.exists' => '選択されたカテゴリーが不正です',

            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.exists' => '選択された商品の状態が不正です',

            'name.required' => '商品名を入力してください',

            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',

            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格は数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}