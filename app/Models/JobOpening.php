<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    protected $table = 'job_openings';

    protected $fillable = [
        'title', 'slug', 'department', 'location', 'employment_type', 'experience_level',
        'is_remote', 'salary_range', 'summary', 'description', 'responsibilities', 'requirements',
        'apply_url', 'is_published', 'sort_order', 'posted_at', 'closes_at',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'is_published' => 'boolean',
        'posted_at' => 'date',
        'closes_at' => 'date',
    ];

    /** Human labels for the employment type. */
    public const TYPES = [
        'full-time' => 'Full-time',
        'part-time' => 'Part-time',
        'contract' => 'Contract',
        'internship' => 'Internship',
        'freelance' => 'Freelance',
    ];

    /** schema.org employmentType values. */
    public const SCHEMA_TYPES = [
        'full-time' => 'FULL_TIME',
        'part-time' => 'PART_TIME',
        'contract' => 'CONTRACTOR',
        'internship' => 'INTERN',
        'freelance' => 'OTHER',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('posted_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->employment_type] ?? ucfirst((string) $this->employment_type);
    }

    /** Split a newline-separated text field into clean list items. */
    public function lines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
    }
}
