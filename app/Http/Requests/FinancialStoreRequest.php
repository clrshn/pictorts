<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancialStoreRequest extends FormRequest
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
            'type' => 'nullable|string|max:50',
            'description' => 'required|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'pr_number' => 'nullable|string|max:50',
            'pr_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'po_number' => 'nullable|string|max:50',
            'po_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'obr_number' => 'nullable|string|max:50',
            'voucher_number' => 'nullable|string|max:50',
            'office_origin' => 'required|exists:offices,id',
            'remarks' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
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
            'description.required' => 'Description is required.',
            'description.max' => 'Description must not exceed 255 characters.',
            'pr_amount.numeric' => 'PR amount must be a valid number.',
            'pr_amount.min' => 'PR amount cannot be negative.',
            'pr_amount.max' => 'PR amount is too large.',
            'po_amount.numeric' => 'PO amount must be a valid number.',
            'po_amount.min' => 'PO amount cannot be negative.',
            'po_amount.max' => 'PO amount is too large.',
            'office_origin.required' => 'Office origin is required.',
            'office_origin.exists' => 'Selected office origin is invalid.',
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'Only PDF, Word, Excel, and image files are allowed.',
        ];
    }
}
