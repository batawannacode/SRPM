<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.head-scripts')
    </head>
    <body class="min-h-screen bg-background antialiased font-poppins relative">
        <div class="mx-auto flex min-h-screen flex-col" x-data="{
                sidebarOpen: false,
            }">
            <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
            <x-owner.hidden-sidebar />

            <!-- Static sidebar for desktop -->
            <x-owner.sidebar />

            <div class="transition-all duration-200 ease-in-out pl-0 lg:pl-64">
                {{-- Header --}}
                <x-owner.header :title="$title" />
                {{-- Content --}}
                <main class="relative md:p-8 p-4 overflow-y-auto scroll-smooth w-full h-[calc(100vh-70px)]">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-ui.toast
            position="top-center"
            maxToasts="3"
            progressBarVariant="thin"
            progressBarAlignment="top"
        />
        @livewireScriptConfig
        @filepondScripts
        @include('partials.body-scripts')
    </body>
</html>
