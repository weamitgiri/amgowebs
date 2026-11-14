<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogFaq extends Model
{
    protected $table = 'blog_faqs';

    protected $fillable = ['post_id', 'question', 'answer', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
