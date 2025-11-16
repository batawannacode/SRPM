<?php

namespace App\Livewire\Owner\Common\Settings;

use App\Models\PaymentRule;
use App\Livewire\Concerns\HasToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentRules extends Component
{
    use HasToast;

    public array $form = [
        'grace_period_days' => 3,
        'penalty_type' => 'fixed',
        'penalty_value' => null,
        'auto_apply' => false,
        'notify_tenant' => false,
    ];

    public function mount()
    {
        $rule = PaymentRule::where('property_id', Auth::user()->owner->active_property)->first();

        if ($rule) {
            $this->form = $rule->only(array_keys($this->form));
        }
    }

    public function saveRules()
    {
        // Sanitize penalty_value (remove %, trim spaces)
        if (is_string($this->form['penalty_value'])) {
            $this->form['penalty_value'] = preg_replace('/[^0-9.]/', '', $this->form['penalty_value']);
        }

        // Convert to float just in case it’s string
        $this->form['penalty_value'] = (float) $this->form['penalty_value'];

        $this->validate([
            'form.grace_period_days' => 'required|integer|min:3',
            'form.penalty_type' => 'required|in:fixed,percentage',
            'form.penalty_value' => $this->form['penalty_type'] === 'percentage'
                                    ? 'required|numeric|min:1|max:100'
                                    : 'required|numeric|min:1',
        ],
        [
            'form.grace_period_days.required' => 'The grace period field is required.',
            'form.grace_period_days.integer' => 'The grace period must be an integer.',
            'form.grace_period_days.min' => 'The grace period must be at least 3 days.',
            'form.penalty_type.required' => 'The penalty type field is required.',
            'form.penalty_value.required' => 'The penalty value field is required.',
            'form.penalty_value.numeric' => 'The penalty value must be a number.',
            'form.penalty_value.min' => 'The penalty value must be at least 1.',
            'form.penalty_value.max' => 'The penalty value may not be greater than 100.',
        ]);

        PaymentRule::updateOrCreate(
            ['property_id' => Auth::user()->owner->active_property],
            $this->form
        );

        $this->toastSuccess('Payment rules updated successfully.');
    }
}