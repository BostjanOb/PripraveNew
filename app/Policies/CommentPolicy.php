<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }
}
