<?php

namespace App\Http\Requests\Api;

use App\Enums\Language;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
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
            'language' => ['required', 'string', Rule::in(Language::values())],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Validation Failed!',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
