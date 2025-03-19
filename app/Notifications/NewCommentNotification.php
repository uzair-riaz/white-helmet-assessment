<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public Comment $comment
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Comment on Task: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new comment has been added to your task: {$this->task->title}")
            ->line("Comment: {$this->comment->content}")
            ->line("Added by: {$this->comment->user->name}")
            ->action('View Task', url("/tasks/{$this->task->id}"))
            ->line('Thank you for using our Task Management System!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'comment_id' => $this->comment->id,
            'comment_content' => $this->comment->content,
            'commenter_id' => $this->comment->user_id,
            'commenter_name' => $this->comment->user->name,
        ];
    }
}
