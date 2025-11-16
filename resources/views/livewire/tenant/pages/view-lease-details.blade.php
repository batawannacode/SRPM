<div class="space-y-6">
    {{-- Breadcrumbs --}}
    <x-ui.breadcrumbs class="mb-10">
        <x-ui.breadcrumbs.item class="hover:underline hover:text-primary" href="{{ route('tenant.leases') }}" separator="slash">
            Leases
        </x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Lease Detail</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-neutral-800 dark:text-neutral-50">
                Lease #{{ $lease->id }}
            </h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Overview and documents of tenant’s lease.
            </p>
        </div>
    </div>

    {{-- Lease, Unit, Tenant Details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Lease Info --}}
        <x-ui.card hoverless size="full" >
            <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50 mb-5 flex items-center gap-2">
                <x-ui.icon name="ps:clipboard-text" class="text-primary size-7" />
                <span class="leading-7">
                     Lease Information
                </span>
            </h3>
            <div class="space-y-2 text-neutral-600 dark:text-neutral-300 text-sm">
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Status:</span>
                    <x-ui.badge color="{{ $lease->status === 'terminated' ? 'rose' : ($lease->status === 'active' ? 'emerald' : 'amber') }}">
                        {{ ucfirst(strtolower($lease->status)) }}
                    </x-ui.badge>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Start Date:</span>
                    <span class="truncate">{{ \Carbon\Carbon::parse($lease->start_date)->format('F d, Y') }}</span>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">End Date:</span>
                    <span class="truncate">{{ \Carbon\Carbon::parse($lease->end_date)->format('F d, Y') }}</span>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Rent Price:</span>
                    <span class="font-semibold text-primary truncate">₱{{ number_format($lease->rent_price, 2) }}</span>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Penalty Value:</span>
                    @if($lease->unit->property->paymentRule->penalty_type === 'fixed')
                        <span class="font-semibold text-primary truncate">₱{{ number_format($lease->unit->property->paymentRule->penalty_value, 2) }}</span>
                    @else
                        <span class="font-semibold text-primary truncate">{{ $lease->unit->property->paymentRule->penalty_value }}%</span>
                    @endif
                </p>
            </div>
        </x-ui.card>

        {{-- Unit Info --}}
        <x-ui.card hoverless size="full" >
            <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50 mb-5 flex items-center gap-2">
                <x-ui.icon name="home" class="text-primary size-7" />
                <span class="leading-7">
                     Unit Details
                </span>
            </h3>
            <div class="space-y-2 text-neutral-600 dark:text-neutral-300 text-sm">
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Unit Number:</span>
                    <span class="truncate">{{ $lease->unit->unit_number }}</span>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Status:</span>
                    <x-ui.badge color="{{ $lease->unit->status === 'maintenance' ? 'amber' : ($lease->unit->status === 'occupied' ? 'emerald' : '') }}">
                        {{ ucfirst(strtolower($lease->unit->status)) }}
                    </x-ui.badge>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Property:</span>
                    <span class="text-start truncate">{{ $lease->unit->property->name ?? '—' }}</span>
                </p>
            </div>
        </x-ui.card>

        {{-- Tenant Info --}}
        {{-- <x-ui.card hoverless size="full" class="max-xl:col-span-full">
            <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50 mb-5 flex items-center gap-2">
                <x-ui.icon name="ps:user" class="text-primary size-7" />
                <span class="leading-7">
                     Tenant Details
                </span>
            </h3>
            <div class="space-y-2 text-neutral-600 dark:text-neutral-300 text-sm">
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Full Name:</span>
                    <span class="truncate">{{ $lease->tenant->user->full_name ?? '—' }}</span>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center gap-2">
                    <span class="font-medium">Email:</span>
                    <span class="truncate">{{ $lease->tenant->user->email ?? '—' }}</span>
                </p>
                <p class="grid grid-cols-2 place-items-start items-center truncate gap-2">
                    <span class="font-medium">Phone:</span>
                    <span class="truncate">{{ $lease->tenant->user->phone_number ?? '—' }}</span>
                </p>
            </div>
        </x-ui.card> --}}
    </div>

    {{-- Lease Documents --}}
    <x-ui.card hoverless size="full" >
        <div class="flex items-center justify-between gap-5 mb-5">
            <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50 flex items-center gap-2">
                <x-ui.icon name="ps:file-text" class="text-primary size-7" />
                <span class="leading-7">
                    Documents
                </span>
            </h3>
            <x-ui.button type="submit" color="amber" icon="ps:file-arrow-down" wire:click="downloadAllDocuments({{ $lease->id }})">
                Download All Documents
            </x-ui.button>
        </div>

        @if ($lease->documents && $lease->documents->count())
        <div class="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach ($lease->documents as $document)
            <a href="{{ $document->tenant_temporary_preview_url }}" target="_blank">
                <div class="group border border-neutral-200 dark:border-neutral-700 hover:ring-2 hover:ring-primary rounded-lg overflow-hidden bg-white dark:bg-neutral-800 shadow-sm transition-all duration-200">
                    <div class="relative h-48 w-full bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center">
                        @if (Str::endsWith($document->file_path, '.pdf'))
                        {{-- PDF icon --}}
                        <x-ui.icon name="ps:file-pdf" class="!text-rose-600 size-16" />
                        @elseif (Str::endsWith($document->file_path, '.docx'))
                        {{-- Word icon --}}
                        <x-ui.icon name="ps:file-doc" class="!text-blue-600 size-16" />
                        @else
                        {{-- Image preview --}}
                        <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->file_name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                        @endif
                    </div>
                    <div class="p-3 border-t border-neutral-200 dark:border-neutral-700">
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate group-hover:underline group-hover:text-primary">
                            {{ $document->file_name }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-neutral-500 dark:text-neutral-400 text-sm text-center py-8">No documents uploaded yet.</p>
        @endif
    </x-ui.card>

    {{-- === Expected Payments Table === --}}
    <x-ui.card hoverless size="full" >
        <div class="flex items-center justify-between gap-5 mb-5">
            <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50 flex items-center gap-2">
                <x-ui.icon name="ps:wallet" class="text-primary size-7" />
                <span class="leading-7">Expected Payments</span>
            </h3>
        </div>

        @if ($this->expectedPayments->count())
        <div class="overflow-x-auto bg-white dark:bg-neutral-800">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 whitespace-nowrap">Payment Date</th>
                        <th class="p-4 whitespace-nowrap">Amount</th>
                        <th class="p-4 whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($this->expectedPayments as $expected)
                    @php
                    $colorClass = $this->getPaymentStatusColor($expected);
                    @endphp
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800  transition {{ $colorClass }}">
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ \Carbon\Carbon::parse($expected->payment_date)->format('F d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 whitespace-nowrap">
                            ₱{{ number_format($lease->rent_price, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <x-ui.badge color="{{ $expected->status === 'paid' ? 'emerald' : 'rose' }}">
                                {{ ucfirst($expected->status) }}
                            </x-ui.badge>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-neutral-500 dark:text-neutral-400 text-sm text-center py-8">
            No expected payments recorded yet.
        </p>
        @endif
    </x-ui.card>
    <div class="mt-4">
        {{ $this->expectedPayments->links() }}
    </div>

    {{-- === Penalties Table === --}}
    <x-ui.card hoverless size="full" >
        <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-50 mb-5 flex items-center gap-2">
            <x-ui.icon name="ps:warning-octagon" class="text-primary size-7" />
            <span class="leading-7">Penalties</span>
        </h3>

        @if ($this->penalties->count())
        <div class="overflow-x-auto bg-white dark:bg-neutral-800">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="p-4 whitespace-nowrap">Due Date</th>
                        <th class="p-4 whitespace-nowrap">Reason</th>
                        <th class="p-4 whitespace-nowrap">Amount</th>
                        <th class="p-4 whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($this->penalties as $penalty)
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800  transition">
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ \Carbon\Carbon::parse($penalty->due_date)->format('F d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            {{ $penalty->reason }}
                        </td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 whitespace-nowrap">
                            ₱{{ number_format($penalty->amount, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <x-ui.badge color="{{ $penalty->is_paid ? 'emerald' : 'rose' }}">
                                {{ $penalty->is_paid ? 'Paid' : 'Unpaid' }}
                            </x-ui.badge>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-neutral-500 dark:text-neutral-400 text-sm text-center py-8">
            No penalties found for this lease.
        </p>
        @endif
    </x-ui.card>
    <div class="mt-4">
        {{ $this->penalties->links() }}
    </div>
</div>

