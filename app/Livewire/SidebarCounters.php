<?php

namespace App\Livewire;

use App\Models\Email;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

class SidebarCounters extends Component
{
    // Define properties for the counters
    public $activeUsers = 0;

    public $blockedUsers = 0;

    public $registeredUsers = 0;

    public $receivedMail = 0;

    public $archivedMail = 0;

    // Set the polling interval (in milliseconds)
    public function render(): Factory|View
    {
        $this->updateCounters();

        return view('livewire.sidebar-counters');
    }

    private function updateCounters(): void
    {
        // Use cache with a short TTL to avoid hitting the database on every poll
        $this->activeUsers = Cache::remember('counter.users.active', 10, static function () {
            return User::where('is_active', true)->where('is_blocked', false)->count();
        });

        $this->blockedUsers = Cache::remember('counter.users.blocked', 10, static function () {
            return User::where('is_blocked', true)->count();
        });

        $this->registeredUsers = Cache::remember('counter.users.registered', 10, static function () {
            return User::where('is_active', false)->where('is_blocked', false)->count();
        });

        $this->receivedMail = Cache::remember('counter.mail.received', 10, static function () {
            return Email::count();
        });

        $this->archivedMail = Cache::remember('counter.mail.archived', 10, static function () {
            return Email::onlyTrashed()->count();
        });
    }

    // Listen for any database changes to invalidate the cache
    protected function getListeners(): array
    {
        return [
            'userStatusChanged' => 'invalidateUserCache',
            'emailStatusChanged' => 'invalidateEmailCache',
        ];
    }

    public function invalidateUserCache(): void
    {
        Cache::forget('counter.users.active');
        Cache::forget('counter.users.blocked');
        Cache::forget('counter.users.registered');
    }

    public function invalidateEmailCache(): void
    {
        Cache::forget('counter.mail.received');
        Cache::forget('counter.mail.archived');
    }
}
