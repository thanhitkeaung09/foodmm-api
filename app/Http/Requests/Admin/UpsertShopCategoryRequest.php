<?php

namespace App\Http\Requests\Admin;

use App\Models\RestaurantCategory;
use App\Models\ShopCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertShopCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                Rule::unique(ShopCategory::class, 'name')->ignore($this->shop_category),
            ],
        ];
    }
}
