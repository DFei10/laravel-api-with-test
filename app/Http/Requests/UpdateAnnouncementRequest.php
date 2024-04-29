<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->announcement->user_id == auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'student_count' => 'sometimes|required|numeric',
            'type' => 'sometimes|required|in:online,offline',
            'status' => 'sometimes|required|in:closed,opened',
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'the :attribute field must be either online or offline.',
            'status.in' => 'the :attribute field must be either closed or opened.',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('location', 'required_if:type,offline|exclude_if:type,online', function () {
            return ! $this->announcement->location;
        });
    }
}
