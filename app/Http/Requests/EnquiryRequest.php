<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['name', 'company_name', 'mobile', 'email', 'subject', 'message']))
            ->map(fn ($v) => is_string($v) ? trim($v) : $v)
            ->all());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'mobile' => ['required', 'string', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'email' => ['nullable', 'email:rfc', 'max:160'],
            'subject' => ['required', 'string', 'min:3', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            // Honeypot: real visitors never see or fill this field.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Please enter a valid mobile number (digits, spaces, + and - only).',
            'website.prohibited' => 'Your enquiry could not be submitted.',
        ];
    }

    public function attributes(): array
    {
        return ['company_name' => 'company name', 'mobile' => 'mobile number'];
    }
}
