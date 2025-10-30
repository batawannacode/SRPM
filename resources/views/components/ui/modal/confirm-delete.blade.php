@props([
'id' => null,
'title' => 'Delete Confirmation',
'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
'confirmText' => 'Delete',
'cancelText' => 'Cancel',
'triggerClass' => ''
])

<div class="flex flex-col justify-center items-center {{ $triggerClass }}" >

    <!-- Trigger Slot -->
    <div wire:click="$dispatch('open-modal', { id: 'confirm-delete-{{ $id }}' })">
        {{ $trigger ?? '' }}
    </div>

    <!-- Modal -->
    <x-ui.modal id="confirm-delete-{{ $id }}" class="hidden" :closeByClickingAway="false" :closeButton="false">
        <div class="p-5">
            <h2 class="text-lg font-semibold text-red-600">{{ $title }}</h2>
            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                {{ $message }}
            </p>

            <div class="flex justify-end mt-6 space-x-3">
                <x-ui.button variant="ghost" size="sm" wire:click="$dispatch('close-modal', { id: 'confirm-delete-{{ $id }}' })">
                    {{ $cancelText }}
                </x-ui.button>

                <x-ui.button variant="danger" size="sm" {{ $attributes->whereStartsWith('wire:click') ?? '' }} wire:click="$dispatch('close-modal', { id: 'confirm-delete-{{ $id }}' })">
                    {{ $confirmText }}
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>
</div>

