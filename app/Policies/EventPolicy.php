<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('event-list');
    }

    public function view(User $user, Event $event): bool
    {
        return $user->hasPermissionTo($event, 'event-list');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('event-create');
    }

    public function update(User $user, Event $event): bool
    {
        return $user->hasPermissionTo($event, 'event-update');
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->hasPermissionTo($event, 'event-destroy');
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->hasPermissionTo($event, 'event-restore');
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->hasPermissionTo($event, 'event-force-delete');
    }
}
