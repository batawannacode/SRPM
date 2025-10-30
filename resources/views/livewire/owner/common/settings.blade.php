<div class="flex flex-col w-full gap-5 relative">
    {{-- HEADER --}}
    <div class="flex flex-col items-start mb-5">
        <h1 class="text-xl md:text-2xl font-bold text-neutral-800 dark:text-neutral-200">Settings</h1>
        <p class="text-sm text-neutral-500">
            Manage your account and property settings. Changes are applied per tab.
        </p>
    </div>

    {{-- Tabs --}}
    <div wire:ignore>
        <x-ui.tabs wire:model.live="activeTab">
            <x-ui.tab.group class="justify-start">
                <x-ui.tab label="Account" name="account" />
                <x-ui.tab label="Property/s" name="property" />
                <x-ui.tab label="Payment Configurations" name="payment" />
            </x-ui.tab.group>
        </x-ui.tabs>
    </div>

    <div class="relative">
        {{-- Loader Overlay (Full Screen Centered) --}}
        <div wire:loading.flex wire:target="activeTab" class="absolute inset-0 z-20 h-svh pt-20 flex items-start justify-center bg-background/60 dark:bg-neutral-900/60 backdrop-blur transition-opacity duration-300">
            {{-- SVG Spinner --}}
            <svg class="animate-spin text-primary dark:text-indigo-400 size-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        {{-- Dynamic content --}}
        <livewire:dynamic-component :is="$this->activeTabs" :key="$activeTab.rand()" />
    </div>

</div>
