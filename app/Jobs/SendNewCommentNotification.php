<?php

namespace App\Jobs;

use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewCommentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Comment $comment
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the task
        $task = $this->comment->task;
        
        // Get the task owner
        $taskOwner = $task->user;
        
        // Only notify if the commenter is not the task owner
        if ($this->comment->user_id !== $taskOwner->id) {
            $taskOwner->notify(new NewCommentNotification($task, $this->comment));
        }
    }
}
