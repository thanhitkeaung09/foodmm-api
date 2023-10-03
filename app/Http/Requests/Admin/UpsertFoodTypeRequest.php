<?php

namespace App\Http\Requests\Admin;

use App\Models\FoodCategory;
use App\Models\FoodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertFoodTypeRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique(FoodType::class)->ignore($this->food_type)],
            'category_id' => ['required', 'integer', Rule::exists(FoodCategory::class, 'id')],
        ];
    }
}
