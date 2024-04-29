<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'location' => ['required_if:type,offline', 'exclude_unless:type,offline'],
            'price' => ['required', 'numeric'],
            'student_count' => ['required', 'numeric'],
            'type' => ['required', 'in:online,offline'],
            'status' => ['required', 'in:closed,opened'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'the :attribute field must be either online or offline.',
            'status.in' => 'the :attribute field must be either closed or opened.',
        ];
    }
}
