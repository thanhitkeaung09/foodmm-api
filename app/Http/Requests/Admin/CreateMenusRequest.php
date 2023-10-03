<?php

namespace App\Http\Requests\Admin;

use App\Models\Food;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMenusRequest extends FormRequest
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
            'food_ids' => ['array'],
            'food_ids.*' => ['integer', Rule::exists(Food::class, 'id')],
        ];
    }
}
