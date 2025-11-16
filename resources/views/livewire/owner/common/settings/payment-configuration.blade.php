<div class="relative">
    {{-- Loader --}}
    <div wire:loading.flex wire:target="saveMethod, deleteMethod, editMethod" class="absolute inset-0 z-20 flex items-center justify-center bg-white/60 dark:bg-neutral-900/60 backdrop-blur-sm">
        <svg class="animate-spin text-primary dark:text-indigo-400 size-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between gap-5 mb-6">
        <p class="text-sm text-neutral-600 dark:text-neutral-400 mr-4">
            This payment method is used as the default billing source for all properties
            <br>
             the user manages, unless a property explicitly overrides it.
        </p>
        <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'payment-method-modal' })">
            Add Payment Method
        </x-ui.button>
    </div>

    {{-- Payment Methods Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($paymentMethods as $method)
        <x-ui.card hoverless size="full" class="relative flex flex-col group">
            {{-- QR Code Preview --}}
            <div class="relative w-full h-48 sm:h-56 rounded-xl overflow-hidden
                        border-2 border-primary/30 group-hover:border-primary/80
                        transition-all duration-300 shadow-sm group-hover:shadow-md bg-neutral-100 dark:bg-neutral-800">
                @if ($method->qr_image_path)
                <img src="{{ asset('storage/'.$method->qr_image_path) }}" alt="{{ $method->type }}" class="object-contain w-full h-full transition-transform duration-300 group-hover:scale-105">
                @else
                <div class="flex items-center justify-center h-full text-neutral-400 dark:text-neutral-500">
                    <x-ui.icon name="image" class="size-8" />
                </div>
                @endif
                <div class="absolute inset-0 rounded-xl ring-2 ring-transparent group-hover:ring-emerald-400/40 transition-all"></div>
            </div>

            {{-- Method Info --}}
            <div class="mt-4 space-y-1 text-center sm:text-left relative">
                {{-- Dropdown Menu --}}
                <div class="absolute top-0 right-0">
                    <x-ui.dropdown position="bottom-end">
                        <x-slot:button class="hover:bg-neutral-200 dark:hover:bg-neutral-700 rounded p-0!">
                            <x-ui.icon variant="bold" name="ps:dots-three" class="size-6 shrink-0" />
                        </x-slot:button>
                        <x-slot:menu>
                            <x-ui.dropdown.item wire:click="editMethod({{ $method->id }})">Edit</x-ui.dropdown.item>
                            <x-ui.dropdown.item wire:click="deleteMethod({{ $method->id }})">Delete</x-ui.dropdown.item>
                        </x-slot:menu>
                    </x-ui.dropdown>
                </div>

                <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50">
                    {{ $method->type }}
                </h3>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 truncate">
                    {{ $method->account_name }}
                </p>
                <p class="text-sm text-neutral-500 dark:text-neutral-500 truncate">
                    {{ $method->account_number }}
                </p>
            </div>
        </x-ui.card>
        @empty
        <p class="text-center text-neutral-500 dark:text-neutral-400 col-span-full py-10">
            No payment methods added yet.
        </p>
        @endforelse
    </div>

    {{-- Add/Edit Modal --}}
    <x-ui.modal id="payment-method-modal" :closeByClickingAway="false" :closeButton="false" width="lg" heading="{{ $isEditing ? 'Edit Payment Method' : 'Add Payment Method' }}">
        <form wire:submit.prevent="saveMethod" class="space-y-5">
            {{-- QR Upload --}}
             <div class="flex items-center gap-4">
                @if ($form->image)
                <img src="{{ $form->image->temporaryUrl() }}" class="max-h-[600px] h-auto w-full object-contain rounded-md border" />
                @elseif ($form->image_path)
                {{-- Existing image from DB --}}
                <img src="{{ Storage::url($form->image_path) }}" class="max-h-[600px] h-auto w-full object-contain rounded-md border" />
                @endif
             </div>
            <x-ui.field>
                <x-ui.label text="QR Code Image" />
                <label for="qr_code_image">
                    <div class="h-10 text-sm cursor-pointer flex items-center justify-center text-white font-medium bg-emerald-500 hover:bg-emerald-600 dark:hover:bg-emerald-500 dark:bg-emerald-600 border border-emerald-600 rounded-lg shadow-xs duration-200 ease-in-out">
                        <input type="file" id="qr_code_image" wire:model.live="form.image" accept="image/*" class="hidden" />
                        <span>Upload QR Code</span>
                    </div>
                </label>
            </x-ui.field>

            {{-- Name --}}
            <x-ui.field>
                <x-ui.label text="Payment Method Name" />
                <x-ui.input wire:model="form.name" placeholder="e.g. GCash, Maya, Seabank" />
            </x-ui.field>

            {{-- Account Name --}}
            <x-ui.field>
                <x-ui.label text="Account Name" />
                <x-ui.input wire:model="form.account_name" placeholder="John Doe" />
            </x-ui.field>

            {{-- Account Number --}}
            <x-ui.field>
                <x-ui.label text="Account Number" />
                <x-ui.input wire:model="form.account_number" placeholder="09123456789 or 1234567890" />
            </x-ui.field>

            {{-- Error Messages --}}
            <div class="text-start">
            @if ($errors->any())
            @foreach($errors->all() as $error)
            <x-ui.error class="text-xs" :messages="$error" />
            @endforeach
            @endif
            </div>

            <div class="flex justify-end gap-3">
                <x-ui.button color="neutral" type="button" variant="ghost" wire:click="cancelModal">
                    Cancel
                </x-ui.button>
                <x-ui.button color="emerald" type="submit">
                    {{ $isEditing ? 'Update' : 'Save' }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>

