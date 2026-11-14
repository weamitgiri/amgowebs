<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryStatusHistory extends Model
{
    protected $table = 'inquiry_status_history';

    protected $fillable = ['inquiry_id', 'user_id', 'from_status', 'to_status', 'note'];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(PackageInquiry::class, 'inquiry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
