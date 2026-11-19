<?php

namespace App\Http\Requests;

use App\Enums\FoundItemStatus;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'found_item_id' => [
                'required',
                'uuid',
                Rule::exists('found_items', 'id'),
            ],
            'proof_description' => [
                'required',
                'string',
                'min:20',
                'max:2000',
            ],
            'lost_item_id' => [
                'nullable',
                'uuid',
                Rule::exists('lost_items', 'id')->where('user_id', $this->user()?->id),
            ],
            'evidence' => [
                'nullable',
                'array',
                'max:3',
            ],
            'evidence.*' => [
                'file',
                'mimes:jpeg,png,webp,pdf',
                'max:5120', // 5MB
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $foundItem = FoundItem::find($this->input('found_item_id'));

            if (! $foundItem) {
                return;
            }

            // Cannot claim own found report
            if ($foundItem->user_id === $this->user()?->id) {
                $validator->errors()->add('found_item_id', 'You cannot claim an item that you reported finding.');
                return;
            }

            // Only approved items can be claimed
            if ($foundItem->status !== FoundItemStatus::Approved) {
                $validator->errors()->add('found_item_id', 'This item is not currently available for claim.');
                return;
            }

            // Cannot have more than 1 active open claim on the same item
            $hasOpenClaim = ClaimRequest::where('found_item_id', $foundItem->id)
                ->where('claimant_id', $this->user()?->id)
                ->whereIn('status', ['pending', 'under_review', 'additional_information_required'])
                ->exists();

            if ($hasOpenClaim) {
                $validator->errors()->add('found_item_id', 'You already have an open claim submitted for this item.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'proof_description.required' => 'Please describe proof of ownership so administrators can verify you are the rightful owner.',
            'proof_description.min' => 'Proof description must be at least 20 characters (include distinguishing marks, serial numbers, wallpapers, contents, etc.).',
            'evidence.max' => 'You may upload a maximum of 3 supporting proof files.',
            'evidence.*.mimes' => 'Supporting proof files must be an image (JPEG, PNG, WEBP) or PDF document.',
            'evidence.*.max' => 'Each evidence file must not exceed 5 MB in size.',
        ];
    }
}
