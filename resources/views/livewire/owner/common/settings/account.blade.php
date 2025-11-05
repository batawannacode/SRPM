<div >
    <x-ui.card hoverless size="full" class="w-full justify-center items-center flex-col divide-y divide-neutral-200 dark:divide-neutral-600 rounded-md p-5">

        {{-- === Profile Avatar === --}}
        <div class="grid w-full grid-cols-1 gap-x-8 gap-y-10 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-neutral-800 dark:text-neutral-50">Profile Photo</h2>
                <p class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400">
                    Upload your profile image. This will be displayed in your account.
                </p>
            </div>

            <div class="md:col-span-2 flex flex-col items-start gap-5">
                <form wire:submit.prevent="updateAvatar" class="flex flex-col items-start justify-center gap-4">
                    <div class="relative">
                        <div class="h-32 w-32 rounded-full flex justify-center items-center overflow-hidden border-4 border-neutral-200 dark:border-neutral-700 shadow">
                            @if ($form->avatar)
                            <img src="{{ $form->avatar->temporaryUrl() }}" alt="Preview" class="object-cover w-full h-full rounded-full">
                            @elseif (auth()->user()->avatar_path)
                            <img src="{{ Storage::url(auth()->user()->avatar_path) }}" alt="Current Avatar" class="object-cover w-full h-full rounded-full">
                            @else
                            <x-ui.avatar name="{{ $user->fullName }}" color="auto" size="full" circle />
                            @endif
                        </div>

                        <!-- Upload Button -->
                        <label for="avatar" class="absolute bottom-0 right-0 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full p-2 cursor-pointer shadow">
                            <x-icon name="camera" class="h-4 w-4" />
                            <input type="file" id="avatar" wire:model.live="form.avatar" accept="image/*" class="hidden" />
                        </label>
                    </div>

                    @error('form.avatar')
                    <x-ui.error class="text-xs mt-0!" :messages="$message" />
                    @enderror

                    <x-ui.button type="submit" wire:target="form.avatar" color="emerald" class="mt-2">Update Photo</x-ui.button>

                    <div wire:loading wire:target="form.avatar" class="text-sm text-neutral-500">
                        Uploading preview...
                    </div>
                </form>
            </div>
        </div>

        {{-- === Personal Information === --}}
        <div class="grid grid-cols-1 gap-x-8 gap-y-10 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-neutral-800 dark:text-neutral-50">Personal Information</h2>
                <p class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400">
                    Use a permanent email address where you can use to log in.
                </p>
            </div>

            <div class="md:col-span-2 flex flex-col gap-5">
                <form class="w-full space-y-5" wire:submit.prevent="updatePersonalInfo">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-ui.field>
                            <x-ui.label for="first_name" text="{{ __('First Name') }}" />
                            <x-ui.input required type="text" wire:model='form.first_name' placeholder="First Name" />
                        </x-ui.field>
                        <x-ui.field>
                            <x-ui.label for="last_name" text="{{ __('Last Name') }}" />
                            <x-ui.input required type="text" wire:model='form.last_name' placeholder="Last Name" />
                        </x-ui.field>
                    </div>

                    <x-ui.field>
                        <x-ui.label for="email" text="{{ __('Email') }}" />
                        <x-ui.input required type="email" wire:model='form.email' placeholder="m@example.com" />
                    </x-ui.field>
                    <x-ui.field>
                        <x-ui.label for="phone_number" text="{{ __('Phone Number') }}" />
                        <x-ui.input required type="text" wire:model='form.phone_number' placeholder="09XXXXXXXXX" />
                    </x-ui.field>

                    <!-- errors-hasAny(['form.first_name', 'form.last_name', 'form.email', 'form.phone_number']-->
                    @if($errors->hasAny(['form.first_name', 'form.last_name', 'form.email', 'form.phone_number']))
                    @foreach(['form.first_name', 'form.last_name', 'form.email', 'form.phone_number'] as $field)
                    @error($field)
                    <x-ui.error class="text-xs mt-0!" :messages="$message" />
                    @enderror
                    @endforeach
                    @endif

                    <div class="flex" >
                        <x-ui.button type="submit" class="disabled:bg-neutral-400 disabled:cursor-not-allowed!" disabled wire:dirty.attr.remove="disabled" wire:target="form.first_name, form.last_name, form.email, form.phone_number" color="emerald">Save</x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- === Password Update === --}}
        <div class="grid w-full grid-cols-1 gap-x-8 gap-y-10 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-neutral-800 dark:text-neutral-50">Change Password</h2>
                <p class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-400">
                    Update your password associated with your account.
                </p>
            </div>

            <div class="md:col-span-2">
                <form class="w-full space-y-5" wire:submit.prevent="updatePassword">
                    <x-ui.field>
                        <x-ui.label for="current-password" text="{{ __('Current Password') }}" />
                        <x-ui.input clearable required type="password" id="current-password" wire:model='form.current_password' placeholder="Current Password" revealable />
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.label for="new-password" text="{{ __('New Password') }}" />
                        <x-ui.input clearable required type="password" id="new-password" wire:model='form.password' placeholder="New Password" revealable />
                        <x-ui.description class="text-xs">
                            Your password must be at least 8 characters long and contain one uppercase letter, one lowercase letter, and one number.
                        </x-ui.description>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.label for="password_confirmation" text="{{ __('Confirm Password') }}" />
                        <x-ui.input clearable required type="password" id="password_confirmation" wire:model='form.password_confirmation' placeholder="Confirm Password" revealable />
                        <x-ui.description class="text-xs">Your password must match the confirmation password.</x-ui.description>
                    </x-ui.field>

                    @if($errors->hasAny(['form.current_password', 'form.password', 'form.password_confirmation']))
                    @foreach(['form.current_password', 'form.password', 'form.password_confirmation'] as $field)
                    @error($field)
                    <x-ui.error class="text-xs mt-0!" :messages="$message" />
                    @enderror
                    @endforeach
                    @endif

                    <div class="flex">
                        <x-ui.button type="submit" class="disabled:bg-neutral-400 disabled:cursor-not-allowed!" disabled wire:dirty.attr.remove="disabled" wire:target="form.current_password, form.password, form.password_confirmation" color="emerald">Save</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.card>
</div>

