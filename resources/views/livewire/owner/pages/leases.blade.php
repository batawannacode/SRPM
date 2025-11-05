<div class="space-y-6">
    {{-- === Header === --}}
    <div class="flex md:justify-between items-center gap-5">
        <div class="max-md:hidden">
            <h1 class="text-2xl font-bold text-neutral-700 dark:text-neutral-200">Leases</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Track, review and manage all Leases for your property.</p>
        </div>
        {{-- Date Range Picker --}}
        <div class=" flex item-center gap-5 w-full md:w-[500px]">
            <x-ui.field>
                <x-ui.label for="start_date" text="{{ __('Start Date') }}" />
                <x-ui.input type="date" id="start_date" wire:model.live="startDate" clearable />
            </x-ui.field>
            <x-ui.field>
                <x-ui.label for="end_date" text="{{ __('End Date') }}" />
                <x-ui.input type="date" id="end_date" wire:model.live="endDate" clearable />
            </x-ui.field>
        </div>
    </div>
    <div class="flex items-center justify-between gap-5 mb-6">
        <x-ui.input clearable wire:model.live="search" placeholder="Search..." class="max-w-sm" leftIcon="magnifying-glass" />
        <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'add-lease-modal' })">
            Add Lease
        </x-ui.button>
    </div>
     {{-- === Leases Table === --}}
     <x-ui.card hoverless size="full">
         <div class="overflow-x-auto bg-white dark:bg-neutral-900">
             <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                 <thead class="bg-neutral-100 dark:bg-neutral-700">
                     <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                         <th class="p-4 whitespace-nowrap">Lease ID</th>
                         <th class="p-4 whitespace-nowrap">Lease Status</th>
                         <th class="p-4 whitespace-nowrap">Tenant Name</th>
                         <th class="p-4 whitespace-nowrap">Unit Name/Number</th>
                         <th class="p-4 whitespace-nowrap">Rent Price</th>
                         <th class="p-4 whitespace-nowrap">Start Date</th>
                         <th class="p-4 whitespace-nowrap">End Date</th>
                         <th class="p-4 text-center">Actions</th>
                     </tr>
                 </thead>
                 <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($this->propertyLeases as $lease)
                     <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800 transition">
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->id }}</td>
                          <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                              <x-ui.badge color="{{ $lease->status === 'terminated' ? 'rose' : ($lease->status === 'active' ? 'emerald' : 'amber') }}">
                                  {{ ucfirst(strtolower($lease->status)) }}
                              </x-ui.badge>
                          </td>
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->tenant->user->fullName ?? 'N/A' }}</td>
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->unit->unit_number ?? 'N/A' }}</td>
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 whitespace-nowrap">₱ {{ number_format($lease->rent_price, 2) }}</td>
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->start_date?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->end_date?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                         <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center justify-center gap-2">
                                <x-ui.button href="{{ route('owner.lease.details', $lease->id) }}" color="emerald" size="sm">
                                    View Details
                                </x-ui.button>
                            </div>
                         </td>
                     </tr>
                     @empty
                     <tr>
                         <td colspan="9" class="px-4 py-3 text-center text-neutral-700 dark:text-neutral-200">
                             No lease's found.
                         </td>
                     </tr>
                     @endforelse
                 </tbody>
             </table>
         </div>
     </x-ui.card>
     <div>
         {{ $this->propertyLeases->links() }}
     </div>
     {{-- === Add Lease Modal === --}}
     <x-ui.modal id="add-lease-modal" heading="Add New Lease" description="Add a new Lease for a property" :closeByClickingAway="false" :closeButton="false" width="xl" slideover sticky-header>
        <form wire:submit.prevent="addLease">
            <div class="space-y-4 mb-4">
                <!-- Select Unit -->
                <x-ui.field>
                    <x-ui.label for="unit" text="Unit" />
                    <x-ui.description>Select or search for a vacant units</x-ui.description>
                    <x-ui.select id="unit" triggerClass="!p-3" searchable clearable class="text-sm" wire:model="form.unit" placeholder="Select unit..">
                        @foreach($this->units as $unit)
                            <x-ui.select.option value="[{{ $unit->unit_number }},{{ $unit->id }}]">{{ $unit->unit_number }}</x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
                <!-- Select Tenant -->
                <x-ui.field>
                    <x-ui.label for="tenant" text="Tenant" />
                    <x-ui.description>Select or search for new tenant</x-ui.description>
                    <x-ui.select id="tenant" triggerClass="!p-3" searchable clearable class="text-sm" wire:model="form.tenant" placeholder="Select tenant..">
                        @foreach($this->tenants as $tenant)
                            <x-ui.select.option value="[{{ $tenant->user->full_name }},{{ $tenant->id }}]">{{ $tenant->user->full_name }}</x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
                <!-- Start and End date Input -->
                <x-ui.field>
                    <x-ui.label for="start_date" text="{{ __('Start Date') }}" />
                    <x-ui.input type="date" id="start_date" wire:model="form.start_date" clearable />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label for="end_date" text="{{ __('End Date') }}" />
                    <x-ui.input type="date" id="end_date" wire:model="form.end_date" clearable />
                </x-ui.field>
                <!-- Rent Price -->
                <x-ui.field>
                    <x-ui.label for="price" text="Rent Price" />
                    <x-ui.input id="price" type="number" step="0.01" min="0" clearable placeholder="Enter amount" wire:model="form.rent_price" />
                </x-ui.field>
                <!-- Lease Documents -->
                <x-ui.field>
                    <x-ui.label for="Lease" text="Documents" />
                    <x-ui.description>Upload the lease documents for the selected unit and tenant (PDF, DOCX, JPG & PNG)</x-ui.description>
                    <x-filepond::upload
                        :max-files="5"
                        {{-- :required="true" --}}
                        :multiple="true"
                        wire:model="form.lease_documents"
                    />
                </x-ui.field>
            </div>

            {{-- Error Messages --}}
            <div class="text-start mb-4">
                @if ($errors->any())
                @foreach($errors->all() as $error)
                <x-ui.error class="text-xs" :messages="$error" />
                @endforeach
                @endif
            </div>

            {{-- Information page, reminding to ensure input are correct --}}
            <x-ui.alerts type="info" class="mb-4">
                <x-slot:heading>Reminder</x-slot:heading>
                <x-slot:content>
                    <p>Please ensure that all information is correct before adding the lease.</p>
                </x-slot:content>
            </x-ui.alerts>

            <div class="flex justify-end gap-3 mt-5 w-full">
                <x-ui.button color="neutral" variant="ghost" wire:click="cancelModal" class="w-full">
                    Cancel
                </x-ui.button>
                <x-ui.button type="submit" color="emerald" class="w-full">
                    Add Lease
                </x-ui.button>
            </div>
        </form>
     </x-ui.modal>

</div>
