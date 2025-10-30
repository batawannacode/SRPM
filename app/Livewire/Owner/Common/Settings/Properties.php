<?php

namespace App\Livewire\Owner\Common\Settings;

use App\Livewire\Concerns\HasToast;
use App\Models\Expense;
use App\Models\Payment;
use App\Support\Toast;
use App\Models\Property as PropertyModel;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;


class Properties extends Component
{
    use WithPagination, HasToast;

     public $form = [
        'property_id' => null,
        'type' => '',
        'amount' => '',
    ];

    #[Computed]
    public function properties()
    {
        return $this->getProperties()->map(function ($property) {
            $income = $this->getIncome($property->id);
            $expenses = $this->getExpenses($property);
            $revenue = $income - $expenses;
            $vacancy = $this->getVacancyChart($property);

            return [
                'id' => $property->id,
                'name' => $property->name,
                'income' => $income,
                'expenses' => $expenses,
                'revenue' => $revenue,
                'isRevenueHigher' => $income > $expenses,
                'units' => $property->units->count(),
                'vacancy' => $vacancy,
                'due' => $this->getTotalDuePayments($property->id),
            ];
        });
    }

    public function getAllPropertiesProperty()
    {
        // List for select dropdown
        $owner = auth()->user()->owner;
        return PropertyModel::where('owner_id', $owner->id)->get();
    }

    public function saveUtilityBill()
    {
        $this->validate([
            'form.property_id' => 'required|exists:properties,id',
            'form.type' => 'required|string|max:50',
            'form.amount' => 'required|numeric|min:0',
        ],
        [
            'form.property_id.required' => 'Please select a property.',
            'form.property_id.exists' => 'The selected property is invalid.',
            'form.type.required' => 'Please select a type.',
            'form.amount.required' => 'Please enter an amount.',
            'form.amount.numeric' => 'The amount must be a number.',
            'form.amount.min' => 'The amount must be at least 1.',
        ]);

        Expense::create([
            'property_id' => $this->form['property_id'],
            'type' => $this->form['type'],
            'amount' => $this->form['amount'],
        ]);

        // Optional toast notification
        $this->toastSuccess('Utility bill added successfully!');

        // Reset and close modal
        $this->reset('form');
        $this->dispatch('close-modal', id: 'utility-bill-modal');
    }

    public function changeActiveProperty(int $propertyId): void
    {
        $owner = auth()->user()->owner;
        $owner->update(['active_property' => $propertyId]);
        Toast::success('You have successfully switched properties!');

        $this->redirectIntended(
            default: route('owner.dashboard', absolute: false),
            navigate: true
        );
    }

    private function getProperties()
    {
        $owner = auth()->user()->owner;

        // Eager load relationships to reduce queries
        return PropertyModel::with(['units', 'expenses'])
            ->where('owner_id', $owner->id)
            ->get();
    }

    private function getIncome($propertyId): float
    {
        return Payment::whereHas('lease.unit', fn($q) => $q->where('property_id', $propertyId))
            ->where('status', 'paid')
            ->sum('amount');
    }

    private function getExpenses($property): float
    {
        return $property->expenses->sum('amount') ?? 0;
    }

    private function getVacancyChart($property): array
    {
        $occupied = $property->units->where('status', 'occupied')->count();
        $maintenance = $property->units->where('status', 'maintenance')->count();
        $vacant = $property->units->where('status', 'vacant')->count();

        return [
            'occupied' => $occupied,
            'maintenance' => $maintenance,
            'vacant' => $vacant,
        ];
    }

    private function getTotalDuePayments($propertyId): float
    {
        return Payment::whereHas('lease.unit', fn($q) => $q->where('property_id', $propertyId))
            ->where('status', 'pending')
            ->sum('amount');
    }

    private function cancelModal()
    {
        $this->reset('form');
        $this->dispatch('close-modal', id: 'utility-bill-modal');
    }
}