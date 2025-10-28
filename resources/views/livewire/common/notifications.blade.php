<x-ui.modal id="notifications" width="md" animation="slide" heading="Notifications" slideover sticky-header>
    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 px-2 pb-3">
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-white bg-primary dark:bg-green-500 rounded-full px-2 py-0.5">
                {{ count($notifications ?? [
                    ['type' => 'message', 'name' => 'Michael Jordan'],
                    ['type' => 'activity', 'name' => 'Jane Doe'],
                    ['type' => 'activity', 'name' => 'John Doe The Great'],
                ]) }}
            </span>
        </div>

        {{-- HEADER BUTTONS --}}
        <div class="flex items-center gap-2">
            <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium" wire:click="markAllAsRead">
                Mark all as read
            </button>
            <button class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium" wire:click="deleteAllNotifications">
                Delete all
            </button>
        </div>
    </div>

    {{-- BODY --}}
    <div class="space-y-2 mt-4 max-h-[70vh] overflow-y-auto">
        @foreach ([
        ['id' => 1, 'type' => 'message', 'name' => 'Michael Jordan', 'content' => 'Hi there! I noticed you forgot...', 'time' => '03:25 am', 'unread' => true],
        ['id' => 2, 'type' => 'activity', 'name' => 'Jane Doe', 'content' => 'Jane accepted your task offer.', 'time' => 'Yesterday', 'unread' => false],
        ['id' => 3, 'type' => 'activity', 'name' => 'John Doe The Great', 'content' => 'John completed your request.', 'time' => '2 days ago', 'unread' => true],
        ['id' => 4, 'type' => 'message', 'name' => 'Queen Sliyva', 'content' => 'Can we discuss the next step?', 'time' => '10:45 am', 'unread' => false],
        ] as $notif)
        <div class="group w-full flex items-center justify-between p-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/60 transition text-start">
            {{-- LEFT: ICON + DETAILS --}}
            <div class="flex items-center gap-3 w-full">
                {{-- ICON --}}
                @if ($notif['type'] === 'message')
                <div class="flex items-center justify-center shrink-0 size-10 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                    <x-ui.icon name="ps:chat-circle-dots" class="size-5" />
                </div>
                @elseif ($notif['type'] === 'activity')
                <div class="flex items-center justify-center shrink-0 size-10 rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                    <x-ui.icon name="ps:lightning" class="size-5" />
                </div>
                @else
                <div class="flex items-center justify-center shrink-0 size-10 rounded-full bg-neutral-200 dark:bg-neutral-700">
                    <x-ui.icon name="ps:bell" class="size-5 text-neutral-600 dark:text-neutral-300" />
                </div>
                @endif

                {{-- DETAILS --}}
                <div class="flex flex-col flex-1">
                    <h3 class="font-medium text-sm text-neutral-800 dark:text-neutral-100">
                        {{ $notif['name'] }}
                    </h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate w-48 md:w-64">
                        {{ $notif['content'] }}
                    </p>
                </div>

                {{-- TIME + UNREAD DOT --}}
                <div class="flex flex-col items-end text-xxs whitespace-nowrap text-neutral-400 mr-2">
                    <span>{{ $notif['time'] }}</span>
                    @if($notif['unread'])
                    <span class="w-2 h-2 bg-yellow-400 rounded-full mt-1"></span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-ui.modal>

