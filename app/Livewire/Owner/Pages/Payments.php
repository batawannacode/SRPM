<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Tenant;
use App\Models\ExpectedPayment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use App\Livewire\Concerns\HasToast;

#[Layout('components.layouts.owner', ['title' => 'Payments'])]
class Payments extends Component
{
    use HasToast, WithPagination;
    public string $search = '';

    #[Computed]
    public function tenants()
    {
        $activePropertyId = auth()->user()->owner->active_property;
        $search = trim($this->search);

        return Tenant::query()
            ->whereHas('leases.unit', function ($query) use ($activePropertyId) {
                $query->where('property_id', $activePropertyId);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($uq) use ($search) {
                    $uq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            })
            ->orderBy('id')
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