<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Mockery\Matcher\HasKey;

class Announcement extends Model
{
    use HasFactory;

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
    public function enroll(): HasMany
    {
        return $this->hasMany(Enroll::class);
    }

    public function like(): HasMany
    {
        return $this->hasMany(Like::class);        
    }
    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);        
    }

    public function reply(): HasMany
    {
        return $this->hasMany(reply::class);        
    }
    public function scopeFilter($query, array $filters) {
        $query->when(
            $filters['search'] ?? false,
            fn ($query, $search) => $query->where(fn ($query) => $query->where('title', 'like', '%' . $search . '%')
                ->OrWhere('description', 'like', '%' . $search . '%'))
        );

        $query->when(
            isset($filters['type']), // Check if 'type' filter is present
            function ($query) use ($filters) {
                $type = $filters['type']; // Get the value of 'type'
                $query->where('type', $type);
            }
        );
    
    }

}
