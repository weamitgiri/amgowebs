<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryNote extends Model
{
    protected $fillable = ['inquiry_id', 'user_id', 'body', 'follow_up_at'];

    protected $casts = ['follow_up_at' => 'datetime'];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(PackageInquiry::class, 'inquiry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
