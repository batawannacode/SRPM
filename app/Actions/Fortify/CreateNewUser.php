<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique(User::class),
            ],
            'phone_number' => ['required', 'numeric', 'digits_between:10,15'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'first_name' => $input['first_name'],
            'last_name'  => $input['last_name'],
            'email'      => $input['email'],
            'phone_number' => $input['phone_number'],
            'password'   => Hash::make($input['password']),
            'email_verified_at' => now(),
        ]);
    }
}