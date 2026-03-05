<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackSearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tracking_code' => 'required|string|max:50|regex:/^PICTO-[A-Z]+-\d{4}-\d{4}$/',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tracking_code.required' => 'Tracking code is required.',
            'tracking_code.max' => 'Tracking code must not exceed 50 characters.',
            'tracking_code.regex' => 'Tracking code must be in format: PICTO-{TYPE}-{YEAR}-{SEQUENCE} (e.g., PICTO-LETTER-2026-0001)',
        ];
    }
}
