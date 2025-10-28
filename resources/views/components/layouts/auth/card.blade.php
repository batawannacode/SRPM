<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.head-scripts')
    </head>
    <body class="min-h-screen font-poppins bg-background antialiased relative">
        <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 relative">
            <a href="{{ route('welcome') }}" class="flex items-center space-x-3 fixed top-5 left-5">
                <x-app-logo-icon class="size-12 rounded-full fill-current text-indigo-600 dark:text-indigo-400" />
                <span class="text-xl font-bold text-neutral-800 dark:text-neutral-200">SRPM</span>
            </a>
            <div class="fixed top-5 right-5">
                <x-ui.theme-switcher iconClasses="size-5" variant="inline" />
            </div>
            <div class="rounded-2xl border max-md:mt-20 bg-white dark:bg-neutral-800 dark:border-neutral-700 text-stone-800 shadow-lg p-5 md:p-8 w-full max-w-md">
                <div class="flex w-full flex-col gap-5">
                    @if($user)
                        <span class="w-full font-bold text-center text-3xl text-primary">{{ $user }} Portal</span>
                    @endif
                    <div class="flex flex-col gap-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        <x-ui.toast
            position="top-center"
            maxToasts="3"
            progressBarVariant="thin"
            progressBarAlignment="top"
        />
        @livewireScriptConfig
        @include('partials.body-scripts')
    </body>
</html>
