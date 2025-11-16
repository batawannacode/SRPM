<div class="space-y-6">
    {{-- Header --}}
    <div>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Configure how penalties and reminders apply for late tenant payments for the selected property.
        </p>
    </div>

    {{-- Settings Form --}}
    <x-ui.card hoverless size="full">
        <div class="grid md:grid-cols-2 gap-6">
            <x-ui.field>
                <x-ui.label text="Grace Period (days)" />
                <x-ui.input type="number" wire:model="form.grace_period_days" min="0" placeholder="e.g. 3" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.label text="Penalty Type" />
                <x-ui.select wire:model.live="form.penalty_type" placeholder="Select type" triggerClass="py-[11px] px-3" class="text-sm">
                    <x-ui.select.option value="fixed">Fixed Amount</x-ui.select.option>
                    <x-ui.select.option value="percentage">Percentage of Rent</x-ui.select.option>
                </x-ui.select>
            </x-ui.field>

            @if($this->form['penalty_type'] === 'percentage')
            <x-ui.field>
                <x-ui.label text="Penalty Value (Percentage)" />
                <x-ui.description class="text-xs text-neutral-500 mt-1">Enter a value between 1% and 100%.</x-ui.description>
                <x-ui.input type="text" wire:model.lazy="form.penalty_value" x-mask="999%" placeholder="e.g. 10%" />
            </x-ui.field>
            @else
            <x-ui.field>
                <x-ui.label text="Penalty Value (₱)" />
                <x-ui.input type="number" wire:model="form.penalty_value" step="0.01" placeholder="e.g. 500" min="1" />
            </x-ui.field>
            @endif

            <div class="block space-y-5">
                <div class="flex items-center gap-3">
                    <x-ui.checkbox wire:model="form.auto_apply" />
                    <x-ui.label text="Automatically apply penalties after grace period" />
                </div>

                <div class="flex items-center gap-3">
                    <x-ui.checkbox wire:model="form.notify_tenant" />
                    <x-ui.label text="Notify tenants before and after due date" />
                </div>
            </div>
        </div>
        <x-ui.error-list class="mt-4" />
        <div class="mt-6 flex justify-end">
            <x-ui.button color="primary" wire:click="saveRules">
                Save Rules
            </x-ui.button>
        </div>
    </x-ui.card>

   {{-- Preview / Example --}}
   <x-ui.card hoverless size="full">
       <h3 class="font-semibold mb-3 text-neutral-700 dark:text-neutral-200">Example Scenario</h3>
       <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
           If a payment is due on <strong>November 5</strong> and your grace period is
           <strong>{{ $form['grace_period_days'] ?? 3 }}</strong> days, the penalty will start on
           <strong>November {{ 5 + ($form['grace_period_days'] ?? 3) }}</strong>.
           <br><br>
           A penalty of
           <strong>
               @if ($form['penalty_type'] === 'percentage')
               {{ $form['penalty_value'] ?? 2 }}% of monthly rent
               @else
               ₱{{ number_format($form['penalty_value'] ?? 500, 2) }}
               @endif
           </strong> will apply for each late period.
       </p>

       <div class="border-t border-neutral-200 dark:border-neutral-700 pt-3 text-sm text-neutral-600 dark:text-neutral-400 space-y-2">
           <p class="font-semibold text-neutral-700 dark:text-neutral-200">Penalty Type Explanation:</p>

           <ul class="list-disc pl-5 space-y-1">
               <li>
                   <strong>Fixed:</strong> A constant penalty amount applied regardless of rent value.
                   <br>
                   <span class="text-neutral-500 dark:text-neutral-400">Example: ₱500 penalty per late payment.</span>
               </li>
               <li>
                   <strong>Percentage:</strong> A variable penalty calculated as a percentage of the monthly rent.
                   <br>
                   <span class="text-neutral-500 dark:text-neutral-400">Example: 2% of ₱10,000 rent = ₱200 penalty.</span>
               </li>
           </ul>
       </div>
   </x-ui.card>
</div>

