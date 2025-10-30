<?php

namespace App\Livewire\Forms\Owner;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Livewire\Form;

class AccountForm extends Form
{
    use PasswordValidationRules, WithFileUploads;

    // Personal Information
    /** @var UploadedFile|null */
    public ?UploadedFile $avatar = null;
    public User $user;
    public string $email = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'max:5120'], // Max 5MB
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => $this->passwordRules(),
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.max' => 'The avatar size must not exceed 5MB.',
            'avatar.required' => 'The avatar is required. Please upload an image. (click the camera button)',
            'email.required' => 'The email address is required.',
            'email.email' => 'The email address must be a valid email format.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'current_password.min' => 'The current password must be at least 8 characters.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'password_confirmation.min' => 'The password confirmation must be at least 8 characters.',
        ];
    }

    public function editAvatar(): bool{

        $this->validateOnly('avatar');

         DB::beginTransaction();

        try {
            $user = auth()->user();

            // Remove old avatar if exists
            if ($user->avatar_path && Storage::exists($user->avatar_path)) {
                Storage::delete($user->avatar_path);
            }

            // Store new avatar
            $path = $this->avatar->store('assets/avatars', 'public');
            $user->update(['avatar_path' => $path]);

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            // Restore previous avatar if possible (optional safeguard)
            if (isset($path) && Storage::exists($path)) {
                Storage::delete($path);
            }

            throw ValidationException::withMessages([
                'avatar' => 'Failed to update avatar. Please try again later.',
            ]);

        }
    }
    public function editPersonalInfo(): bool{
        $this->validateOnly('email');
        $this->validateOnly('first_name');
        $this->validateOnly( 'last_name');

        DB::beginTransaction();

        try {
            $user = auth()->user();

            $user->update([
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
            ]);

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw ValidationException::withMessages([
                'email' => 'Failed to update personal information.',
            ]);
        }
    }
    public function editPassword(){
        $this->validateOnly('current_password');
        $this->validateOnly('new_password');
        $this->validateOnly('password_confirmation');

        DB::beginTransaction();

        try {
            $user = auth()->user();

            if (!Hash::check($this->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Your current password is incorrect.',
                ]);
            }

            if ($this->new_password !== $this->password_confirmation) {
                throw ValidationException::withMessages([
                    'password_confirmation' => 'Passwords do not match.',
                ]);
            }

            $user->update([
                'password' => Hash::make($this->new_password),
            ]);

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            // If it's not a validation error, wrap it in one
            if (! $e instanceof ValidationException) {
                throw ValidationException::withMessages([
                    'password' => 'Failed to update password. Please try again.',
                ]);
            }

            throw $e;
        }
    }
}