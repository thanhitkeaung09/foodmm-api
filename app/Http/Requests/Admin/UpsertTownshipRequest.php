<?php

namespace App\Http\Requests\Admin;

use App\Models\City;
use App\Models\Township;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertTownshipRequest extends FormRequest
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
                'max:50',
                Rule::unique(Township::class)->ignore($this->township),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists(City::class, 'id'),
            ]
        ];
    }
}
