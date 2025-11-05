<?php

namespace App\Livewire\Forms\Owner;

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Role;
use App\Models\Owner;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class RegisterForm extends Form
{
    // Personal Information
    public string $email = '';
    public string $phone_number = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Property Information
    public string $property_name = '';
    public string $property_address = '';
    public int $total_units = 0;
    public bool $terms = false;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,15'],
            'password' => ['required', 'min:8', 'confirmed'],
            'property_name' => ['required', 'string', 'max:255'],
            'property_address' => ['required', 'string', 'max:255'],
            'total_units' => ['required', 'integer', 'min:1'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The email address is required.',
            'email.email' => 'The email address must be a valid email format.',
            'email.unique' => 'The email address is already registered.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.max' => 'The phone number must not exceed 15 characters.',
            'phone_number.regex' => 'The phone number must be a valid format.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'property_name.required' => 'The property name is required.',
            'property_address.required' => 'The property address is required.',
            'total_units.required' => 'The total units are required.',
            'terms.accepted' => 'You must accept the terms and conditions.',
        ];
    }

    public function submit(): User|false
    {
        $this->validate();

        // ✅ Check rate limit BEFORE doing anything
        $this->checkRateLimit();

        try {
            // Use transaction to ensure all data is created atomically
            DB::beginTransaction();

            /** 1️⃣ Create the user */
            $user = app(CreateNewUser::class)->create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);

            // Assign role
            $user->assignRole(Role::Owner->value);

            /** 2️⃣ Create owner profile */
            $owner = Owner::create(['user_id' => $user->id]);

            /** 3️⃣ Create the owner’s property */
            $property = Property::create([
                'owner_id' => $owner->id,
                'name' => $this->property_name,
                'address' => $this->property_address,
                'total_units' => $this->total_units,
            ]);
            // Set as active property
            $owner->update(['active_property' => $property->id]);

            /** 4️⃣ Generate units dynamically */
            collect(range(1, $this->total_units))
                ->each(fn($i) =>
                    Unit::create([
                        'property_id' => $property->id,
                        'unit_number' => "Unit {$i}",
                    ])
                );

            DB::commit();

            // ✅ Hit the rate limiter (successful attempt)
            RateLimiter::hit($this->throttleKey(), 60); // 60 seconds lockout window

            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();

            // You can log this or handle gracefully
            report($e);

            return false;
        }
    }

    /**
     * Check if too many login attempts have been made.
     */
    protected function checkRateLimit(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
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
