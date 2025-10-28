<div class="flex flex-col gap-6">
    {{-- @unless (\App\Models\User::role('owner')->count() >= 1)
    <div class="flex items-center w-full justify-center">
        <a wire:navigate.hover href="{{ route('owner.auth.login') }}" @class(["rounded-l-lg text-center py-3 w-full !bg-primary text-white hover:!text-white hover:!bg-primary duration-200 ease-in-out"])>Login</a>
        <a wire:navigate.hover href="{{ route('owner.auth.register') }}" @class(["rounded-r-lg text-center py-3 w-full text-neutral-800 text-base font-medium hover:text-neutral-800 dark:text-neutral-200 dark:hover:text-neutral-200 hover:bg-primary/10 dark:hover:bg-primary/30 duration-200 ease-in-out"])>Register</a>
    </div>
    @endunless --}}

    <form wire:submit.prevent="submit" class="flex flex-col gap-6">
        <!-- Email Address -->
        <x-ui.field>
            <x-ui.label for="email" text="{{ __('Email') }}" />
            <x-ui.input required type="email" wire:model='form.email' placeholder="m@example.com" />
        </x-ui.field>

        <!-- Password -->
        <x-ui.field>
            <x-ui.label for="password" text="{{ __('Password') }}" />
            <x-ui.input required type="password" wire:model='form.password' placeholder="Password" revealable />
        </x-ui.field>

        <div class="flex items-center justify-between gap-5">
            <!-- Remember Me -->
            <x-ui.checkbox size="sm" label="Remember me" wire:model='form.remember' />
            {{-- Forgot password link --}}
            <x-ui.link :href="route('password.request')" wire:navigate class="text-sm">
                Forgot password?
            </x-ui.link>
        </div>

         @if ($errors->any())
         @foreach($errors->all() as $error)
         <x-ui.error class="text-xs !mt-0" :messages="$error" />
         @endforeach
         @endif

        <x-ui.button type="submit" class="!py-5.5">Access Lease Portal</x-ui.button>
    </form>
</div>

