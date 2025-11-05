<div class="space-y-6">
    {{-- === Header === --}}
    <div class="flex md:justify-between items-center gap-5">
        <div class="max-md:hidden">
            <h1 class="text-2xl font-bold text-neutral-700 dark:text-neutral-200">Payments</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Track, review and manage all Payments for your property.</p>
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
    </div>
    {{-- Payments Table --}}
    <x-ui.card hoverless size="full">
        <div class="overflow-x-auto bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-800/80">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 whitespace-nowrap">Payment Date</th>
                        <th class="p-4 whitespace-nowrap">Lease Status</th>
                        <th class="p-4 whitespace-nowrap">Tenant Name</th>
                        <th class="p-4 whitespace-nowrap">Unit Number</th>
                        <th class="p-4 whitespace-nowrap text-center">Amount</th>
                        <th class="p-4 whitespace-nowrap">Payment Method</th>
                        <th class="p-4 whitespace-nowrap">Account Number</th>
                        <th class="p-4 whitespace-nowrap">Reference Number</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($this->payments as $payment)
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/60 transition">
                        {{-- Payment Date --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $payment->payment_date?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}
                        </td>

                        {{-- Lease Status --}}
                        <td class="px-4 py-3">
                            <x-ui.badge color="{{ $payment->lease->status === 'terminated' ? 'amber' : ($payment->lease->status === 'active' ? 'emerald' : 'neutral') }}">
                                {{ ucfirst($payment->lease->status ?? 'N/A') }}
                            </x-ui.badge>
                        </td>

                        {{-- Tenant Name --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $payment->tenant->user->full_name ?? 'N/A' }}
                        </td>

                        {{-- Unit --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $payment->lease->unit->unit_number ?? 'N/A' }}
                        </td>

                        {{-- Amount --}}
                        <td class="px-4 py-3 text-center font-medium text-primary dark:text-amber-400">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>

                        {{-- Payment Method --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ ucfirst($payment->payment_method ?? 'N/A') }}
                        </td>

                        {{-- Account Number --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $payment->account_number ?? '—' }}
                        </td>

                        {{-- Reference Number --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $payment->reference_number ?? '—' }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-center">
                            <x-ui.button color="emerald" size="sm" href="#">
                                View Details
                            </x-ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-neutral-500 dark:text-neutral-400">
                            No payments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div>
        {{ $this->payments->links() }}
    </div>
</div>
