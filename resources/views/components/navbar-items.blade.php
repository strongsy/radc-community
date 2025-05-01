<!-- resources/views/components/navbar-items.blade.php -->
<flux:navbar.item href="{{ route('home') }}" icon="home" wire:navigate="{{ route('home') }}">Home</flux:navbar.item>
<flux:navbar.item href="{{ route('contact') }}" icon="pencil-square" wire:navigate="{{ route('contact') }}">Contact Us</flux:navbar.item>

@guest
    <flux:navbar.item href="{{ route('login') }}" icon="lock-open" wire:navigate="{{ route('login') }}">Login</flux:navbar.item>
    <flux:navbar.item href="{{ route('register') }}" icon="user" wire:navigate="{{ route('register') }}">Register</flux:navbar.item>
@endguest

@auth
    <flux:navbar.item href="{{ route('dashboard') }}" icon="cog-6-tooth" wire:navigate="{{ route('dashboard') }}">Dashboard</flux:navbar.item>
@endauth
