<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, News $news): bool {}

    public function create(User $user): bool {}

    public function update(User $user, News $news): bool {}

    public function delete(User $user, News $news): bool {}

    public function restore(User $user, News $news): bool {}

    public function forceDelete(User $user, News $news): bool {}
}
