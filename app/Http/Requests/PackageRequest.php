<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->slug ?: Str::slug($this->name),
            'is_custom_pricing' => $this->boolean('is_custom_pricing'),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('package')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:180', Rule::unique('packages', 'slug')->ignore($id)],
            'is_custom_pricing' => ['boolean'],
            'price_usd' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'original_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:99'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'pages_included' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'delivery_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'support_months' => ['nullable', 'integer', 'min:0', 'max:120'],
            'revisions' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'cta_label' => ['nullable', 'string', 'max:50'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'included' => ['array'],
            'included.*' => ['nullable', 'string', 'max:200'],
            'excluded' => ['array'],
            'excluded.*' => ['nullable', 'string', 'max:200'],
            'addon_label' => ['array'],
            'addon_label.*' => ['nullable', 'string', 'max:200'],
            'addon_price' => ['array'],
            'addon_price.*' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
