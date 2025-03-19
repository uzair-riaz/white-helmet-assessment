<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'user_id',
        'assigned_to',
    ];
    
    protected $casts = [
        'due_date' => 'date',
    ];
    
    /**
     * Get the user that created the task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the user assigned to the task
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the comments for the task
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
