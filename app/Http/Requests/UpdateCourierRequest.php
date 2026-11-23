<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],

            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('couriers', 'phone')
                    ->ignore($this->route('courier')),
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:150',
                Rule::unique('couriers', 'email')
                    ->ignore($this->route('courier')),
            ],

            'address' => ['sometimes', 'required', 'string'],

            'level' => [
                'sometimes',
                'required',
                'integer',
                'between:1,5',
            ],

            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
