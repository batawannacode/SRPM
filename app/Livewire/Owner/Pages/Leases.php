<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Lease;
use App\Models\Unit;
use App\Models\Tenant;
use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\Owner\LeaseForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Layout('components.layouts.owner', ['title' => 'Leases'])]
class Leases extends Component
{
    use HasToast, WithPagination, WithFilePond;
    public LeaseForm $form;
    public string $startDate = '';
    public string $endDate = '';
    public string $search = '';

    #[Computed]
    public function propertyLeases()
    {
        return Lease::query()
        ->whereHas('unit', function ($q) {
            $q->where('property_id', auth()->user()->owner->active_property);
        })
        ->when($this->search, function ($q) {
            $search = '%' . trim($this->search) . '%';

            $q->where(function ($sub) use ($search) {
                // Match lease status
                $sub->where('status', 'like', $search)
                    ->orWhere('id', 'like', $search)
                    ->orWhere('rent_price', 'like', $search)
                    ->orWhereRaw("DATE_FORMAT(start_date, '%Y-%m-%d') LIKE ?", [$search])
                    ->orWhereRaw("DATE_FORMAT(end_date, '%Y-%m-%d') LIKE ?", [$search])
                    ->orWhereRaw("MONTHNAME(start_date) LIKE ?", [$search])
                    ->orWhereRaw("MONTHNAME(end_date) LIKE ?", [$search])

                    // Match tenant full name
                    ->orWhereHas('tenant.user', function ($q2) use ($search) {
                        $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$search]);
                    })

                    // Match unit attributes (including date fields)
                    ->orWhereHas('unit', function ($q3) use ($search) {
                        $q3->where('unit_number', 'like', $search);
                    });
            });
        })
        ->when($this->startDate && $this->endDate, function ($q) {
            $start = \Carbon\Carbon::parse($this->startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($this->endDate)->endOfDay();

            // Filter leases by date range
            $q->where(function ($dateQ) use ($start, $end) {
                $dateQ->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end]);
            });
        })
        ->orderByDesc(
            'created_at'
        )
        ->paginate(12);
    }

    #[Computed]
    public function units()
    {
        return Unit::query()
            ->where('property_id', auth()->user()->owner->active_property)
            ->where('status', 'vacant')
            ->orderBy('unit_number', 'asc')
            ->get();
    }

    #[Computed]
    public function tenants()
    {
        return Tenant::query()
            ->select('tenants.*')
            ->join('users', 'users.id', '=', 'tenants.user_id')
            ->whereDoesntHave('leases', function ($query) {
                $query->where('status', 'active'); // adjust 'status' field as needed
            })
            ->orderBy('users.first_name', 'asc')
            ->get();
    }

    public function addLease()
    {
        if($this->form->submit()) {
            $this->toastSuccess('Lease added successfully.');
            $this->cancelModal();
            return;
        }
        $this->toastError('Failed to add lease. Please try again.');
    }

    public function cancelModal()
    {
        $this->form->reset();
        $this->form->resetErrorBag();
        $this->form->resetValidation();
        $this->dispatch('close-modal', id: 'add-lease-modal');
        $this->dispatch('filepond-reset-form.lease_documents');
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
