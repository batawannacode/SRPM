<div class="space-y-6">
    {{-- Breadcrumbs --}}
    <x-ui.breadcrumbs class="mb-10">
        <x-ui.breadcrumbs.item class="hover:underline hover:text-primary" href="{{ route('owner.payments') }}" separator="slash">
            Payments
        </x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Payment Detail</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    {{-- Lease Selector --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-neutral-800 dark:text-neutral-50">
            Lease #{{ $this->selectedLease?->id }}
        </h2>

        <div>
            <select wire:model.live="selectedLeaseId" class="border border-neutral-200 dark:border-neutral-700 rounded-lg py-2.5 bg-white dark:bg-neutral-800 dark:text-white text-sm w-full max-w-lg">
                @foreach ($this->leases as $lease)
                <option value="{{ $lease->id }}">
                    Lease #{{ $lease->id }} - {{ ucfirst($lease->status) }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Payments Tabs --}}
    <x-ui.card hoverless size="full">
        <x-ui.tabs class="space-y-6">
            <x-ui.tab.group class="justify-start" wire:ignore>
                <x-ui.tab label="Pending" name="pending" />
                <x-ui.tab label="Paid" name="paid" />
                <x-ui.tab label="Unpaid" name="unpaid" />
            </x-ui.tab.group>

            {{-- Pending Payments --}}
            <x-ui.tab.panel name="pending">
                @php
                $pendingPayments = $this->getPaymentsByStatus($this->selectedLease, 'pending');
                @endphp

                @if ($pendingPayments->isEmpty())
                <x-ui.empty text="No Pending Payments" />
                @else
                <div class="overflow-x-auto mb-5">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                        <thead>
                            <tr class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
                                <th class="p-4 text-left">Payment Date</th>
                                <th class="p-4 text-left">Date Created</th>
                                <th class="p-4 text-left">Penalty</th>
                                <th class="p-4 text-left">Total Amount Due</th>
                                <th class="p-4 text-left">Amount Paid</th>
                                <th class="p-4 text-left">Method</th>
                                <th class="p-4 text-left">Reference</th>
                                <th class="p-4 text-left">Account</th>
                                <th class="p-4 text-left">Proof</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                            @foreach ($pendingPayments as $expected)
                            @php $payment = $expected->payment; @endphp
                            <tr>
                                <td class="p-4">{{ \Carbon\Carbon::parse($expected->payment_date)->format('d M, Y') }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y h:i A') }}</td>
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
                                <td class="p-4">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="p-4">{{ $payment->payment_method }}</td>
                                <td class="p-4">{{ $payment->reference_number }}</td>
                                <td class="p-4">{{ $payment->account_name }}</td>
                                <td class="p-4">
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach ($payment->proof as $img)
                                        @php
                                            $encrypted = Crypt::encryptString($img);
                                            $url = URL::temporarySignedRoute(
                                                'owner.file.preview',
                                                now()->addMinutes(5),
                                                ['encrypted' => base64_encode($encrypted)]
                                            );
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" class="w-14 h-14 hover:ring-primary hover:ring-2 rounded transition duration-200">
                                            <img src="{{ asset('storage/'.$img) }}" class="w-14 h-14 object-cover rounded border" />
                                        </a>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center gap-3 justify-center">
                                        <x-ui.button size="sm" type="submit" wire:click="approvePayment({{ $expected->id }})">Approve</x-ui.button>
                                        <x-ui.button size="sm" type="submit" color="rose" wire:click="rejectPayment({{ $expected->id }})">Reject</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-ui.tab.panel>

            {{-- Paid Payments --}}
            <x-ui.tab.panel name="paid">
                @php
                $paidPayments = $this->getPaymentsByStatus($this->selectedLease, 'paid');
                @endphp

                @if ($paidPayments->isEmpty())
                <x-ui.empty text="No Paid Payments" />
                @else
                <div class="overflow-x-auto mb-5">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                        <thead>
                            <tr class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
                                <th class="p-4 text-left">Payment Date</th>
                                <th class="p-4 text-left">Date Created</th>
                                <th class="p-4 text-left">Penalty</th>
                                <th class="p-4 text-left">Total Amount Due</th>
                                <th class="p-4 text-left">Amount Paid</th>
                                <th class="p-4 text-left">Method</th>
                                <th class="p-4 text-left">Reference</th>
                                <th class="p-4 text-left">Account</th>
                                <th class="p-4 text-left">Proof</th>
                                <th class="p-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                            @foreach ($paidPayments as $expected)
                            @php $payment = $expected->payment; @endphp
                            <tr>

                                <td class="p-4">{{ \Carbon\Carbon::parse($expected->payment_date)->format('d M, Y') }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y h:i A') }}</td>
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
                                <td class="p-4">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="p-4">{{ $payment->payment_method }}</td>
                                <td class="p-4">{{ $payment->reference_number }}</td>
                                <td class="p-4">{{ $payment->account_name }}</td>
                                <td class="p-4">
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach ($payment->proof as $img)
                                         @php
                                            $encrypted = Crypt::encryptString($img);
                                            $url = URL::temporarySignedRoute(
                                                'owner.file.preview',
                                                now()->addMinutes(5),
                                                ['encrypted' => base64_encode($encrypted)]
                                            );
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" class="w-14 h-14 hover:ring-primary hover:ring-2 rounded transition duration-200">
                                            <img src="{{ asset('storage/'.$img) }}" class="w-14 h-14 object-cover rounded border" />
                                        </a>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4">
                                    <x-ui.button type="submit" size="sm" color="amber" wire:click="viewReceipt({{ $expected->id }})">View Receipt</x-ui.button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-ui.tab.panel>

            {{-- Unpaid Payments --}}
            <x-ui.tab.panel name="unpaid">
                @php
                $unpaidPayments = $this->getPaymentsByStatus($this->selectedLease, 'unpaid');
                @endphp

                @if ($unpaidPayments->isEmpty())
                <x-ui.empty text="No Unpaid Payments" />
                @else
                <div class="overflow-x-auto mb-5">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                        <thead>
                            <tr class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
                                <th class="p-4 text-left">Payment Date</th>
                                <th class="p-4 text-left">Rent Price</th>
                                <th class="p-4 text-left">Penalty</th>
                                <th class="p-4 text-left">Total Payment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                            @foreach ($unpaidPayments as $expected)
                            <tr>
                                <td class="p-4">{{ \Carbon\Carbon::parse($expected->payment_date)->format('d M, Y') }}</td>
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
                                <td class="p-4">₱{{ number_format($expected->lease->rent_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-ui.tab.panel>
        </x-ui.tabs>
    </x-ui.card>

    {{-- View Receipt Modal --}}
    <x-ui.modal id="viewing-payment-modal" width="lg" heading="Payment Receipt #{{ $viewingPayment?->payment?->id }}">
        @if ($viewingPayment)
        @php $payment = $viewingPayment->payment; @endphp
        <div class="space-y-3 text-sm text-neutral-700 dark:text-neutral-300">
            <div class="flex justify-between">
                <span>Date:</span>
                <span>{{ \Carbon\Carbon::parse($payment->created_at)->format('F j, Y h:i A') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tenant:</span>
                <span>{{ $viewingPayment->lease->tenant->user->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Amount Paid:</span>
                <span class="font-semibold">₱{{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Method:</span>
                <span>{{ $payment->payment_method }}</span>
            </div>
            <div class="flex justify-between">
                <span>Reference:</span>
                <span>{{ $payment->reference_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Account Name:</span>
                <span>{{ $payment->account_name }}</span>
            </div>

            <div>
                <span class="block mb-1">Proof of Payment:</span>
                <div class="flex gap-2 flex-wrap">
                    @foreach ($payment->proof as $img)
                    <img src="{{ asset('storage/'.$img) }}" class="w-full h-auto object-contain max-h-[600px] rounded border" />
                    @endforeach
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex items-center gap-5 justify-between w-full">
                <x-ui.button wire:click="$dispatch('close-modal',  { id: 'viewing-payment-modal' })" class="w-full" variant="ghost">
                    Close
                </x-ui.button>
                <x-ui.button wire:click="downloadReceiptPdf" type="submit" class="w-full" color="primary">
                    Download Receipt
                </x-ui.button>
            </div>
        </x-slot:footer>
        @endif
    </x-ui.modal>
</div>

