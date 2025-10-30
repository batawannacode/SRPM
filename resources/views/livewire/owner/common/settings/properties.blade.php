
<div>
    <div class="flex items-center justify-between gap-5 mb-6">
        <h3 class="text-lg font-semibold dark:text-neutral-400 text-neutral-800">
            Property Summary
        </h3>
        <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'utility-bill-modal' })">
            Add Utility Bill
        </x-ui.button>
    </div>
    {{-- Properties Table --}}
    <x-ui.card hoverless size="full" >
        <div class="overflow-x-auto dark:border-neutral-700 bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-200 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="px-4 py-3">Property</th>
                         <th class="px-4 py-3 text-left whitespace-nowrap">Total Income</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Total Expenses</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Total Revenue</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Units/Rooms</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Occupied</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Maintenance</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Vacant</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Due Payments</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($this->properties as $property)
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800 transition">
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                            {{ $property['name'] }}
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                            ₱ {{ number_format($property['income'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                            ₱ {{ number_format($property['expenses'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap flex justify-start items-center gap-1">
                            @if ($property['isRevenueHigher'])
                            <x-ui.icon name="arrow-up" class="text-emerald-500! size-4" />
                            <span class="text-emerald-600 dark:text-emerald-400">
                                ₱ {{ number_format($property['revenue'], 2) }}
                            </span>
                            @else
                            <x-ui.icon name="arrow-down" class="text-rose-500! size-4" />
                            <span class="text-rose-600 dark:text-rose-400">
                                ₱ {{ number_format($property['revenue'], 2) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                            {{ $property['units'] }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap text-emerald-600 dark:text-emerald-400">
                            {{ $property['vacancy']['occupied'] }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap text-amber-500 dark:text-amber-400">
                            {{ $property['vacancy']['maintenance'] }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                            {{ $property['vacancy']['vacant'] }}
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap text-rose-600 dark:text-rose-400">
                            ₱ {{ number_format($property['due'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" wire:click="changeActiveProperty({{ $property['id'] }})" class="text-primary hover:underline flex items-center justify-center gap-1">
                                Switch
                                <x-ui.icon name="arrow-right" class="size-4 text-primary!" />
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-neutral-500 dark:text-neutral-400">
                            No properties found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <x-ui.modal id="utility-bill-modal" heading="Add Utility Bill" :closeByClickingAway="false" :closeButton="false" width="lg">
        <div class="space-y-4">
            <!-- Property Select -->
            <x-ui.select label="Property" triggerClass="!p-3" class="text-sm" wire:model="form.property_id" placeholder="Select a property">
                @foreach ($this->getAllPropertiesProperty() as $property)
                <x-ui.select.option value="{{ $property->id }}">{{ $property->name }}</x-ui.select.option>
                @endforeach
            </x-ui.select>

            <!-- Type Select -->
            <x-ui.select label="Type" triggerClass="!p-3" class="text-sm" wire:model="form.type" placeholder="Select type">
                <x-ui.select.option value="electricity">Electricity</x-ui.select.option>
                <x-ui.select.option value="water">Water</x-ui.select.option>
                <x-ui.select.option value="maintenance">Maintenance</x-ui.select.option>
                <x-ui.select.option value="others">Others</x-ui.select.option>
            </x-ui.select>

            <!-- Amount Input -->
            <x-ui.input label="Amount" type="number" step="0.01" min="0" placeholder="Enter amount" wire:model.defer="form.amount" />
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
            <x-ui.button color="neutral" variant="ghost" wire:click="$dispatch('close-modal', { id: 'utility-bill-modal' })">
                    Cancel
            </x-ui.button>

            <x-ui.button color="emerald" wire:click="saveUtilityBill">
                Save Bill
            </x-ui.button>
        </div>
    </x-ui.modal>
</div>




