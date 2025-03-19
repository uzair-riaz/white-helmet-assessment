<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'content',
        'task_id',
        'user_id',
    ];
    
    /**
     * Get the task that owns the comment
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
    
    /**
     * Get the user that created the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
