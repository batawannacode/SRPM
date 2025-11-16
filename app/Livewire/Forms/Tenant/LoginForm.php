<?php

namespace App\Livewire\Forms\Tenant;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;
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

        // Validate user manually
        $user = $this->validateUser($this->email, $this->password, [Role::Tenant->value]);

        if (! $user) {
            // Increment failed attempts if validation fails
            RateLimiter::hit($this->throttleKey(), 60); // 60 seconds lockout
            return false;
        }

        // Log in the user
        Auth::login($user, $this->remember);

        // Regenerate session for security
        session()->regenerate();

        // Clear rate limiter on success
        RateLimiter::clear($this->throttleKey());

        return true;
    }


    /**
     * Validate the user credentials and role.
     */
    private function validateUser(string $email, string $password, array $roles): false|User
    {
        $user = User::firstWhere('email', $email);

        if (! $user) {
            $this->addError('email', 'No account found with this email address.');
            return false;
        }

        if (! Hash::check($password, $user->password)) {
            $this->addError('password', 'Incorrect password.');
            return false;
        }

        if (! $user->hasAnyRole($roles)) {
            $this->addError('email', 'You are not authorized to access this area.');
            return false;
        }

        return $user;
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