<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'business_name' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190'],
            'country' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:40'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'website_type' => ['nullable', 'string', 'max:100'],
            'pages_required' => ['nullable', 'string', 'max:50'],
            'existing_url' => ['nullable', 'url', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'required_features' => ['nullable', 'string', 'max:2000'],
            'preferred_meeting_at' => ['nullable', 'date', 'after_or_equal:today'],
            'budget' => ['nullable', 'string', 'max:50'],
            'additional_requirements' => ['nullable', 'string', 'max:2000'],
            // Optional requirements file — restricted to safe document/image types, max 10 MB.
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,odt,jpg,jpeg,png,webp', 'max:10240'],
            'consent' => ['accepted'],
            // Honeypot: bots fill this hidden field; humans never see it.
            'nickname' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'consent.accepted' => 'Please agree to be contacted about your inquiry.',
            'nickname.prohibited' => 'Your submission could not be processed.',
            'description.min' => 'Please tell us a little more about your project (at least 10 characters).',
        ];
    }
}
