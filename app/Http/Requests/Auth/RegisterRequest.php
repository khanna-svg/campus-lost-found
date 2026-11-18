<?php

namespace App\Http\Requests\Auth;

use App\Rules\SchoolEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => Str::lower(trim((string) $this->input('email')))]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'school_id' => ['nullable', 'string', 'max:50', 'unique:users,school_id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new SchoolEmail],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
