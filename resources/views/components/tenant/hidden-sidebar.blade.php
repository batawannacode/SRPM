<div class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak x-show="sidebarOpen">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm " aria-hidden="true" x-cloak x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300 ease-linear" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-300 ease-linear" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <div class="fixed inset-0 flex">
        <div class="relative mr-16 flex w-full max-w-60 flex-1 transform transition duration-300 ease-in-out" x-cloak x-show="sidebarOpen" x-on:click.outside="sidebarOpen = false" x-transition:enter="transform transition duration-300 ease-in-out" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition duration-300 ease-in-out" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false" x-cloak x-show="sidebarOpen" x-transition:enter="transition duration-300 ease-in-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-300 ease-in-out" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex grow flex-col gap-y-6 bg-white dark:bg-neutral-800 pb-4">
                <a href="{{ route('welcome') }}" class="flex items-center justify-center space-x-2 p-2 border-b border-neutral-200 dark:border-neutral-700">
                    <x-app-logo-icon class="size-12 rounded-full fill-current text-indigo-600 dark:text-indigo-400" />
                    <span class="text-xl font-bold text-neutral-800 dark:text-neutral-200">SRPM</span>
                </a>

                <nav class="flex flex-1 flex-col">
                    <x-tenant.sidebar-content />
                </nav>
            </div>
        </div>
    </div>
</div>

