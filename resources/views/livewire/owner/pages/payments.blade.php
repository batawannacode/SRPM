<div class="space-y-6">
    {{-- === Header === --}}
    <div class="flex md:justify-between items-center gap-5">
        <div class="max-md:hidden">
            <h1 class="text-2xl font-bold text-neutral-700 dark:text-neutral-200">Payments</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Table of all tenants who hold a lease for this property, enabling the owner to view tenants payments.</p>
        </div>
        <x-ui.input clearable wire:model.live="search" placeholder="Search..." class="max-w-sm" leftIcon="magnifying-glass" />
    </div>
    {{-- Payments Table --}}
    <x-ui.card hoverless size="full">
        <div class="overflow-x-auto bg-white dark:bg-neutral-800">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 whitespace-nowrap">Tenant Name</th>
                        <th class="p-4 whitespace-nowrap">Last Payment Date</th>
                        <th class="p-4 whitespace-nowrap text-center">Amount</th>
                        <th class="p-4 whitespace-nowrap">Payment Method</th>
                        <th class="p-4 whitespace-nowrap">Reference Number</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($this->tenants as $tenant)
                    @php
                    $latestPayment = $tenant->latestPaidPayment();
                    @endphp
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/60 transition">
                        {{-- Tenant Name --}}
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $tenant->user->full_name ?? 'N/A' }}
                        </td>

                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $latestPayment?->payment_date?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}
                        </td>

                        <td class="px-4 py-3 text-center font-medium text-primary dark:text-amber-400">
                            @if($latestPayment)
                            ₱{{ number_format($latestPayment->payment?->amount, 2) }}
                            @else
                            —
                            @endif
                        </td>

                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ ucfirst($latestPayment?->payment?->payment_method ?? '—') }}
                        </td>

                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $latestPayment?->payment?->reference_number ?? '—' }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-center">
                            <x-ui.button size="sm" color="emerald" href="{{ route('owner.tenant.payments', ['tenant' => $tenant->id]) }}">
                                View All Payments
                            </x-ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-neutral-500 dark:text-neutral-400">
                            No tenants with leases found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div>
        {{ $this->tenants->links() }}
    </div>
</div>
