<?php

namespace App\Livewire\Tenant\Pages;

use App\Models\Lease;
use App\Models\Unit;
use App\Models\Tenant;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;

#[Layout('components.layouts.tenant', ['title' => 'My leases'])]

class Leases extends Component
{
    use WithPagination;

    public string $startDate = '';
    public string $endDate = '';
    public string $search = '';

    #[Computed]
    public function propertyLeases()
    {
        return Lease::query()
        ->where('tenant_id', auth()->user()->tenant->id)
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
