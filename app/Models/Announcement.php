<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'message',
        'badge_type',
        'is_active',
        'created_by',
        'admin_id',
        'target_role',
        'target_audience',
        'expires_at',
    ];

    protected $appends = ['admin_id', 'target_audience', 'message'];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($announcement) {
            if (empty($announcement->created_by)) {
                $announcement->created_by = $announcement->admin_id ?: (\App\Models\User::where('role', 'admin')->value('id') ?: 1);
            }
            if (empty($announcement->admin_id)) {
                $announcement->admin_id = $announcement->created_by;
            }
            if (empty($announcement->content) && !empty($announcement->message)) {
                $announcement->content = $announcement->message;
            }
            if (empty($announcement->message) && !empty($announcement->content)) {
                $announcement->message = $announcement->content;
            }
            if (empty($announcement->target_role) && !empty($announcement->target_audience)) {
                $announcement->target_role = $announcement->target_audience;
            }
            if (empty($announcement->target_audience) && !empty($announcement->target_role)) {
                $announcement->target_audience = $announcement->target_role;
            }
        });
    }

    public function getMessageAttribute(): ?string
    {
        return $this->attributes['content'] ?? ($this->attributes['message'] ?? null);
    }

    public function setMessageAttribute($value): void
    {
        $this->attributes['message'] = $value;
        $this->attributes['content'] = $value;
    }

    public function getTargetAudienceAttribute(): ?string
    {
        return $this->attributes['target_role'] ?? ($this->attributes['target_audience'] ?? 'all');
    }

    public function setTargetAudienceAttribute($value): void
    {
        $this->attributes['target_audience'] = $value;
        $this->attributes['target_role'] = $value;
    }

    public function getAdminIdAttribute(): ?int
    {
        return $this->attributes['created_by'] ?? ($this->attributes['admin_id'] ?? null);
    }

    public function setAdminIdAttribute($value): void
    {
        $this->attributes['admin_id'] = $value;
        $this->attributes['created_by'] = $value;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
