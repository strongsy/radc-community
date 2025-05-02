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
        switch ($this->type) {
            case 'activeUsers':
                $this->count = User::where('is_active', true)->where('is_blocked', false)->count();
                break;
            case 'blockedUsers':
                $this->count = User::where('is_blocked', true)->count();
                break;
            case 'registeredUsers':
                $this->count = User::where('is_active', false)->where('is_blocked', false)->count();
                break;
            case 'receivedMail':
                $this->count = Email::count();
                break;
            case 'archivedMail':
                $this->count = Email::onlyTrashed()->count();
                break;
            default:
                $this->count = 0;
                break;
        }
    }
}
