@aware(['darkIcon'=>'moon','lightIcon'=>'sun','iconVariant' => "mini"])

<div
    class="flex items-center transition-all duration-200 size-10 overflow-hidden"
 >
    <x-ui.button
        :icon="$lightIcon"
        :$iconVariant
        variant="soft"
        x-cloak
        x-show="$theme.isResolvedToLight"
        x-on:click="$theme.toggle()"
        iconVariant="outline"
        class="text-neutral-600 dark:text-neutral-200 hover:bg-indigo-100 dark:hover:bg-neutral-700"
        role="button"
        aria-pressed="true"
        x-bind:aria-pressed="$theme.isResolvedToLight"
        aria-label="Activate light theme"
    />
    <x-ui.button
        :icon="$darkIcon"
        :$iconVariant
        variant="soft"
        x-cloak
        x-show="$theme.isResolvedToDark"
        x-on:click="$theme.toggle()"
        iconVariant="outline"
        class="text-neutral-600 dark:text-neutral-200 hover:bg-indigo-100 dark:hover:bg-neutral-700"
        role="button"
        aria-pressed="true"
        x-bind:aria-pressed="$theme.isResolvedToDark"
        aria-label="Activate dark theme"
    />
</div>
