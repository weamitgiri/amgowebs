<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasSeo;

    protected $fillable = [
        'name', 'slug', 'price_usd', 'original_price', 'discount_percent', 'is_custom_pricing',
        'short_description', 'description', 'pages_included', 'delivery_days', 'support_months',
        'revisions', 'icon', 'image', 'cta_label', 'is_featured', 'is_active', 'status', 'sort_order',
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_custom_pricing' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class)->orderBy('sort_order');
    }

    public function includedFeatures(): HasMany
    {
        return $this->features()->where('type', 'included');
    }

    public function excludedFeatures(): HasMany
    {
        return $this->features()->where('type', 'excluded');
    }

    public function addonFeatures(): HasMany
    {
        return $this->features()->where('type', 'addon');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PackageInquiry::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('price_usd');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPriceLabelAttribute(): string
    {
        if ($this->is_custom_pricing || is_null($this->price_usd)) {
            return 'Custom';
        }

        return '$' . number_format((float) $this->price_usd, 0);
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->original_price && $this->price_usd && $this->original_price > $this->price_usd;
    }
}
