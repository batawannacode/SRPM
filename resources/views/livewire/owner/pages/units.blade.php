<div class="space-y-6">
    {{-- === Header === --}}
    <div class="flex md:justify-between items-center gap-5">
        <div class="max-md:hidden">
            <h1 class="text-2xl font-bold text-neutral-700 dark:text-neutral-200">Units/Rooms</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Track, review and manage all Units for your property.</p>
        </div>
    </div>
    <div class="flex items-center justify-between gap-5 mb-6">
        <x-ui.input clearable wire:model.live="search" placeholder="Search..." class="max-w-sm" leftIcon="magnifying-glass" />
        <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'unit-modal' })">
            Add Units
        </x-ui.button>
    </div>
    {{-- === Units Table === --}}
    <x-ui.card hoverless size="full">
        <div class="overflow-x-auto dark:border-neutral-700 bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 whitespace-nowrap">Unit Name/Number</th>
                        <th class="p-4 whitespace-nowrap">Status</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($this->units as $unit)
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800 transition">
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $unit->unit_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            <x-ui.badge color="{{ $unit->status === 'maintenance' ? 'amber' : ($unit->status === 'occupied' ? 'emerald' : '') }}">
                                {{ ucfirst(strtolower($unit->status)) }}
                            </x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 flex items-center justify-center">
                            <x-ui.button class="!text-emerald-600" size="sm" variant="ghost" wire:click="editUnit({{ $unit->id }})">
                                Edit
                            </x-ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-neutral-700 dark:text-neutral-200">
                            No units found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div>
        {{ $this->units->links() }}
    </div>
    {{-- === Add/Edit Unit Modal === --}}
    <x-ui.modal id="unit-modal" heading="{{ $isEditing ? 'Edit Unit' : 'Add Unit' }}" description="{{ $isEditing ? 'Update the Unit details.' : 'Add a new Unit/Room for a property.' }}" :closeByClickingAway="false" :closeButton="false" width="lg">
        <div class="space-y-4">
            <!-- Unit Number/Name -->
            <x-ui.field>
                <x-ui.label for="name" text="Unit Number/Name" />
                <x-ui.input id="name" placeholder="Enter unit number/name" wire:model="form.unit_number" />
            </x-ui.field>
            <!-- Status Select -->
            <x-ui.field>
                <x-ui.label for="status" text="Status" />
                <x-ui.select id="status" triggerClass="!p-3" class="text-sm" wire:model.live="form.status" placeholder="{{ trim($this->form['status']) ?: 'Select status..' }}">
                    <x-ui.select.option value="vacant">Vacant</x-ui.select.option>
                    <x-ui.select.option value="occupied">Occupied</x-ui.select.option>
                    <x-ui.select.option value="maintenance">Maintenance</x-ui.select.option>
                </x-ui.select>
            </x-ui.field>
        </div>

        {{-- Error Messages --}}
        <div class="text-start">
            @if ($errors->any())
            @foreach($errors->all() as $error)
            <x-ui.error class="text-xs" :messages="$error" />
            @endforeach
            @endif
        </div>

        <div class="flex justify-end gap-3 mt-5">
            <x-ui.button color="neutral" variant="ghost" wire:click="cancelModal">
                Cancel
            </x-ui.button>

            <x-ui.button color="emerald" wire:click="saveUnit">
                @if (!$isEditing) Add @else Update @endif Unit
            </x-ui.button>
        </div>
    </x-ui.modal>

</div>
