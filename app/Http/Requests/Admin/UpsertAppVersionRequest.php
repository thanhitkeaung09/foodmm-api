<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpsertAppVersionRequest extends FormRequest
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
            'version' => ['required', 'string', 'max:255'],
            'build_no' => ['required', 'string', 'max:255'],
            'is_forced_updated' => ['required', 'boolean'],
            'ios_link' => ['required', 'string', 'max:255'],
            'android_link' => ['required', 'string', 'max:255'],
        ];
    }
}
