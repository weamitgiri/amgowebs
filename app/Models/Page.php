<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasSeo;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'template', 'featured_image',
        'status', 'is_in_sitemap', 'is_system', 'published_at', 'author_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_in_sitemap' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    /**
     * Return the data array of the first visible section of a given type,
     * so bespoke templates (e.g. the homepage) can render admin-editable
     * content without being locked to the generic section partials.
     *
     * @return array<string, mixed>
     */
    public function sectionData(string $type): array
    {
        $section = $this->sections
            ->where('type', $type)
            ->where('is_visible', true)
            ->first();

        return $section?->data ?? [];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (is_null($this->published_at) || $this->published_at->isPast());
    }
}
