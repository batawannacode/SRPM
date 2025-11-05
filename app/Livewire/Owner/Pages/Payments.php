<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use App\Livewire\Concerns\HasToast;

#[Layout('components.layouts.owner', ['title' => 'Payments'])]
class Payments extends Component
{
    use HasToast, WithPagination;

    #[Computed]
    public function payments()
    {
        // Step 1: Get latest payment ID for each tenant using subquery (no DB::raw)
        $latestPayments = Payment::selectRaw('MAX(id) as id')
            ->groupBy('tenant_id');

        // Step 2: Fetch full payment records for those latest IDs
        return Payment::whereIn('id', $latestPayments)
            ->orderByDesc('payment_date')
            ->paginate(12);
    }
}
