@props(['title' => ''])

<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8">
    <button type="button" class="-m-2.5 p-2.5 text-gray-700 dark:text-neutral-200 lg:hidden" x-on:click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Separator -->
    <div class="h-6 w-px bg-neutral-300 dark:bg-neutral-700 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 justify-between gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex items-center justify-start">
            <span class="hidden sm:block text-lg font-semibold text-primary dark:text-indigo-400">{{ $title }}</span>
        </div>
        <div class="flex items-center gap-2 text-neutral-600" x-data="{
                imageUrl: '{{ asset('storage/assets/default.png') }}',
            }" x-on:profile-image-updated.window="imageUrl = $event.detail.image_url">

            {{-- DARKMODE TOGGLE --}}
            <x-ui.theme-switcher iconClasses="size-5" variant="inline" class="text-neutral-600 dark:text-neutral-200 hover:bg-indigo-100 dark:hover:bg-neutral-700" />

            {{-- NOTICATION --}}
            <livewire:common.notifications/>

            {{-- PROFILE --}}
            <div class="flex items-center gap-2">
                @if($user->avatar_path)
                <x-ui.avatar name="{{ $user->fullName }}" color="auto" size="md" circle :src="asset('storage/'.$user->avatar_path)" />
                @else
                <x-ui.avatar name="{{ $user->fullName }}" color="auto" size="md" circle />
                @endif
                <div class="hidden sm:flex flex-col">
                    <span class=" text-sm font-medium text-neutral-700 dark:text-neutral-200 ">{{ $user->fullName }}</span>
                    <span class=" text-xs font-medium text-neutral-500 dark:text-neutral-400 ">{{ $user->email }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
