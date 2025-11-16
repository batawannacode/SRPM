<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Tenant;
use App\Models\Lease;
use App\Models\ExpectedPayment;
use App\Livewire\Concerns\HasToast;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.owner', ['title' => 'Tenant Payments'])]
class ViewTenantPayments extends Component
{
    use WithPagination, HasToast;
    public Tenant $tenant;
    public ?ExpectedPayment $viewingPayment = null;
    public int $selectedLeaseId;

    public function mount()
    {
        $this->selectedLeaseId = $this->activeLease()?->id;
    }

    #[Computed]
    public function leases()
    {
        $propertyId = Auth::user()->owner->active_property;

        return Lease::query()
            ->whereHas('unit', fn($query) => $query->where('property_id', $propertyId))
            ->where('tenant_id', $this->tenant->id)
            ->latest()
            ->get();
    }

    #[Computed]
    public function selectedLease()
    {
        return $this->leases->firstWhere('id', $this->selectedLeaseId)
            ?? $this->activeLease();
    }

    public function activeLease()
    {
        return $this->leases()
            ->first(fn($lease) => $lease->status === 'active')
            ?? $this->leases()->first();
    }

    public function getPaymentsByStatus(Lease $lease, string $status)
    {
        return ExpectedPayment::where('lease_id', $lease->id)
        ->where('status', $status)
        ->get();
    }

    public function approvePayment(ExpectedPayment $payment)
    {
        if ($payment) {
            $payment->status = 'paid';
            $payment->save();
            $this->toastSuccess('Payment approved successfully.');
            // Notify tenant about approval
            Notification::create([
                'user_id' => $payment->lease->tenant->user->id,
                'message' => "Your payment for {$payment->payment_date->format('M d, Y')} has been approved. Thank you for your timely payment.",
                'type' => 'approved_payment',
            ]);
            return;
        }
        $this->toastError('Failed to approve payment.');
    }

    public function rejectPayment(ExpectedPayment $payment)
    {
        if ($payment) {
            $payment->status = 'unpaid';
            $payment->save();

            // Optionally, you might want to delete the associated payment record
            if ($payment->payment) {
                $payment->payment->delete();
            }
            // Notify tenant about rejection
            Notification::create([
                'user_id' => $payment->lease->tenant->user->id,
                'message' => "Your payment for {$payment->payment_date->format('M d, Y')} has been rejected. Please make sure you submit the exact payment and correct details.",
                'type' => 'rejected_payment',
            ]);
            $this->toastSuccess('Payment rejected successfully.');
            return;
        }
        $this->toastError('Failed to reject payment.');
    }

    public function viewReceipt(int $paymentId)
    {
        $this->viewingPayment = ExpectedPayment::with(['payment', 'lease.tenant.user'])
            ->find($paymentId);
        $this->dispatch('open-modal',  id: 'viewing-payment-modal' );
    }

    public function downloadReceiptPdf()
    {
        if (! $this->viewingPayment) return;

        $payment = $this->viewingPayment;

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment->payment,
            'tenant_user' => $payment->lease->tenant->user,
            'lease' => $payment->lease,
        ])->setPaper([0, 0, 226.77, 600], 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "payment-receipt-{$payment->id}.pdf"
        );
    }
}