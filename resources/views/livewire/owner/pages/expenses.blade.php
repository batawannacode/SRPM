<div class="space-y-5">
    {{-- === Header === --}}
    <div class="flex md:justify-between items-center gap-5">
        <div class="max-md:hidden">
            <h1 class="text-2xl font-bold text-neutral-700 dark:text-neutral-200">Expenses</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Track, review and manage all expense records for your properties.</p>
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
        <x-ui.input wire:model.live="search" placeholder="Search..." class="max-w-sm" leftIcon="magnifying-glass" />
        <x-ui.button color="emerald" icon="plus" wire:click="$dispatch('open-modal', { id: 'expense-utility-bill-modal' })">
            Add Utility Bill
        </x-ui.button>
    </div>

    {{-- === Expenses Table === --}}
    <x-ui.card hoverless size="full">
        <div class="overflow-x-auto dark:border-neutral-700 bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-700">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-neutral-600 dark:text-neutral-300">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($this->expenses as $expense)
                    <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/80 dark:bg-neutral-800 transition">
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $expense->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">
                            <x-ui.badge color="{{ $expense->type === 'others' ? '' : ( $expense->type === 'maintenance' ? 'amber' : ($expense->type === 'water' ? 'sky' : 'rose') ) }}">
                                {{ ucfirst(strtolower($expense->type)) }}
                            </x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">₱ {{ number_format($expense->amount, 2) }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200 flex items-center justify-center">
                            <x-ui.button class="!text-emerald-600" size="sm" variant="ghost" wire:click="editUtilityBill({{ $expense->id }})">
                                Edit
                            </x-ui.button>
                             <x-ui.modal.confirm-delete title="Delete Utility Bill" message="Are you sure you want to delete this utility bill? This action cannot be undone." :id="$expense->id" wire:click="deleteUtilityBill({{ $expense->id }})">
                                 <x-slot:trigger>
                                     <x-ui.button class="text-rose-600!" size="sm" variant="ghost">
                                         Delete
                                     </x-ui.button>
                                 </x-slot:trigger>
                             </x-ui.modal.confirm-delete>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-neutral-700 dark:text-neutral-200">
                            No expenses found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div>
        {{ $this->expenses->links() }}
    </div>

    {{-- === Expense / Utility Bill Modal === --}}
    <x-ui.modal id="expense-utility-bill-modal" heading="{{ $isEditing ? 'Edit Utility Bill' : 'Add Utility Bill' }}" :closeByClickingAway="false" :closeButton="false" width="lg">
        <div class="space-y-4">
            <!-- Type Select -->
            <x-ui.select label="Type" triggerClass="!p-3" class="text-sm" wire:model="form.type" placeholder="Select type">
                <x-ui.select.option value="electricity">Electricity</x-ui.select.option>
                <x-ui.select.option value="water">Water</x-ui.select.option>
                <x-ui.select.option value="maintenance">Maintenance</x-ui.select.option>
                <x-ui.select.option value="others">Others</x-ui.select.option>
            </x-ui.select>

            <!-- Amount Input -->
            <x-ui.input label="Amount" type="number" step="0.01" min="0" placeholder="Enter amount" wire:model="form.amount" />
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

            @if (!$isEditing)
            <x-ui.button color="emerald" wire:click="saveUtilityBill">
                Save Utility Bill
            </x-ui.button>
            @else
            <x-ui.button color="emerald" wire:click="updateUtilityBill">
                Update Utility Bill
            </x-ui.button>
            @endif
        </div>
    </x-ui.modal>

</div>
