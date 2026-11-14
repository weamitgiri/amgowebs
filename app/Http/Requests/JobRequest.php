<?php

namespace App\Http\Requests;

use App\Models\JobOpening;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->slug ?: Str::slug((string) $this->title),
            'is_remote' => $this->boolean('is_remote'),
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('job')?->id;

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:190', Rule::unique('job_openings', 'slug')->ignore($id)],
            'department' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['required', Rule::in(array_keys(JobOpening::TYPES))],
            'experience_level' => ['nullable', 'string', 'max:50'],
            'is_remote' => ['boolean'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'responsibilities' => ['nullable', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'apply_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'posted_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date'],
        ];
    }
}
