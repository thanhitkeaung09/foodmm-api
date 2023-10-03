<?php

namespace App\Http\Requests\Admin;

use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\Township;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertShopRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique(Shop::class)->ignore($this->shop)],
            'description' => ['required', 'string'],
            'phones' => ['nullable', 'string'],
            'opening_hours' => ['array'],
            'opening_hours.*' => ['string'],
            'category_id' => ['required', 'integer', Rule::exists(ShopCategory::class, 'id')],
            'township_id' => ['required', 'integer', Rule::exists(Township::class, 'id')],
            'address' => ['required', 'string'],
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'images' => ['sometimes', 'array'],
        ];
    }
}
