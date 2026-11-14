<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasSeo;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'featured_image', 'thumbnail', 'social_image', 'image_alt',
        'author_id', 'category_id', 'status', 'published_at', 'views',
    ];

    protected $casts = ['published_at' => 'datetime'];

    /** Image shown on listing cards (thumbnail, falling back to the main image). */
    public function getCardImageAttribute(): ?string
    {
        return $this->thumbnail ?: $this->featured_image;
    }

    /** Large image shown on the post detail / used for Open Graph. */
    public function getBannerImageAttribute(): ?string
    {
        return $this->featured_image ?: $this->thumbnail;
    }

    /** ALT text, falling back to the post title so images are never unlabelled. */
    public function getImageAltTextAttribute(): string
    {
        return $this->image_alt ?: $this->title;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(BlogFaq::class)->orderBy('sort_order');
    }

    /** Only the FAQs that are visible (used for the public accordion + schema). */
    public function visibleFaqs(): HasMany
    {
        return $this->faqs()->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200));
    }

    /** Real, checkable on-page SEO score 0–100 (see App\Support\SeoScorer). */
    public function getSeoScoreAttribute(): int
    {
        return \App\Support\SeoScorer::score($this);
    }

    /** draft | scheduled | published (scheduled = published status with a future date). */
    public function getStateAttribute(): string
    {
        if ($this->status !== 'published') {
            return 'draft';
        }

        return ($this->published_at && $this->published_at->isFuture()) ? 'scheduled' : 'published';
    }
}
