<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentStoreRequest extends FormRequest
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
            'document_type' => 'required|in:MEMO,EO,SO,LETTER,SP,OTHERS',
            'direction' => 'required|in:INCOMING,OUTGOING',
            'originating_office' => 'required|exists:offices,id',
            'subject' => 'required|string|max:255',
            'picto_number' => 'nullable|string|max:50',
            'doc_number' => 'nullable|string|max:50',
            'to_office' => 'nullable|exists:offices,id',
            'action_required' => 'nullable|string|max:500',
            'endorsed_to' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'shared_drive_link' => 'nullable|url|max:500',
            'received_via_online' => 'boolean',
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
            'document_type.required' => 'Document type is required.',
            'document_type.in' => 'Invalid document type selected.',
            'direction.required' => 'Direction is required.',
            'direction.in' => 'Invalid direction selected.',
            'originating_office.required' => 'Originating office is required.',
            'originating_office.exists' => 'Selected originating office is invalid.',
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject must not exceed 255 characters.',
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'Only PDF, Word, Excel, and image files are allowed.',
        ];
    }
}
