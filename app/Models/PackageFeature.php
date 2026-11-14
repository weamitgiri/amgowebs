<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends Model
{
    protected $fillable = ['package_id', 'label', 'type', 'addon_price', 'sort_order'];

    protected $casts = ['addon_price' => 'decimal:2'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
