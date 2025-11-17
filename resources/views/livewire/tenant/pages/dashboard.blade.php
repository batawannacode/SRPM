<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-800 dark:text-neutral-200">
            Welcome back, {{ Auth::user()->full_name }} 👋
        </h1>
        <p class="text-sm text-neutral-500">
            Here’s a summary of your lease and payments.
        </p>
    </div>

    {{-- Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Next Payment --}}
        <x-ui.card size="full">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Next Payment
                <x-ui.tooltip>
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This shows the total amount and due date <br />
                        of your upcoming rent payment.
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
            <div class="text-3xl font-bold mt-1 text-amber-400">
                @if($nextPayment)
                ₱{{ number_format($nextPayment->total_amount, 2) }}
                @else
                —
                @endif
            </div>
            <div class="text-xs text-neutral-400 mt-1">
                @if($nextPayment)
                Due {{ \Carbon\Carbon::parse($nextPayment->payment_date)->format('F j, Y') }}
                @endif
            </div>
        </x-ui.card>

        {{-- Total Paid --}}
        <x-ui.card size="full">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Total Paid
                <x-ui.tooltip>
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This is the total amount you’ve successfully <br />
                        paid for this lease.
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
            <div class="text-3xl font-bold mt-1 text-emerald-600">
                ₱{{ number_format($totalPaid, 2) }}
            </div>
        </x-ui.card>

        {{-- Unpaid Balance --}}
        <x-ui.card size="full">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Unpaid Balance
                <x-ui.tooltip>
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This amount shows your remaining unpaid <br />
                        rent balance for this lease.
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
            <div class="text-3xl font-bold mt-1 text-rose-500">
                ₱{{ number_format($totalUnpaid, 2) }}
            </div>
        </x-ui.card>

        {{-- Total Penalties --}}
        <x-ui.card size="full">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Total Penalties
                <x-ui.tooltip>
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This shows the total penalties incurred <br />
                        for late or missed payments.
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
            <div class="text-3xl font-bold mt-1 text-orange-500">
                ₱{{ number_format($totalPenalties, 2) }}
            </div>
        </x-ui.card>

    </div>

    {{-- Lease Info --}}
    <x-ui.card hoverless size="full">
        @if($lease)
        <div class="flex items-start justify-between gap-5">
            <div>
                <div class="text-xl font-semibold dark:text-neutral-100 mt-1">
                    {{ $lease->unit->property->name ?? 'N/A' }} — {{ $lease->unit->unit_number ?? 'N/A' }}
                </div>
                <div class="text-sm text-neutral-500 mt-1">
                    Lease Period: {{ \Carbon\Carbon::parse($lease->start_date)->format('M d, Y') }}
                    to {{ \Carbon\Carbon::parse($lease->end_date)->format('M d, Y') }}
                </div>
            </div>
            <div>
                <x-ui.badge color="emerald">Active</x-ui.badge>
            </div>
        </div>
        @else
        <p class="text-center text-sm text-neutral-500 dark:text-neutral-400 py-6">
            You currently have no active leases.
        </p>
        @endif
    </x-ui.card>

    {{-- Unpaid Balance --}}
    <x-ui.card hoverless size="full">
        @php
        $unpaidPayments = $this->getPaymentsByStatus('unpaid');
        @endphp

        @if ($unpaidPayments->isEmpty())
        <x-ui.empty text="No Unpaid Payments" />
        @else
        <div class="overflow-x-auto mb-5">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead>
                    <tr class="bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-200">
                        <th class="p-4 text-left">Payment Date</th>
                        <th class="p-4 text-left">Rent Price</th>
                        <th class="p-4 text-left">Penalty</th>
                        <th class="p-4 text-left">Total Payment</th>
                        <th class="p-4 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700 text-neutral-600 dark:text-neutral-400">
                    @foreach ($unpaidPayments as $expected)
                    <tr>
                        <td class="p-4">{{ \Carbon\Carbon::parse($expected->payment_date)->format('d M, Y') }}</td>
                        <td class="p-4">₱{{ number_format($expected->lease->rent_price, 2) }}</td>

                        @if ($expected->penalty)
                        <td class="p-4">₱{{ number_format($expected->penalty->amount, 2) }}</td>
                        @else
                        <td class="p-4">(No Penalty)</td>
                        @endif

                        <td class="p-4">
                            @php
                            $totalPayment = $expected->lease->rent_price + ($expected->penalty ? $expected->penalty->amount : 0);
                            @endphp
                            ₱{{ number_format($totalPayment, 2) }}
                        </td>
                        <td class="p-4">
                             <a href="{{ route('tenant.payments') }}" class="text-primary hover:underline flex items-center gap-2">
                                 pay
                                 <x-ui.icon name="arrow-right" class="size-5 inline-block !text-primary align-middle" />
                             </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </x-ui.card>

    {{-- Recent Notifications --}}
    <div class="mt-4 ">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-base font-semibold text-neutral-700 dark:text-neutral-300">Recent Notifications</h2>
        </div>

        <x-ui.card hoverless size="full" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            @forelse($notifications as $note)
            <div class="p-3 text-sm">
                <div class="font-medium text-neutral-800 dark:text-neutral-200">{{ $note->message }}</div>
                <div class="text-xs text-neutral-500 mt-1">
                    {{ $note->created_at->diffForHumans() }}
                </div>
            </div>
            @empty
            <div class="p-4 text-sm text-neutral-500 dark:text-neutral-400 text-center">
                No notifications yet.
            </div>
            @endforelse
        </x-ui.card>
    </div>
</div>

