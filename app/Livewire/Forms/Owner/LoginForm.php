<?php

namespace App\Livewire\Forms\Owner;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Form;
use Illuminate\Validation\ValidationException;

class LoginForm extends Form
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the user.
     *
     * @return bool
     */
    public function submit(): bool
    {
        $this->validate();

        $this->checkRateLimit();

        // Attempt login
        if (Auth::attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            // Clear rate limit on successful login
            RateLimiter::clear($this->throttleKey());
            return true;
        }

        // Increment failed attempts
        RateLimiter::hit($this->throttleKey(), 60); // Lockout duration = 60 seconds

        $this->addError('email', 'Invalid email or password.');
        return false;
    }

     /**
     * Check if too many login attempts have been made.
     */
    protected function checkRateLimit(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /**
     * Unique rate limit key per user/IP combination.
     */
    protected function throttleKey(): string
    {
        return Str::lower($this->email).'|'.request()->ip();
    }
}
