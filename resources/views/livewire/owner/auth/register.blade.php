<div class="flex flex-col gap-6">
    {{-- @unless (\App\Models\User::role('owner')->count() >= 1)
    <div class="flex items-center w-full justify-center">
        <a wire:navigate.hover href="{{ route('owner.auth.login') }}" @class(["rounded-l-lg text-center py-3 w-full text-neutral-800 text-base font-medium hover:text-neutral-800 dark:text-neutral-200 dark:hover:text-neutral-200 hover:bg-primary/10 dark:hover:bg-primary/30 duration-200 ease-in-out"])>Login</a>
        <a wire:navigate.hover href="{{ route('owner.auth.register') }}" @class(["rounded-r-lg text-center py-3 w-full text-base font-medium !bg-primary text-white hover:!text-white hover:!bg-primary duration-200 ease-in-out"])>Register</a>
    </div>
    @endunless --}}

    <h3 class="text-xl font-semibold text-center dark:text-neutral-200 text-neutral-800">One time Owner Registration</h3>

    <form wire:submit.prevent="submit" class="flex flex-col gap-6">
        <x-ui.fieldset label="Personal Information">
            <div class="grid grid-cols-2 gap-4">
                <!-- First Name -->
                <x-ui.field>
                    <x-ui.label for="first_name" text="{{ __('First Name') }}" />
                    <x-ui.input required type="text" wire:model='form.first_name' placeholder="First Name" />
                </x-ui.field>
                <!-- Last Name -->
                <x-ui.field>
                    <x-ui.label for="last_name" text="{{ __('Last Name') }}" />
                    <x-ui.input required type="text" wire:model='form.last_name' placeholder="Last Name" />
                </x-ui.field>
            </div>

            <!-- Email Address -->
            <x-ui.field>
                <x-ui.label for="email" text="{{ __('Email') }}" />
                <x-ui.input required type="email" wire:model='form.email' placeholder="m@example.com" />
            </x-ui.field>

            <!-- Phone Number -->
            <x-ui.field>
                <x-ui.label for="phone_number" text="{{ __('Phone Number') }}" />
                <x-ui.input required type="text" wire:model='form.phone_number' placeholder="Phone Number" />
            </x-ui.field>

            <!-- Password -->
            <x-ui.field>
                <x-ui.label for="password" text="{{ __('Password') }}" />
                <x-ui.input required type="password" wire:model='form.password' placeholder="Password" revealable />
                <x-ui.description class="text-xs">Your password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.</x-ui.description>
            </x-ui.field>
            <!-- Confirm Password -->
            <x-ui.field>
                <x-ui.label for="password_confirmation" text="{{ __('Confirm Password') }}" />
                <x-ui.input required type="password" wire:model='form.password_confirmation' placeholder="Confirm Password" revealable />
                <x-ui.description class="text-xs">Your password must match the confirmation password.</x-ui.description>
            </x-ui.field>
        </x-ui.fieldset>
        <x-ui.fieldset label="Property Information">
            <!-- Property Name -->
            <x-ui.field>
                <x-ui.label for="property_name" text="{{ __('Name') }}" />
                <x-ui.input required type="text" wire:model='form.property_name' placeholder="Property 1" />
            </x-ui.field>
            <!-- Property Address -->
            <x-ui.field>
                <x-ui.label for="property_address" text="{{ __('Address') }}" />
                <x-ui.input required type="text" wire:model='form.property_address' placeholder="Blk 1" />
            </x-ui.field>
            <!-- Total Units -->
            <x-ui.field>
                <x-ui.label for="total_units" text="{{ __('Total Units/Rooms') }}" />
                <x-ui.input required type="number" wire:model='form.total_units' placeholder="99" />
            </x-ui.field>
        </x-ui.fieldset>
        <!-- Terms and Conditions -->
        <div class="flex items-center text-sm dark:text-neutral-200">
            <x-ui.checkbox required size="sm" wire:model='form.terms'/>
                I agree to the&nbsp;<x-ui.link href="{{ route('terms-and-conditions') }}" target="_blank" class="text-sm"> Terms and Conditions</x-ui.link>
        </div>

        @if ($errors->any())
        <div class="text-start">
            @foreach($errors->all() as $error)
            <x-ui.error class="text-xs" :messages="$error" />
            @endforeach
        </div>
        @endif

        <x-ui.button type="submit" color="emerald" class="!py-5.5">Register Owner Account</x-ui.button>
    </form>
</div>

