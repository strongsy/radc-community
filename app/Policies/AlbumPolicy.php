<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Album $album): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Album $album): bool
    {
    }

    public function delete(User $user, Album $album): bool
    {
    }

    public function restore(User $user, Album $album): bool
    {
    }

    public function forceDelete(User $user, Album $album): bool
    {
    }
}
