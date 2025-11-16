<?php

namespace App\Livewire\Forms\Tenant;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
    public string $phone_number = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'max:5120'], // Max 5MB
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone_number' => ['required', 'numeric', 'digits_between:10,15'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'current_password' => ['required', 'string', 'min:8'],
            'password' => $this->passwordRules(),
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
            'email.unique' => 'The email address is already registered.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.numeric' => 'The phone number must be a valid number.',
            'phone_number.digits_between' => 'The phone number must be between 10 and 15 digits.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'current_password.min' => 'The current password must be at least 8 characters.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password_confirmation.min' => 'The password confirmation must be at least 8 characters.',
        ];
    }

    public function editAvatar(){

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

            $this->addError('avatar', 'Failed to update profile photo.');
        }
    }
    public function editPersonalInfo(){
        $this->validateOnly('email');
        $this->validateOnly('first_name');
        $this->validateOnly( 'last_name');
        $this->validateOnly( 'phone_number');

        DB::beginTransaction();

        try {
            $user = auth()->user();

            $user->update([
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
                'phone_number' => $this->phone_number,
            ]);

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->addError('email', 'Failed to update personal information.');
            return false;
        }
    }
    public function editPassword(){
        $this->validateOnly('current_password');
        $this->validateOnly('password');
        $this->validateOnly('password_confirmation');

        DB::beginTransaction();

        try {
            $user = auth()->user();

            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Your current password is incorrect.');
                return false;
            }

            if ($this->password !== $this->password_confirmation) {
                $this->addError('password_confirmation', 'Passwords do not match.');
                return false;
            }

            $user->update([
                'password' => Hash::make($this->password),
            ]);

            DB::commit();

            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            // If it's not a validation error, wrap it in one
            $this->addError('password', 'Failed to update password. Please try again.');
            return false;

        }
    }
}
