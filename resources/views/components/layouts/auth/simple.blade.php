<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.head-scripts')
    </head>
    <body class="font-poppins min-h-screen bg-background antialiased dark:bg-linear-to-b relative">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-6">
                <div class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-28 fill-current text-black dark:text-white" />
                    </span>
                </div>

                <div class="flex flex-col gap-6">
                    {{ $slot }}
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
