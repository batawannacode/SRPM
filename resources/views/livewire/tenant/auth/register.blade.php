<div class="flex flex-col gap-6">
    <form wire:submit.prevent="submit" class="flex flex-col gap-6">
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

        <x-ui.button type="submit" color="emerald" class="!py-5.5">Register</x-ui.button>
    </form>
    <div class="flex justify-center">
        <x-ui.text class="text-sm">
            Already have an account?
            <x-ui.link :href="route('tenant.auth.login')" wire:navigate.hover class="text-sm">
                Login here.
            </x-ui.link>
        </x-ui.text>
    </div>
</div>

