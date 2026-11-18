<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Users may edit only their own contact details.
 * Email, school ID and role are managed by administrators and are NOT accepted here.
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]{5,30}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone may only contain digits, spaces, + ( ) and -.',
        ];
    }
}
