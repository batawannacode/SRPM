<?php

namespace App\Livewire\Tenant\Auth;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\Tenant\RegisterForm;
use App\Support\Toast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth', ['title' => 'Tenant Register', 'user' => 'Tenant'])]

class Register extends Component
{
    use HasToast;
    public RegisterForm $form;

    public function submit(): void
    {
        $user = $this->form->submit();
        if (! $user) {
            $this->toastError('Registration failed. Please check the form for errors.');
            return;
        }
        Auth::login($user);

        Session::regenerate();

        Toast::success('You have successfully registered!');

        // Redirect to dashboard
        $this->redirect(route('tenant.dashboard', absolute: false), navigate: true);
    }
}