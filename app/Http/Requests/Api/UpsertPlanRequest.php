<?php

namespace App\Http\Requests\Api;

use App\Rules\CheckOwnCollection;
use Illuminate\Foundation\Http\FormRequest;

class UpsertPlanRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'shop_id' => ['required_without:restaurant_id'],
            'restaurant_id' => ['required_without:shop_id'],
            'collection_id' => ['required', 'integer', new CheckOwnCollection],
            'foods' => ['required', 'array'],
            'plan_date' => ['required', 'string'],
            'plan_time' => ['required', 'string'],
        ];
    }
}
