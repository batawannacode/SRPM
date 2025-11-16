<?php

namespace App\Livewire\Tenant\Auth;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\Tenant\LoginForm;
use App\Support\Toast;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth', ['title' => 'Tenant Login', 'user' => 'Tenant'])]
class Login extends Component
{
    use HasToast;

    public LoginForm $form;

    public function submit(): void
    {
        if (! $this->form->submit()) {
            // Show toast only if no specific error message is already added
            if (! $this->form->getErrorBag()->has('email')) {
                $this->toastError('Invalid email or password.');
            }
            return;
        }

        Toast::success('Welcome back! You have successfully logged in.');

        $this->redirect(route('tenant.dashboard', absolute: false), navigate: true);
    }
}