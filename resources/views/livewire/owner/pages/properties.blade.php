
<div class="space-y-6">
    <div class="flex items-center justify-end md:justify-between gap-5">
        <div class="hidden md:flex flex-col items-start ">
            <h1 class="text-xl md:text-2xl font-bold text-neutral-800 dark:text-neutral-200">Properties</h1>
            <p class="text-sm text-neutral-500">
                Overview of all your properties and their income, expenses, and occupancy status.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'utility-bill-modal' })">
                Add Utility Bill
            </x-ui.button>
            <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'new-property-modal' })">
                Add New Property
            </x-ui.button>
        </div>
    </div>
    {{-- Properties Table --}}
    <x-ui.card hoverless size="full" >
        <div class="overflow-x-auto dark:border-neutral-700 bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 text-left whitespace-nowrap">Property</th>
                        <th class="p-4 text-left">Address</th>
                        <th class="p-4 text-left whitespace-nowrap">Total Income</th>
                        <th class="p-4 text-left whitespace-nowrap">Total Expenses</th>
                        <th class="p-4 text-left whitespace-nowrap">Total Net Income</th>
                        <th class="p-4 text-left whitespace-nowrap">Units/Rooms</th>
                        <th class="p-4 text-left whitespace-nowrap">Occupied</th>
                        <th class="p-4 text-left whitespace-nowrap">Maintenance</th>
                        <th class="p-4 text-left whitespace-nowrap">Vacant</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($this->properties as $property)
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800 transition">
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                            {{ $property['name'] }}
                        </td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                            <p class="w-60">
                                {{ $property['address'] }}
                            </p>
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                            ₱ {{ number_format($property['income'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                            ₱ {{ number_format($property['expenses'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap ">
                            <div class="flex items-center gap-1">
                                @if ($property['isNetIncomeHigher'])
                                <x-ui.icon name="arrow-up" class="text-emerald-500! size-4" />
                                <span class="text-emerald-600 dark:text-emerald-400">
                                    ₱ {{ number_format($property['netIncome'], 2) }}
                                </span>
                                @else
                                <x-ui.icon name="arrow-down" class="text-rose-500! size-4" />
                                <span class="text-rose-600 dark:text-rose-400">
                                    ₱ {{ number_format($property['netIncome'], 2) }}
                                </span>
                                @endif
                            </div>
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
                        <td class="px-4 py-3 text-center">
                           <div class="flex items-center gap-1">
                                <x-ui.button class="text-emerald-600!" size="sm" variant="ghost" wire:click="editProperty({{ $property['id'] }})">
                                    Edit
                                </x-ui.button>
                                <x-ui.button size="sm" variant="ghost" iconAfter="arrow-right" wire:click="changeActiveProperty({{ $property['id'] }})" iconClasses="text-primary!" class="text-primary!">
                                    Switch
                                </x-ui.button>
                           </div>
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
    <div>
        {{ $this->getProperties()->links() }}
    </div>

    {{-- Add Utility Bill Modal --}}
    <x-ui.modal id="utility-bill-modal" heading="Add Utility Bill" description="Add a new utility bill/expenses for a property. This can water, electricity, or other utility bills." :closeByClickingAway="false" :closeButton="false" width="lg">
        <div class="space-y-4">
            <!-- Property Select -->
            <x-ui.field>
                <x-ui.label for="property" text="Property" />
                <x-ui.select id="property" label="Property" triggerClass="!p-3" class="text-sm" wire:model="form.property_id" placeholder="Select a property">
                    @foreach ($this->getAllPropertiesProperty() as $property)
                    <x-ui.select.option value="{{ $property->id }}">{{ $property->name }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>

            <!-- Type Select -->
            <x-ui.field>
                <x-ui.label for="type" text="Type" />
                <x-ui.select id="type" label="Type" triggerClass="!p-3" class="text-sm" wire:model.live="form.type" placeholder="Select type">
                    <x-ui.select.option value="electricity">Electricity</x-ui.select.option>
                    <x-ui.select.option value="water">Water</x-ui.select.option>
                    <x-ui.select.option value="maintenance">Maintenance</x-ui.select.option>
                    <x-ui.select.option value="others">Others</x-ui.select.option>
                </x-ui.select>
            </x-ui.field>

            <!-- Description Input (Conditional) -->
            @if($form['type'] === 'others' || $form['type'] === 'maintenance')
            <x-ui.field>
                <x-ui.label for="description" text="Description" />
                <x-ui.textarea wire:model="form.description" placeholder="Enter your description..." />
            </x-ui.field>
            @endif

            <!-- Amount Input -->
            <x-ui.field>
                <x-ui.label for="amount" text="Amount" />
                <x-ui.input label="Amount" type="number" step="0.01" min="0" placeholder="Enter amount" wire:model.defer="form.amount" />
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
            <x-ui.button color="emerald" wire:click="saveUtilityBill">
                Add Utility Bill
            </x-ui.button>
        </div>
    </x-ui.modal>
    {{-- Add New Property Modal --}}
    <x-ui.modal id="new-property-modal" heading="Add New Property" description="Add a new property to the system." :closeByClickingAway="false" :closeButton="false" width="lg">

        <div class="space-y-4">
             <!-- Property Name -->
             <x-ui.field>
                 <x-ui.label for="property_name" text="{{ __('Name') }}" />
                 <x-ui.input required type="text" wire:model='form.property_name' placeholder="Property 1" />
             </x-ui.field>
             <!-- Property Address -->
             <x-ui.field>
                 <x-ui.label for="property_address" text="{{ __('Address') }}" />
                 <x-ui.input required type="text" wire:model='form.property_address' placeholder="Blk 1" />
             </x-ui.field>
             <!-- Total Units -->
             <x-ui.field>
                 <x-ui.label for="total_units" text="{{ __('Total Units/Rooms') }}" />
                 <x-ui.input required type="number" wire:model='form.total_units' placeholder="99" />
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
            <x-ui.button color="emerald" wire:click="saveProperty">
                Add Property
            </x-ui.button>
        </div>
    </x-ui.modal>
    {{-- Edit Property Modal --}}
    <x-ui.modal id="edit-property-modal" heading="Edit Property" description="Edit the details of the property." :closeByClickingAway="false" :closeButton="false" width="lg">

        <div class="space-y-4">
             <!-- Property Name -->
             <x-ui.field>
                 <x-ui.label for="property_name" text="{{ __('Name') }}" />
                 <x-ui.input required type="text" wire:model='form.property_name' placeholder="Property 1" />
             </x-ui.field>
             <!-- Property Address -->
             <x-ui.field>
                 <x-ui.label for="property_address" text="{{ __('Address') }}" />
                 <x-ui.input required type="text" wire:model='form.property_address' placeholder="Blk 1" />
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
            <x-ui.button color="emerald" wire:click="updateProperty">
                Update Property
            </x-ui.button>
        </div>
    </x-ui.modal>
</div>




