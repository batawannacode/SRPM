<?php

namespace App\Livewire\Owner\Common\Settings;

use App\Livewire\Forms\Owner\AccountForm;
use App\Livewire\Concerns\HasToast;
use Livewire\Component;
use Livewire\WithFileUploads;

class Account extends Component
{
    use HasToast, WithFileUploads;

    public AccountForm $form;

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->email = $user->email;
        $this->form->first_name = $user->first_name;
        $this->form->last_name = $user->last_name;
        $this->form->phone_number = (string) $user->phone_number;
    }

    public function updateAvatar()
    {
        $this->form->editAvatar();
        $this->toastSuccess('Profile photo updated successfully.');
        return redirect(request()->header('Referer') ?? route('owner.settings'));

    }

    public function updatePersonalInfo()
    {
        if($this->form->editPersonalInfo()) {
            $this->toastSuccess('Personal information updated successfully.');
            return redirect(request()->header('Referer') ?? route('owner.settings'));
        }
        $this->toastError('Failed to update personal information.');
    }

    public function updatePassword()
    {
        if($this->form->editPassword()) {
            $this->toastSuccess('Password updated successfully.');
            return redirect(request()->header('Referer') ?? route('owner.settings'));
        }
        $this->toastError('Failed to update password.');

    }

}
