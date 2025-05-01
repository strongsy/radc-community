<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ $title ?? 'Default Title' }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

@if (session('status'))
    <div id="status-alert" class="mb-6 bg-teal-100 border-l-4 border-teal-500 text-teal-700 p-4 rounded shadow-md relative" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-teal-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">{{ session('status') }}</p>
            </div>
        </div>
        <button type="button" class="absolute top-1 right-1 text-teal-500 hover:text-teal-700" onclick="document.getElementById('status-alert').remove()">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
@endif

<flux:header
    class="w-full border-b px-0! border-zinc-200 dark:border-zinc-700 flex flex-col transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
    <x-front-end-header />
</flux:header>

<!--mobile sidebar-->
<flux:sidebar stashable sticky
              class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>
    <a href="{{ route('home') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0 lg:hidden" wire:navigate>
        <x-app-logo/>
    </a>

    <x-navbar-items/>
</flux:sidebar>

{{ $slot }}

<!--footer-->
<flux:footer
    class="w-full p-5 mt-10 border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 flex flex-col">


   <x-front-end-footer />

</flux:footer>

@fluxScripts
@persist('toast')
<flux:toast position="top right" class="pt-24"/>
@endpersist

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
</body>
</html>

