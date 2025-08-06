<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStudent();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'type' => 'required|in:sick,family,personal,emergency',
            'reason' => 'required|string|min:10|max:1000',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from_date.required' => 'Start date is required.',
            'from_date.date' => 'Please provide a valid start date.',
            'from_date.after_or_equal' => 'Start date must be today or later.',
            'to_date.required' => 'End date is required.',
            'to_date.date' => 'Please provide a valid end date.',
            'to_date.after_or_equal' => 'End date must be same or after start date.',
            'type.required' => 'Leave type is required.',
            'type.in' => 'Please select a valid leave type.',
            'reason.required' => 'Reason is required.',
            'reason.min' => 'Reason must be at least 10 characters.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',
            'evidence_file.file' => 'Evidence must be a valid file.',
            'evidence_file.mimes' => 'Evidence must be a PDF or image file (jpg, jpeg, png).',
            'evidence_file.max' => 'Evidence file cannot be larger than 5MB.',
        ];
    }
}