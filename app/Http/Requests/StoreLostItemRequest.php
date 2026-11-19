<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLostItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'uuid', 'exists:item_categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'date_lost' => ['required', 'date', 'before_or_equal:today'],
            'time_lost' => ['nullable', 'date_format:H:i'],
            'location_last_seen' => ['required', 'string', 'max:200'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'identifying_details' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Please select a valid item category.',
            'date_lost.before_or_equal' => 'The date lost cannot be in the future.',
            'photo.max' => 'The photograph may not be greater than 5 megabytes.',
            'photo.mimes' => 'The photograph must be a JPG, JPEG, PNG, or WebP image.',
        ];
    }
}
