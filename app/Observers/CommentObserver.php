<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\BadgeService;

class CommentObserver
{
    public function __construct(private readonly BadgeService $badgeService) {}

    public function created(Comment $comment): void
    {
        if ($comment->user) {
            $this->badgeService->checkCommentBadges($comment->user);
        }
    }
}
