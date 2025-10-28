<?php

namespace App\Livewire\Owner\Auth;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\Owner\RegisterForm;
use App\Support\Toast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth', ['title' => 'Owner Register', 'user' => 'Owner'])]
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

        $this->redirectIntended(
            default: route('owner.dashboard', absolute: false),
            navigate: true
        );
    }
}
