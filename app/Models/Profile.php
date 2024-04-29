<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'birth_date',
        'avatar_path',
        'state',
        'bio',
    ];

    /** @return BelongsTo<User, Profile> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
