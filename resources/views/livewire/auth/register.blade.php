<?php

use App\Models\Community;
use App\Models\Entitlement;
use App\Models\Membership;
use App\Models\User;
use App\Notifications\NewUserRegistrationForAdminNotification;
use App\Notifications\UserRegistrationReceivedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int|null $community_id = null;
    public int|null $membership_id = null;
    public string $affiliation = '';
    public bool $is_subscribed = false;
    public array $communities = [];
    public array $memberships = [];
    public array $entitlements = [];
    // Array to store selected entitlements
    public array $selectedEntitlements = [];


    public function mount(): void
    {
        $this->communities = Community::pluck('name', 'id')->toArray();
        $this->memberships = Membership::pluck('name', 'id')->toArray();
        $this->entitlements = Entitlement::pluck('name', 'id')->toArray();
    }


    /**
     * Handles user registration by validating input data, creating a new user, dispatching a job for notification,
     * and redirecting to the welcome page with a status message.
     *
     * @return Redirector Redirects to the 'home' route with a success status message.
     */
    public function register(): Redirector
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'community_id' => ['required'],
            'membership_id' => ['required'],
            'affiliation' => ['required', 'string', 'min:10', 'max:500'],
            'is_subscribed' => ['boolean'],
        ]);

        //set is_active to false
        $validated['is_active'] = false;

        //hash the user password
        $validated['password'] = Hash::make($validated['password']);

        //add the user to the user table (is_active will be false until the user is approved for access)
        $user = User::create($validated);

        // Assign the 'user' role to the newly registered user
        $user->assignRole('user');

        if (!empty($this->selectedEntitlements)) {
            $user->entitlements()->attach($this->selectedEntitlements);
        }


        activity()->log($user->name . ' REGISTERED');

        //send the emails
        $user->notify(new UserRegistrationReceivedNotification($user));

        Notification::route('mail', config('mail.sec_email'))
            ->notify(new NewUserRegistrationForAdminNotification($user));


        //reset the form
        $this->reset();

        //redirect to the welcome page with a toast notification
        return redirect(route('home'))->with('status', 'Thank you for your registration . You will receive an email shortly if your registration is approved . ');
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Register for an account')"
                   :description="__('Enter your details and we will get back to you as soon as possible . ')"/>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success ">
            <flux:heading size="sm" level="3" class="text-teal-600 dark:text-teal-400">
                {{ session('status') }}
            </flux:heading>

        </div>
    @endif

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required=""
            autofocus
            autocomplete="name"
            placeholder="Full name"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required=""
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            autocomplete="new-password"
            :placeholder="__('Password')"
            required=""/>

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required=""
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <!-- Community -->
        <flux:select
            wire:model="community_id"
            label="Community"
            variant="listbox"
            size="sm"
            placeholder="Choose community..."
            required="">
            @foreach($communities as $id => $name)
                <flux:select.option value="{{ $id }}" wire:key="{{ $id }}">
                    {{ $name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <!-- Membership -->
        <flux:select
            wire:model="membership_id"
            label="Membership" size="sm"
            variant="listbox"
            placeholder="Choose membership..."
            required="">
            @foreach($memberships as $id => $name)
                <flux:select.option value="{{ $id }}" wire:key="{{ $id }}">
                    {{ $name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select
            wire:model="selectedEntitlements"
            variant="listbox"
            multiple size="sm"
            label="Entitlements"
            placeholder="Choose entitlements..."
            required="">
            @foreach($entitlements as $id => $name)
                <flux:select.option value="{{ $id }}" wire:key="{{ $id }}">
                    {{ $name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <!-- Affiliation -->
        <flux:textarea
            wire:model="affiliation"
            :label="__('Affiliation')"
            type="text"
            required=""
            placeholder="Your affiliation..."
        />

        <!-- subscribe for notifications -->
        <flux:checkbox
            wire:model="is_subscribed"
            value="subscribe"
            label="Subscribe"
            description="Receive notifications straight to your inbox."
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Submit') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account ? ') }}
        <flux:link :href="route('login')" wire:navigate="route('login')">{{ __('Log in') }}</flux:link>
    </div>

    @persist('toast')
    <flux:toast/>
    @endpersist
</div>
