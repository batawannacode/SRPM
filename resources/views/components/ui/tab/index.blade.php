@aware([ 'variant' => 'outlined', 'size' => 'default'])

@props([
    'label' => null,
    'name' => null,
    'iconAfter' => null,
    'icon' => null,
    'iconVariant' => null,
    'iconClasses' => null,
])

@php
    $classes = match($variant){
        'outlined' => [
            'py-3 !px-7 z-10 text-xs sm:text-sm border-x-none border-t-none rounded-box rounded-b-none border-b-2 border-transparent dark:border-neutral-700 hover:text-primary hover:border-primary justify-center focus:outline-none',
            'data-[active=true]:text-primary',
            'data-[active=true]:border-b-2 data-[active=true]:border-primary',
        ],
        'non-contained' => [
            'data-[active=true]:bg-primary dark:data-[active=true]:bg-neutral-800 data-[active=true]:text-neutral-200',
            'hover:bg-primary focus:bg-primary text-neutral-800 dark:text-neutral-200 hover:text-white focus:text-white text-xs sm:text-sm',
            'rounded-[calc(var(--noncontained-variant-radius)-var(--noncontained-variant-padding))]', // those variables are defined on the group wrapper
        ],
        'pills' => [
            'rounded-full h-8 whitespace-nowrap rounded-full text-sm font-medium',
            'data-[active=true]:bg-primary data-[active=true]:text-primary-fg'
        ],
        default => [],
    };

    // if tab has name we need to bind it, so we can prirotize it then in orders
    // we can mutate the AttributeBag Objet as an array, it implement \ArrayAccess interafce
    if(filled($name)) $attributes['data-name'] = $name;
@endphp

<x-ui.button
    :attributes="$attributes->class(Arr::toCssClasses($classes))"
    x-on:click="handleTabClick($el)"
    data-slot="tab"
    tabindex="0"
    variant="none"
>
    @if($slot->isNotEmpty())
        <span class="flex-1">{{ $slot }}</span>
    @else
        {{ $label }}
    @endif
</x-ui.button>
