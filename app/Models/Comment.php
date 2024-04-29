<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'announcement_id',
        'comment',
    ];

    /** @return BelongsTo<User, Comment> */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Announcement, Comment> */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }
}
