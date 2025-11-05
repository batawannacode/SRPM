<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Expense;
use App\Models\Property;
use App\Livewire\Concerns\HasToast;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.owner', ['title' => 'Expenses'])]
class Expenses extends Component
{
    use HasToast, WithPagination;

    public string $startDate = '';
    public string $endDate = '';
    public string $search = '';
    public array $form = [
        'id' => 0,
        'type' => '',
        'amount' => '',
    ];
    public bool $isEditing = false;

    public function mount(): void
    {
        $this->startDate = \Carbon\Carbon::now()->startOfYear()->toDateString(); // Jan 01 of this year
        $this->endDate = \Carbon\Carbon::now()->endOfYear()->toDateString();     // Dec 31 of this year
    }

    #[Computed]
    public function expenses()
    {
        $owner = auth()->user()->owner;
        $propertyId = $owner->active_property;

        if (!$propertyId) {
            return collect(); // return empty if no active property
        }

        // Optional date filters
        $start = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->startOfDay() : null;
        $end = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->endOfDay() : null;

        return Expense::query()
            ->where('property_id', $propertyId)
            ->when($start && $end, fn($q) =>
                $q->whereBetween('created_at', [$start, $end])
            )
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($inner) use ($s) {
                    $inner->where('type', 'like', "%{$s}%")
                        ->orWhere('amount', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%");

                    if ($s === '') return;

                    // Try parsing as a date
                    try {
                        $date = \Carbon\Carbon::parse($s);
                        $inner->orWhereDate('created_at', $date->toDateString())
                            ->orWhereMonth('created_at', $date->month);
                    } catch (\Exception $e) {
                        // Handle numeric months or month names (e.g., 10, Oct, October)
                        if (preg_match('/^\d{1,2}$/', $s) && (int)$s >= 1 && (int)$s <= 12) {
                            $inner->orWhereMonth('created_at', (int)$s);
                        } else {
                            $lower = strtolower($s);
                            $inner->orWhereRaw("LOWER(MONTHNAME(created_at)) LIKE ?", ["%{$lower}%"]);
                        }

                        // Generic fallback for YYYY-MM-DD
                        $inner->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$s}%"]);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function editUtilityBill(int $id)
    {
        $owner = auth()->user()->owner;

        $expense = Expense::find($id);
        if (!$expense || $expense->property_id !== $owner->active_property) {
            $this->toastError('Expense not found or access denied.');
            return;
        }
        $this->form['id'] = $expense->id;
        $this->form['type'] = $expense->type;
        $this->form['amount'] = $expense->amount;
        $this->form['description'] = $expense->description ?? '';
        $this->isEditing = true;
        $this->dispatch('open-modal', id: 'expense-utility-bill-modal');
    }

    public function deleteUtilityBill(int $id)
    {
        $owner = auth()->user()->owner;

        $expense = Expense::find($id);
        if (!$expense || $expense->property_id !== $owner->active_property) {
            $this->toastError('Expense not found or access denied.');
            return;
        }

        $expense->delete();

        $this->toastSuccess('Utility bill deleted successfully!');
    }

    public function cancelModal()
    {
        $this->reset();
        $this->resetErrorBag();
        $this->resetValidation();
        $this->isEditing = false;
        $this->dispatch('close-modal', id: 'expense-utility-bill-modal');
    }

    public function saveUtilityBill()
    {
        $this->validate([
            'form.type' => 'required|string|max:50',
            'form.amount' => 'required|numeric|min:1',
            'form.description' => $this->form['type'] === 'others' || $this->form['type'] === 'maintenance' ? 'required|string|max:255' : 'nullable|string|max:255',
        ],
        [
            'form.type.required' => 'Please select a type.',
            'form.amount.required' => 'Please enter an amount.',
            'form.amount.numeric' => 'The amount must be a number.',
            'form.amount.min' => 'The amount must be at least 1.',
            'form.description.required' => 'Please enter a description.',
            'form.description.string' => 'The description must be a valid string.',
            'form.description.max' => 'The description may not be greater than 255 characters.',
        ]);

        $owner = auth()->user()->owner;

        Expense::create([
            'property_id' => $owner->active_property,
            'type' => $this->form['type'],
            'amount' => $this->form['amount'],
            'description' => $this->form['description'] ?? null,
        ]);

        $this->isEditing = false;

        // Optional toast notification
        $this->toastSuccess('Utility bill added successfully!');

        // Reset and close modal
        $this->reset('form');
        $this->dispatch('close-modal', id: 'expense-utility-bill-modal');
    }

    public function updateUtilityBill()
    {
        $this->validate([
            'form.type' => 'required|string|max:50',
            'form.amount' => 'required|numeric|min:0',
            'form.description' => $this->form['type'] === 'others' || $this->form['type'] === 'maintenance' ? 'required|string|max:255' : 'nullable|string|max:255',
        ],
        [
            'form.type.required' => 'Please select a type.',
            'form.amount.required' => 'Please enter an amount.',
            'form.amount.numeric' => 'The amount must be a number.',
            'form.amount.min' => 'The amount must be at least 1.',
            'form.description.required' => 'Please enter a description.',
            'form.description.string' => 'The description must be a valid string.',
            'form.description.max' => 'The description may not be greater than 255 characters.',
        ]);

        $owner = auth()->user()->owner;

        $expense = Expense::find($this->form['id']);
        if (!$expense || $expense->property_id !== $owner->active_property) {
            $this->toastError('Expense not found or access denied.');
            return;
        }

        $expense->update([
            'type' => $this->form['type'],
            'amount' => $this->form['amount'],
            'description' => $this->form['description'] ?? null,
        ]);

        $this->isEditing = false;

        $this->toastSuccess('Utility bill updated successfully!');
        $this->reset('form');
        $this->dispatch('close-modal', id: 'expense-utility-bill-modal');
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
