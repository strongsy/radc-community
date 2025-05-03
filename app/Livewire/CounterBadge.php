<?php

namespace App\Livewire;

use App\Models\Email;
use App\Models\User;
use Livewire\Component;

class CounterBadge extends Component
{
    public $type;

    public $count = 0;

    public function mount($type)
    {
        $this->type = $type;
        $this->updateCount();
    }

    public function render()
    {
        return view('livewire.counter-badge');
    }

    public function updateCount()
    {
        $this->count = match ($this->type) {
            'activeUsers' => User::where('is_active', true)->where('is_blocked', false)->count(),
            'blockedUsers' => User::where('is_blocked', true)->count(),
            'registeredUsers' => User::where('is_active', false)->where('is_blocked', false)->count(),
            'receivedMail' => Email::count(),
            'archivedMail' => Email::onlyTrashed()->count(),
            default => 0,
        };
    }
}
