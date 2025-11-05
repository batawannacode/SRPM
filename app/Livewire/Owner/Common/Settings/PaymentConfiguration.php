<?php

namespace App\Livewire\Owner\Common\Settings;

use App\Models\PaymentMethod;
use App\Livewire\Forms\Owner\PaymentMethodForm;
use App\Livewire\Concerns\HasToast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentConfiguration extends Component
{
    use WithFileUploads, HasToast;

    public PaymentMethodForm $form;
    public Collection $paymentMethods;
    public bool $isEditing = false;
    public ?string $editingImagePath = null;

    public function mount(): void
    {
        $this->loadMethods();
    }

    public function loadMethods(): void
    {
        $this->paymentMethods = PaymentMethod::where('owner_id', auth()->user()->owner->id)->get();
    }

    public function saveMethod(): void
    {
        $this->form->save();
        $this->loadMethods();
        $this->cancelModal();
        $this->toastSuccess('Payment method saved successfully.');
    }

    public function editMethod(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);

        $this->form->id = $method->id;
        $this->form->name = $method->type;
        $this->form->account_name = $method->account_name;
        $this->form->account_number = $method->account_number;
        $this->form->image_path = $method->qr_image_path;

        $this->isEditing = true;

        $this->dispatch('open-modal', id: 'payment-method-modal');
    }

    public function deleteMethod(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);
        if ($method->qr_image_path) {
            Storage::disk('public')->delete($method->qr_image_path);
        }
        $method->delete();
        $this->toastSuccess('Payment method deleted successfully.');
        $this->loadMethods();
    }
    public function cancelModal(): void
    {
        $this->form->cancel(); // clean the file + reset
        $this->dispatch('close-modal', id: 'payment-method-modal'); // example modal close event
    }
}
