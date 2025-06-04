<?php
/*//user badges
use App\Models\Article;
use App\Models\Email;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use App\Models\Event;

//user badges
$activeUsers = User::where('is_active', true)->where('is_blocked', false)->count();
$blockedUsers = User::where('is_blocked', true)->count();
$registeredUsers = User::where('is_active', false)->where('is_blocked', false)->count();

//mail  badges
$receivedMail = Email::count();
$archivedMail = Email::onlyTrashed()->count();

//event badges
$activeEvents = Event::count();

//article badges
$articles = Article::count();

//posts badges
$posts = Post::where('user_id', 'auth()->id()')->where('is_approved', true)->count();

//stories badges
$stories = Story::count();*/
?>

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ config('app.name') }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
        <x-app-logo/>
    </a>

    <!-- routes -->
    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Platform')" class="grid">
            <!-- dashboard -->
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                               wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>

            <!-- activity log -->
            @can('activity-log-read')
                <flux:navlist.item icon="briefcase" :href="route('activity-log')"
                                   :current="request()->routeIs('activity-log')"
                                   wire:navigate>{{ __('Activity Log') }}</flux:navlist.item>
            @endcan

            <!-- articles -->
            @can('article-read')
                <flux:navlist.item icon="information-circle" :href="route('article-list')"
                                   :current="request()->routeIs('article-list')"
                                   wire:navigate>{{ __('Articles') }}</flux:navlist.item>
            @endcan

            <!-- email -->
            @can('mail-read')
                <flux:navlist.group heading="Email" expandable>
                    <flux:navlist.item icon="envelope-open" href="{{route('mail-list')}}"
                                       :current="request()->routeIs('mail-list')" wire:navigate>
                        {{ __('Received') }} </flux:navlist.item>
                    <flux:navlist.item icon="archive-box" href="{{ route('mail-archived') }}"
                                       :current="request()->routeIs('mail-archived')"
                                       wire:navigate>{{ __('Archived') }}</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            <!-- events -->
            @can('event-read')
                <flux:navlist.group heading="Events" expandable>
                    <flux:navlist.item icon="calendar" href="{{ route('event-list') }}"
                                       :current="request()->routeIs('event-list')"
                                       wire:navigate>{{ __('List Events') }}</flux:navlist.item>
                    <flux:navlist.item icon="plus-circle" href="{{ route('event-create') }}"
                                       :current="request()->routeIs('event-create')"
                                       wire:navigate>{{ __('Create Event') }}</flux:navlist.item>
                    <flux:navlist.item icon="map-pin" href="{{ route('venue-list') }}"
                                       :current="request()->routeIs('venue-list')"
                                       wire:navigate>{{ __('Venues') }}</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            <!-- galleries -->
            @can('gallery-read')
                <flux:navlist.item icon="photo" href="{{ route('gallery-list') }}"
                                   :current="request()->routeIs('gallery-list')"
                                   wire:navigate>{{ __('Galleries') }}</flux:navlist.item>
            @endcan

            <!-- news -->
            @can('news-read')
                <flux:navlist.item icon="newspaper" href="{{ route('news-list') }}"
                                   :current="request()->routeIs('news-list')"
                                   wire:navigate>{{ __('News') }}</flux:navlist.item>
            @endcan

            <!-- posts -->
            @can('post-read')
                <flux:navlist.item icon="speaker-wave" :href="route('post-list')"
                                   :current="request()->routeIs('post-list')"
                                   wire:navigate>{{ __('Posts') }}</flux:navlist.item>
            @endcan

            <!-- stories -->
            @can('story-read')
                <flux:navlist.item icon="book-open" :href="route('story-list')"
                                   :current="request()->routeIs('story-list')"
                                   wire:navigate>{{ __('Stories') }}</flux:navlist.item>
            @endcan

            <!-- users -->
            @can('user-approve')
                <flux:navlist.group heading="Users" expandable>
                    <flux:navlist.item icon="users" href="{{route('users.active')}}"
                                       :current="request()->routeIs('users.active')" wire:navigate>
                        {{ __('Active') }} </flux:navlist.item>
                    <flux:navlist.item icon="user-plus" href="{{ route('users.registrations') }}"
                                       :current="request()->routeIs('users.registrations')"
                                       wire:navigate>{{ __('Registrations') }}</flux:navlist.item>
                    <flux:navlist.item icon="no-symbol" href="{{ route('users.blocked') }}"
                                       :current="request()->routeIs('users.blocked')"
                                       wire:navigate>{{ __('Blocked') }}</flux:navlist.item>
                </flux:navlist.group>
            @endcan
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer/>

    <!-- notifications dropdown -->
    <flux:dropdown position="bottom" align="start">
        <flux:navlist variant="outline" icon="chevrons-up-down">
            <flux:navlist.item icon="bell">Notifications</flux:navlist.item>
        </flux:navlist>

        <flux:menu>
            <flux:menu.item icon="plus">New post</flux:menu.item>

            <flux:menu.separator/>

            <flux:menu.submenu heading="Sort by">
                <flux:menu.radio.group>
                    <flux:menu.radio checked>Name</flux:menu.radio>
                    <flux:menu.radio>Date</flux:menu.radio>
                    <flux:menu.radio>Popularity</flux:menu.radio>
                </flux:menu.radio.group>
            </flux:menu.submenu>

            <flux:menu.submenu heading="Filter">
                <flux:menu.checkbox checked>Draft</flux:menu.checkbox>
                <flux:menu.checkbox checked>Published</flux:menu.checkbox>
                <flux:menu.checkbox>Archived</flux:menu.checkbox>
            </flux:menu.submenu>

            <flux:menu.separator/>

            <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
        </flux:menu>
    </flux:navlist>

    <!-- help link -->
    <flux:navlist variant="outline">
        <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
            {{ __('Help') }}
        </flux:navlist.item>
    </flux:navlist>

    <!-- Desktop User Menu -->
    <flux:dropdown position="bottom" align="start">
        <flux:profile
            :name="auth()->user()->name"
            :initials="auth()->user()->initials()"
            icon-trailing="chevrons-up-down"
        />

        <flux:menu class="w-[220px]">
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator/>

            <flux:menu.radio.group>
                <flux:menu.item :href="route('settings.profile')" icon="cog"
                                wire:navigate>{{ __('Settings') }}</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator/>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

    <flux:spacer/>

    <flux:dropdown position="top" align="end">
        <flux:profile
            :initials="auth()->user()->initials()"
            icon-trailing="chevron-down"
        />

        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator/>

            <flux:menu.radio.group>
                <flux:menu.item :href="route('settings.profile')" icon="cog"
                                wire:navigate>{{ __('Settings') }}</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator/>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

{{ $slot }}

@fluxScripts

@persist('toast')
<flux:toast
    position='top-right'
    class="pt-24"/>
@endpersist

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
