<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isTeacher() || $this->user()->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string|max:500',
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date',
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
            'attendance.required' => 'Attendance data is required.',
            'attendance.*.student_id.required' => 'Student ID is required for each attendance record.',
            'attendance.*.student_id.exists' => 'Selected student does not exist.',
            'attendance.*.status.required' => 'Attendance status is required.',
            'attendance.*.status.in' => 'Invalid attendance status.',
            'schedule_id.required' => 'Schedule ID is required.',
            'schedule_id.exists' => 'Selected schedule does not exist.',
            'date.required' => 'Date is required.',
            'date.date' => 'Please provide a valid date.',
        ];
    }
}