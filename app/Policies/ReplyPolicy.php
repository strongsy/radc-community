<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, Reply $reply): bool {}

    public function create(User $user): bool {}

    public function update(User $user, Reply $reply): bool {}

    public function delete(User $user, Reply $reply): bool {}

    public function restore(User $user, Reply $reply): bool {}

    public function forceDelete(User $user, Reply $reply): bool {}
}
