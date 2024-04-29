<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;

    public const TYPE_OFFLINE = 0;

    public const TYPE_ONLINE = 1;

    public const STATUS_CLOSED = 0;

    public const STATUS_OPENED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'location',
        'price',
        'status',
        'student_count',
    ];

    /** @return Attribute<string, int> */
    protected function type(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === self::TYPE_ONLINE) {
                    return 'online';
                }
                if ($value === self::TYPE_OFFLINE) {
                    return 'offline';
                }
            },
            set: function ($value) {
                if ($value === 'online') {
                    return self::TYPE_ONLINE;
                }
                if ($value === 'offline') {
                    return self::TYPE_OFFLINE;
                }
            }
        );
    }

    /** @return Attribute<string, int> */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === self::STATUS_CLOSED) {
                    return 'closed';
                }
                if ($value === self::STATUS_OPENED) {
                    return 'opened';
                }
            },
            set: function ($value) {
                if ($value === 'closed') {
                    return self::STATUS_CLOSED;
                }
                if ($value === 'opened') {
                    return self::STATUS_OPENED;
                }
            }
        );
    }

    public function isClosed(): bool
    {
        return $this->status == 'closed';
    }

    public function hasAvailableEnrollment(): bool
    {
        return $this->enrolls()->count() < $this->student_count;
    }

    /** @return BelongsTo<User, Announcement> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Enroll> */
    public function enrolls(): HasMany
    {
        return $this->hasMany(Enroll::class);
    }

    /** @return HasMany<Like> */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /** @return HasMany<Comment> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
