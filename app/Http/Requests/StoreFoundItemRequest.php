<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoundItemRequest extends FormRequest
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
            'public_description' => ['required', 'string', 'max:2000'],
            'date_found' => ['required', 'date', 'before_or_equal:today'],
            'location_found' => ['required', 'string', 'max:200'],
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'private_details' => ['required', 'string', 'max:2000'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'is_surrendered' => ['nullable', 'in:0,1,true,false'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Please select a valid item category.',
            'date_found.before_or_equal' => 'The date found cannot be in the future.',
            'photo.required' => 'A photograph of the found item is required.',
            'photo.max' => 'The photograph may not be greater than 5 megabytes.',
            'photo.mimes' => 'The photograph must be a JPG, JPEG, PNG, or WebP image.',
            'private_details.required' => 'Please provide private identifying characteristics so administrators can verify the true owner.',
        ];
    }
}
