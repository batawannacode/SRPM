<?php

namespace App\Livewire\Tenant\Pages;

use App\Models\Lease;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Penalty;
use App\Livewire\Concerns\HasToast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;
use ZipArchive;

#[Layout('components.layouts.tenant', ['title' => 'Lease Details'])]
class ViewLeaseDetails extends Component
{
    use HasToast, WithPagination, WithFilePond;
    protected $paginationTheme = 'tailwind';

    public Lease $lease;

    #[Computed]
    public function expectedPayments()
    {
        return $this->lease->expectedPayments()
            ->orderByRaw("
                CASE
                    WHEN status = 'unpaid' AND payment_date < NOW() THEN 0  -- 🔴 overdue unpaid first
                    WHEN status = 'unpaid' AND payment_date >= NOW() THEN 1 -- 🟡 upcoming unpaid next
                    ELSE 2                                                  -- 🟢 paid last
                END
            ")
            ->orderBy('payment_date', 'asc') // older first within each group
            ->paginate(12, ['*'], 'expectedPage');
    }

    #[Computed]
    public function units()
    {
        return Unit::query()
            ->where('property_id', auth()->user()->owner->active_property)
            ->where(function ($query) {
            $query->where('status', 'vacant')
                    ->orWhere('id', $this->lease->unit_id); // include current lease unit
            })
            ->orderBy('unit_number', 'asc')
            ->get();
    }

    #[Computed]
    public function tenants()
    {
        return Tenant::query()
            ->select('tenants.*')
            ->join('users', 'users.id', '=', 'tenants.user_id')
            ->where(function ($query) {
                // tenants with no active lease
                $query->whereDoesntHave('leases', function ($subQuery) {
                    $subQuery->where('status', 'active');
                })
                // or the tenant in the current lease
                ->orWhere('tenants.id', $this->lease->tenant_id);
            })
            ->orderBy('users.first_name', 'asc')
            ->get();
    }

    #[Computed]
    public function penalties()
    {
        return Penalty::query()
            ->whereHas('expectedPayment', function ($query) {
                $query->where('lease_id', $this->lease->id);
            })
            ->orderByDesc('due_date')
            ->paginate(12, ['*'], 'penaltyPage');
    }

   public function getPaymentStatusColor($expectedPayment)
    {
        if ($expectedPayment->status === 'paid') {
            return 'text-emerald-600 dark:text-emerald-400';
        }

        $today = now();
        $paymentDate = Carbon::parse($expectedPayment->payment_date);
        $daysLeft = $today->diffInDays($paymentDate, false);

        if ($daysLeft < 0) {
            return 'bg-rose-50 dark:bg-rose-800/50 border-2 border-rose-400 hover:!bg-rose-50 dark:hover:!bg-rose-800/50 dark:!bg-rose-800/50 dark:!border-none';
        } elseif ($daysLeft <= 5) {
            return 'bg-amber-50 dark:bg-amber-400/40 border-2 border-amber-400 hover:!bg-amber-50 dark:hover:!bg-amber-400/40 dark:!bg-amber-400/40 dark:!border-none';
        }

        return '';
    }

    /**
     * Download all lease documents as a single ZIP file
     */
    public function downloadAllDocuments(Lease $lease)
    {
        $documents = $lease->documents;

        if ($documents->isEmpty()) {
            $this->toastError('No documents found for this lease.');
            return;
        }

        $zip = new ZipArchive;
        $zipFileName = 'lease_' . $lease->id . '_documents.zip';
        $zipFilePath = storage_path('app/public/downloads/owner/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($documents as $document) {
                $filePath = storage_path('app/public/' . $document->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($document->file_name));
                }
            }
            $zip->close();
        } else {
            $this->toastError('Unable to create ZIP file.');
            return;
        }

        // Dispatch browser event to trigger file download
        $this->dispatch('download-zip', url: Storage::url('downloads/owner/'. $zipFileName));

        $this->toastSuccess('All documents have been zipped successfully. It is being downloaded.');
    }

    public function cancelModal()
    {
        $this->form->resetErrorBag();
        $this->form->resetValidation();
        $this->dispatch('close-modal', id: 'update-lease-modal');
    }
}