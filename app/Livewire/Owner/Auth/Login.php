<?php

namespace App\Livewire\Owner\Auth;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\Owner\LoginForm;
use App\Support\Toast;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth', ['title' => 'Owner Login', 'user' => 'Owner'])]
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

        // Redirect to dashboard
        $this->redirect(route('owner.dashboard', absolute: false), navigate: true);
    }

}