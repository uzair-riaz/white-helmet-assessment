<?php

namespace App\Services;

use App\Jobs\SendNewCommentNotification;
use App\Notifications\CommentPosted;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\CommentServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CommentService implements CommentServiceInterface
{
    protected $commentRepository;
    protected $taskRepository;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        TaskRepositoryInterface $taskRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->taskRepository = $taskRepository;
    }

    public function getTaskComments(int $taskId)
    {
        return $this->commentRepository->getAllForTask($taskId);
    }

    public function createComment(array $data, int $taskId)
    {
        return DB::transaction(function () use ($data, $taskId) {
            $commentData = [
                'content' => $data['content'],
                'task_id' => $taskId,
                'user_id' => Auth::id()
            ];

            $comment = $this->commentRepository->create($commentData);

            Notification::send($comment->task->user, new CommentPosted($comment->task, $comment));

            return $comment;
        });
    }

    public function updateComment(array $data, int $taskId, int $id)
    {
        return DB::transaction(function () use ($data, $taskId, $id) {
            $comment = $this->commentRepository->getById($id);

            // Check if the user is the author of the comment
            if ($comment->user_id !== Auth::id()) {
                throw new AuthorizationException('Unauthorized to update this comment');
            }

            return $this->commentRepository->update($id, [
                'content' => $data['content']
            ]);
        });
    }

    public function deleteComment(int $taskId, int $id): bool
    {
        return DB::transaction(function () use ($taskId, $id) {
            $comment = $this->commentRepository->getById($id);
            $task = $this->taskRepository->getById($taskId);

            // Check if the user is the author of the comment or the task owner
            if ($comment->user_id !== Auth::id()) {
                throw new AuthorizationException('Unauthorized to delete this comment');
            }

            return $this->commentRepository->delete($id);
        });
    }
}
