<div class="space-y-5">
    {{-- === Analytics Cards === --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <x-ui.card hoverless size="full" class="flex flex-col items-center justify-center h-24">
            <h2 class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 truncate min-w-0">₱ {{ number_format($totalIncome, 2) ?? 0.00 }}</h2>
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Total Income
                <x-ui.tooltip>
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This amount represents the total <br/>
                        income generated from all tenants <br/>
                        payments.
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
        </x-ui.card>

        <x-ui.card hoverless size="full" class="flex flex-col items-center justify-center h-24">
            <h2 class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 truncate min-w-0">₱ {{ number_format($totalExpenses, 2) ?? 0.00 }}</h2>
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Total Expenses
                <x-ui.tooltip placement="top">
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This amount represents the total expenses for the all period. <br />
                        It is the summation of all expense entries — including water, <br />
                        electricity, and maintenance.
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
        </x-ui.card>

        <x-ui.card hoverless size="full" class="flex flex-col items-center justify-center h-24">
            <div class="flex items-center space-x-2">
                <h2 class="text-3xl font-bold truncate min-w-0
                    {{ $isRevenueHigher ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    ₱ {{ number_format($totalRevenue, 2) }}
                </h2>

                @if($isRevenueHigher)
                {{-- Arrow Up Icon --}}
                <x-ui.icon variant="bold" name="ps:arrow-up" class="w-6 h-6 !text-emerald-600 dark:!text-emerald-400" />
                @else
                {{-- Arrow Down Icon --}}
                <x-ui.icon variant="bold" name="ps:arrow-down" class="w-6 h-6 !text-rose-600 dark:!text-rose-400" />
                @endif
            </div>
            <div class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                Total Revenue
                <x-ui.tooltip>
                    <x-slot:trigger>
                        <x-ui.icon name="information-circle" class="size-4 !text-neutral-500 dark:!text-neutral-400" />
                    </x-slot:trigger>
                    <x-ui.tooltip.content class="bg-indigo-600 text-white">
                        This amount represents the net revenue for the all period.<br/>
                        It is calculated as: Total Income − Total Expenses.<br/>
                        A positive value indicates profit; a negative value indicates a loss.<br/>
                    </x-ui.tooltip.content>
                </x-ui.tooltip>
            </div>
        </x-ui.card>
    </div>

    {{-- === Chart Section === --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <x-ui.card hoverless size="full">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold dark:text-neutral-200 text-neutral-800">Income & Expenses</h3>
                <x-ui.select class="w-32" placeholder="Select a range.." wire:model.live="filterPeriodIncome">
                    <x-ui.select.option value="monthly">Monthly</x-ui.select.option>
                    <x-ui.select.option value="yearly">Yearly</x-ui.select.option>
                </x-ui.select>
            </div>
            <div class="mt-4" wire:ignore>
                <x-ui.chart.line-chart dispatch_name="update-income-chart" :names="['Income','Expenses']" :categories="$expenseChartData['labels']" :data="array_column($expenseChartData['datasets'], 'data')" />
            </div>
        </x-ui.card>

        <x-ui.card hoverless size="full">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold dark:text-neutral-200 text-neutral-800">Vacancy Rate</h3>
                <x-ui.select class="w-32" placeholder="Select a range.." wire:model.live="filterPeriodVacancy">
                    <x-ui.select.option value="monthly">Monthly</x-ui.select.option>
                    <x-ui.select.option value="yearly">Yearly</x-ui.select.option>
                </x-ui.select>
            </div>
            <div class="mt-4 flex flex-col items-center space-y-2" wire:ignore>
                 <x-ui.chart.pie-chart :labels="$vacancyChart['labels']" :series="$vacancyChart['series']" dispatch_name="update-vacancy-chart" />
                  <p class="text-4xl flex flex-col justify-center items-center font-bold text-neutral-800 dark:text-neutral-200">
                      {{ $totalUnits }}
                      <span class="text-base font-medium text-neutral-500 dark:text-neutral-400">Total Units</span>
                  </p>
            </div>
        </x-ui.card>
    </div>


    {{-- === Recent Tables === --}}
    <div class="grid grid-cols-1 gap-5">
        {{-- Payments Table --}}
        <x-ui.card hoverless size="full">
            <div class="flex items-center justify-between gap-5 mb-4">
                <h3 class="text-lg font-semibold dark:text-neutral-200 text-neutral-800">Recent Payments</h3>
                <a href="#" class="text-primary hover:underline flex items-center gap-2">
                    View More
                    <x-ui.icon name="arrow-right" class="size-5 inline-block !text-primary align-middle" />
                </a>
            </div>
            <div class="overflow-x-auto dark:border-neutral-700 bg-white dark:bg-neutral-900">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                    <thead class="bg-neutral-100 dark:bg-neutral-700">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Tenant</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Method</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($recentPayments as $payment)
                        <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800 transition">
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $payment->payment_date->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $payment->tenant->user->fullName }}</td>
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">₱ {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $payment->payment_method }}</td>
                            <td class="px-4 py-3 flex justify-center">
                                 <x-ui.badge color="{{ $payment->status === 'paid' ? 'emerald' : ( $payment->status === 'pending' ? 'amber' : 'rose' ) }}">
                                    {{ ucfirst(strtolower($payment->status)) }}
                                 </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="#" class="text-primary hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-neutral-700 dark:text-neutral-200">
                                No recent payments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Requests & Complaints Table --}}
        <x-ui.card hoverless size="full">
            <div class="flex items-center justify-between gap-5 mb-4">
                <h3 class="text-lg font-semibold dark:text-neutral-200 text-neutral-800">Recent Requests & Complaints</h3>
                <a href="#" class="text-primary hover:underline flex items-center gap-2">
                    View More
                    <x-ui.icon name="arrow-right" class="size-5 inline-block !text-primary align-middle" />
                </a>
            </div>
            <div class="overflow-x-auto dark:border-neutral-700 bg-white dark:bg-neutral-900">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                    <thead class="bg-neutral-100 dark:bg-neutral-700">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                         @forelse($recentRequests as $request)
                        <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/50 dark:bg-neutral-800 transition">
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $request->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ ucfirst($request->type) }}</td>
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $request->description }}</td>
                            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 flex justify-center">
                                <x-ui.badge color="{{ $request->status === 'completed' ? 'emerald' : ( $request->status === 'in_progress' ? 'amber' : 'rose' ) }}">
                                    {{ ucfirst(strtolower(str_replace('_', ' ', $request->status))) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="#" class="text-primary hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-neutral-700 dark:text-neutral-200">
                                No recent requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
