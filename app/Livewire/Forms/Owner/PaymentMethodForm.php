<?php

namespace App\Livewire\Forms\Owner;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Livewire\Form;

use function Laravel\Prompts\form;

class PaymentMethodForm extends Form
{
    use WithFileUploads;

    /** @var UploadedFile|null */
    public ?UploadedFile $image = null;
    public ?string $image_path = null;
    public ?int $id = null;
    public string $name = '';
    public string $account_name = '';
    public string $account_number = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'image' => [$this->id ? 'nullable' : 'required', 'image', 'max:5120'], // Max 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The payment method name is required.',
            'account_name.required' => 'The account name is required.',
            'account_number.required' => 'The account number is required.',
            'image.required' => 'The QR code image is required.',
            'image.image' => 'The QR code must be an image file.',
            'image.mimes' => 'The QR code must be a file of type: png, jpg, jpeg.',
            'image.max' => 'The QR code size must not exceed 5MB.',
        ];
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {

            $path = $this->image_path; // keep old one by default

            // Handle QR image upload if provided
            if ($this->image instanceof UploadedFile) {
                if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
                    Storage::disk('public')->delete($this->image_path);
                }
                $path = $this->image->store('assets/payment_methods', 'public');
            }

            PaymentMethod::updateOrCreate(
                ['id' => $this->id],
                [
                    'owner_id' => auth()->id(),
                    'type' => $this->name,
                    'account_name' => $this->account_name,
                    'account_number' => $this->account_number,
                    'qr_image_path' => $path,
                ]
            );

            DB::commit();
            $this->reset();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('name', 'An error occurred while saving the payment method. Please try again.');
            throw $e;

        }
    }

    public function cancel(): void
    {
        // If there's an unsaved uploaded file, delete it manually
        if ($this->image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            try {
                $this->image->delete();
            } catch (\Throwable $e) {
                // Optional: log the error but don’t interrupt the user
                Log::warning('Failed to delete temporary upload: '.$e->getMessage());
            }
        }

        $this->reset();
    }
}
