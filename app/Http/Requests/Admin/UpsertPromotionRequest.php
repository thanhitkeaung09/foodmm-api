<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpsertPromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'images' => ['sometimes', 'array'],
            'label' => ['required', 'string'],
            'status' => ['required', 'string'],
            'period' => ['required', 'string'],
            'shop_id' => ['required_without:restaurant_id'],
            'restaurant_id' => ['required_without:shop_id'],
            'food_ids' => ['array'],
            'description' => ['required', 'string'],
        ];
    }
}
