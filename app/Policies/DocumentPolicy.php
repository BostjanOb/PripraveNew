<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
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

    public function view(User $user, Document $document): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Document $document): bool
    {
        return $document->user_id === $user->id;
    }

    public function delete(User $user, Document $document): bool
    {
        return $document->user_id === $user->id;
    }

    public function restore(User $user, Document $document): bool
    {
        return $document->user_id === $user->id;
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return $document->user_id === $user->id;
    }
}
