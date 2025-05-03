<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('reply-list');
    }

    public function view(User $user, Reply $reply): bool
    {
        return $user->hasPermissionTo($reply, 'reply-list');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('reply-create');
    }

    public function update(User $user, Reply $reply): bool
    {
        return $user->hasPermissionTo($reply, 'reply-update');
    }

    public function delete(User $user, Reply $reply): bool
    {
        return $user->hasPermissionTo($reply, 'reply-destroy');
    }

    public function restore(User $user, Reply $reply): bool
    {
        return $user->hasPermissionTo($reply, 'reply-restore');
    }

    public function forceDelete(User $user, Reply $reply): bool
    {
        return $user->hasPermissionTo($reply, 'reply-force-delete');
    }
}
