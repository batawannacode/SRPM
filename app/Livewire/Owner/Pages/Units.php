<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Unit;
use App\Livewire\Concerns\HasToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;

#[Layout('components.layouts.owner', ['title' => 'Units/Rooms'])]
class Units extends Component
{
    use HasToast, WithPagination;
    public int $property_id = 0;
    public string $search = '';
    public array $form = [
        'id' => null,
        'unit_number' => '',
        'status' => '',
    ];
    public bool $isEditing = false;

    public function mount(){
        $this->property_id = auth()->user()->owner->active_property;
    }

    #[Computed]
    public function units(){
        if (!$this->property_id) {
            return collect(); // return empty if no active property
        }

        return Unit::query()
            ->where('property_id', $this->property_id)
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($inner) use ($s) {
                    $inner->where('unit_number', 'like', "%{$s}%")
                        ->orWhere('status', 'like', "%{$s}%");

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

    public function editUnit(int $id)
    {
        $unit = Unit::find($id);

        $this->form['id'] = $unit->id;
        $this->form['unit_number'] = $unit->unit_number;
        $this->form['status'] = $unit->status;

        $this->isEditing = true;
        $this->dispatch('open-modal', id: 'unit-modal');
    }

    public function saveUnit()
    {
        $this->validate(
            [
                'form.unit_number' => ['required', 'string', 'max:100'],
                'form.status' => ['required', 'in:vacant,occupied,maintenance'],
            ],
            [
                'form.unit_number.required' => 'Please enter the unit number.',
                'form.status.required' => 'Please select the unit status.',
                'form.status.in' => 'Invalid status selected.',
            ]
        );

        // Save or update the unit
        Unit::updateOrCreate(
            ['id' => $this->form['id']],
            [
                    'property_id' => $this->property_id,
                    'unit_number' => $this->form['unit_number'],
                    'status' => $this->form['status'],
                ]
        );

        $this->isEditing = false;

        // Toast feedback
        $this->toastSuccess(
            $this->form['id']
                ? 'Unit updated successfully!'
                : 'New unit added successfully!'
        );

        // Reset form and close modal
        $this->reset('form', 'isEditing');
        $this->dispatch('close-modal', id: 'unit-modal');
    }


    public function cancelModal()
    {
        $this->reset('form');
        $this->resetErrorBag();
        $this->resetValidation();
        $this->isEditing = false;
        $this->dispatch('close-modal', id: 'unit-modal');
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
