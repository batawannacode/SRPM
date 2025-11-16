<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Lease;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Penalty;
use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\Owner\LeaseForm;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;
use ZipArchive;

#[Layout('components.layouts.owner', ['title' => 'Lease Details'])]
class ViewLeaseDetails extends Component
{
    use HasToast, WithPagination, WithFilePond;
    public LeaseForm $form;

    protected $paginationTheme = 'tailwind';

    public Lease $lease;

    public function mount()
    {
        $this->loadForms();
    }

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

    #[Computed]
    public function dueSoonCount()
    {
        $today = now();

        return $this->lease->expectedPayments
            ->filter(fn ($expected) =>
                $expected->status !== 'paid' &&
                $today->diffInDays(Carbon::parse($expected->payment_date), false) <= 5
            )
            ->count();
    }


    public function editLease()
    {
        $this->loadForms();
        $this->dispatch('open-modal', id: 'update-lease-modal');
    }

    public function loadForms()
    {
        $this->form->tenant =  '[' . $this->lease->tenant->user->full_name . ',' . $this->lease->tenant->id . ']';
        $this->form->unit = '[' . $this->lease->unit->unit_number . ',' . $this->lease->unit->id . ']';
        $this->form->start_date = $this->lease->start_date?->format('Y-m-d');
        $this->form->end_date = $this->lease->end_date?->format('Y-m-d');
        $this->form->rent_price = $this->lease->rent_price;
        $this->form->lease_documents = collect($this->lease->documents)
            ->map(fn ($doc) => Storage::url($doc->file_path)) // convert to public URL
            ->toArray();
    }

    public function updateLease()
    {
        if ($this->form->update($this->lease)) {
            $this->toastSuccess('Lease updated successfully.');
            $this->cancelModal();
            return;
        }
        $this->toastError('Failed to update lease. Please try again.');
    }

    public function notifyTenant()
    {
        $cacheKey = "lease-notify-tenant-{$this->lease->id}";

        // Check if the lease was already notified today
        if (Cache::has($cacheKey)) {
            $this->toastInfo('You have already notified the tenant today. Please try again after midnight.');
            return;
        }

        if (! $this->form->notifyAllDuePayments($this->lease)) {
            $this->toastError('Failed to notify tenant. Please try again.');
            return;
        }
        $this->toastSuccess('Tenant has been notified for payment.');
        // Set cache key to expire at midnight (resets daily)
        $midnight = Carbon::tomorrow()->startOfDay();
        Cache::put($cacheKey, true, $midnight);
    }

    public function terminateLease(Lease $lease)
    {
        $lease->terminate();
        $this->dispatch('close-modal', id: 'confirm-delete-'.$lease->id);
        $this->toastSuccess('Lease has been terminated successfully.');
        return redirect(request()->header('Referer') ?? route('owner.leases'));
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
