<?php

namespace App\Livewire\Owner\Pages;

use App\Livewire\Concerns\HasToast;
use App\Models\Unit;
use App\Models\Expense;
use App\Models\Payment;
use App\Support\Toast;
use App\Models\Property as PropertyModel;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.owner', ['title' => 'Properties'])]
class Properties extends Component
{
    use WithPagination, HasToast;

     public $form = [
        'property_id' => null,
        'type' => '',
        'amount' => 0,
        'description' => '',
        'property_name' => '',
        'property_address' => '',
        'total_units' => 0,
    ];

    #[Computed]
    public function properties()
    {
        return $this->getProperties()->map(function ($property) {
            $income = $this->getIncome($property->id);
            $expenses = $this->getExpenses($property);
            $netIncome = $income - $expenses;
            $vacancy = $this->getVacancyChart($property);

            return [
                'id' => $property->id,
                'name' => $property->name,
                'address' => $property->address,
                'income' => $income,
                'expenses' => $expenses,
                'netIncome' => $netIncome,
                'isNetIncomeHigher' => $netIncome > 0,
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

    public function saveProperty()
    {
        $this->validate([
            'form.property_name' => 'required|string|max:255',
            'form.property_address' => 'required|string|max:255',
            'form.total_units' => 'required|integer|min:1',
        ],
        [
            'form.property_name.required' => 'Please enter the property name.',
            'form.property_address.required' => 'Please enter the property address.',
            'form.total_units.required' => 'Please enter the total number of units.',
            'form.total_units.integer' => 'The total units must be a valid number.',
            'form.total_units.min' => 'The total units must be at least 1.',
        ]);
        // Create Property
        $property = PropertyModel::create([
            'owner_id' => auth()->user()->owner->id,
            'name' => $this->form['property_name'],
            'address' => $this->form['property_address'],
            'total_units' => $this->form['total_units'],
        ]);
        // Create Units
        collect(range(1, $this->form['total_units']))
                ->each(fn($i) => Unit::create([
                    'property_id' => $property->id,
                    'unit_number' => "Unit {$i}",
                ]));

        // Optional toast notification
        $this->toastSuccess('Property added successfully!');

        // Reset and close modal
        $this->reset('form');
        $this->dispatch('close-modal', id: 'new-property-modal');
    }

    public function saveUtilityBill()
    {
        $this->validate([
            'form.property_id' => 'required|exists:properties,id',
            'form.type' => 'required|string|max:50',
            'form.amount' => 'required|numeric|min:1',
            'form.description' => $this->form['type'] === 'others' || $this->form['type'] === 'maintenance' ? 'required|string|max:255' : 'nullable|string|max:255',
        ],
        [
            'form.property_id.required' => 'Please select a property.',
            'form.property_id.exists' => 'The selected property is invalid.',
            'form.type.required' => 'Please select a type.',
            'form.amount.required' => 'Please enter an amount.',
            'form.amount.numeric' => 'The amount must be a number.',
            'form.amount.min' => 'The amount must be at least 1.',
            'form.description.required' => 'Please enter a description.',
            'form.description.string' => 'The description must be a valid string.',
            'form.description.max' => 'The description may not be greater than 255 characters.',
        ]);

        Expense::create([
            'property_id' => $this->form['property_id'],
            'type' => $this->form['type'],
            'description' => $this->form['description'] ?? null,
            'amount' => $this->form['amount'],
        ]);

        // Optional toast notification
        $this->toastSuccess('Utility bill added successfully!');

        // Reset and close modal
        $this->reset('form');
        $this->dispatch('close-modal', id: 'utility-bill-modal');
    }

    public function editProperty(PropertyModel $property)
    {
        $this->form['property_id'] = $property->id;
        $this->form['property_name'] = $property->name;
        $this->form['property_address'] = $property->address;

        $this->dispatch('open-modal', id: 'edit-property-modal');
    }

    public function updateProperty()
    {
        $this->validate([
            'form.property_name' => 'required|string|max:255',
            'form.property_address' => 'required|string|max:255',
        ],
        [
            'form.property_name.required' => 'Please enter the property name.',
            'form.property_address.required' => 'Please enter the property address.',
        ]);

        $property = PropertyModel::findOrFail($this->form['property_id']);
        $property->update([
            'name' => $this->form['property_name'],
            'address' => $this->form['property_address'],
        ]);

        // Optional toast notification
        $this->toastSuccess('Property updated successfully!');

        // Reset and close modal
        $this->reset('form');
        $this->dispatch('close-modal', id: 'edit-property-modal');
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
            ->paginate(12);
    }

    private function getIncome($propertyId): float
    {
        return Payment::query()
            ->whereHas('expectedPayment', function ($query) use ($propertyId) {
                $query->where('status', 'paid')
                    ->whereHas('lease.unit', fn($q) => $q->where('property_id', $propertyId));
            })
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
        return Payment::query()
            ->whereHas('expectedPayment', function ($query) use ($propertyId) {
                $query->where('status', 'pending')
                    ->whereHas('lease.unit', fn($q) => $q->where('property_id', $propertyId));
            })
            ->sum('amount');
    }

    public function cancelModal()
    {
        $this->reset();
        $this->resetValidation();
        $this->resetErrorBag();
        $this->dispatch('close-modal',  id: 'edit-property-modal');
        $this->dispatch('close-modal', id: 'utility-bill-modal');
        $this->dispatch('close-modal', id: 'new-property-modal');
    }

    public function updating(string $property): void
    {
        $shouldResetPage = in_array(
            needle: $property,
            haystack: [
                'search'
            ],
            strict: true,
        );

        if ($shouldResetPage) {
            $this->resetPage(); // Reset pagination to the first page
        }
    }
}