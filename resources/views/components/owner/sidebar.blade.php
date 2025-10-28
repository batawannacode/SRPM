<div class="fixed inset-y-0 left-0 z-40 w-60 transform transition-all duration-200 ease-in-out
           bg-white dark:bg-neutral-800
           flex flex-col shadow-xs
           border-r dark:border-neutral-700
           opacity-0 -translate-x-full
           lg:opacity-100 lg:translate-x-0">

    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="flex grow flex-col gap-y-6 bg-white dark:bg-neutral-800 pb-4">
        <a href="{{ route('welcome') }}" class="flex items-center justify-center space-x-2 p-2 border-b border-neutral-200 dark:border-neutral-700">
            <x-app-logo-icon class="size-12 rounded-full fill-current text-indigo-600 dark:text-indigo-400" />
            <span class="text-xl font-bold text-neutral-800 dark:text-neutral-200">SRPM</span>
        </a>
        <nav id="admin_sidebar_tab" class="flex flex-1 flex-col ">
            <x-owner.sidebar-content />
        </nav>
    </div>
</div>
