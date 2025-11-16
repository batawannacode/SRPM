<div class="flex flex-col gap-6">
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

        <x-ui.button type="submit" class="!py-5.5">Login</x-ui.button>
    </form>
    <div class="flex justify-center">
        <x-ui.text class="text-sm">
            Don't have an account?
            <x-ui.link :href="route('tenant.auth.register')" wire:navigate.hover class="text-sm">
                Register here.
            </x-ui.link>
        </x-ui.text>
    </div>
</div>

