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
            $this->toastError('Invalid email or password.');
            return;
        }
        // Regenerate session for security
        Session::regenerate();

        Toast::success('Welcome back! You have successfully logged in.');

        // Redirect to dashboard
        $this->redirectIntended(
            default: route('owner.dashboard', absolute: false),
            navigate: true
        );
    }

}
