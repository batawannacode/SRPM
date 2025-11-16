<div class="space-y-6">
    {{-- === Header === --}}
    <div class="flex md:justify-between items-center gap-5">
        <div class="max-md:hidden">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Overview of all of your leases — view each lease's status, unit number, rent amount, and start/end dates.
            </p>
        </div>
        {{-- Date Range Picker --}}
        <div class=" flex item-center gap-5 min-w-[500px]">
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
    </div>
    {{-- === Leases Table === --}}
    <x-ui.card hoverless size="full">
        <div class="overflow-x-auto bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 whitespace-nowrap">Lease ID</th>
                        <th class="p-4 whitespace-nowrap">Lease Status</th>
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
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->unit->unit_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 whitespace-nowrap">₱ {{ number_format($lease->rent_price, 2) }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->start_date?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $lease->end_date?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center justify-center gap-2">
                                <x-ui.button href="{{ route('tenant.lease.details', $lease->id) }}" color="emerald" size="sm">
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
</div>

