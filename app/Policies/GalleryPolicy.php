<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GalleryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Gallery $gallery): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Gallery $gallery): bool
    {
    }

    public function delete(User $user, Gallery $gallery): bool
    {
    }

    public function restore(User $user, Gallery $gallery): bool
    {
    }

    public function forceDelete(User $user, Gallery $gallery): bool
    {
    }
}
