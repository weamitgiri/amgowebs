<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageInquiry extends Model
{
    protected $fillable = [
        'reference', 'package_id', 'name', 'business_name', 'email', 'country', 'phone',
        'website_type', 'pages_required', 'existing_url', 'description', 'required_features',
        'preferred_meeting_at', 'budget', 'additional_requirements', 'attachment_path', 'attachment_name',
        'status', 'source_page', 'assigned_to', 'follow_up_at', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'preferred_meeting_at' => 'datetime',
        'follow_up_at' => 'datetime',
    ];

    public const STATUSES = [
        'new' => 'New',
        'under_review' => 'Under Review',
        'meeting_scheduled' => 'Meeting Scheduled',
        'proposal_sent' => 'Proposal Sent',
        'converted' => 'Converted',
        'rejected' => 'Rejected',
        'closed' => 'Closed',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(InquiryNote::class, 'inquiry_id')->latest();
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(InquiryStatusHistory::class, 'inquiry_id')->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
