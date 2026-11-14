<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Resolve a SEO value, falling back to a model attribute, then a default.
     */
    public function seoValue(string $field, ?string $fallbackAttribute = null, ?string $default = null): ?string
    {
        $value = $this->seo?->{$field};

        if (filled($value)) {
            return $value;
        }

        if ($fallbackAttribute && filled($this->{$fallbackAttribute})) {
            return $this->{$fallbackAttribute};
        }

        return $default;
    }
}
