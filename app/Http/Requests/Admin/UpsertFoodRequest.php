<?php

namespace App\Http\Requests\Admin;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertFoodRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique(Food::class)->ignore($this->food)],
            'food_type_id' => ['required', 'integer', Rule::exists(FoodType::class, 'id')],
            'ingredients' => ['nullable', 'string'],
            'vitamins' => ['nullable', 'string'],
            'calories' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'images' => ['sometimes', 'array'],
        ];
    }
}
