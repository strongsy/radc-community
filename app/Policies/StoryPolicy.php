<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Story $story): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Story $story): bool
    {
    }

    public function delete(User $user, Story $story): bool
    {
    }

    public function restore(User $user, Story $story): bool
    {
    }

    public function forceDelete(User $user, Story $story): bool
    {
    }
}
