@aware(['label', 'size'])

{{-- Label --}}
@php
    $classes=[

        'font-medium text-neutral-700 dark:text-neutral-200 [:where(&)]:text-neutral-800 font-semibold [:where(&)]:dark:text-white select-none',
        match ($size) {
            'xs' => 'text-xs',
            'sm' => 'text-sm',
            'md' => 'text-sm',
            'lg' => 'text-base',
            'xl' => 'text-lg',
            default => 'text-sm',
        },
    ];
@endphp

<label
    @class($classes)
    data-slot="checkbox-label"
>
    {{ $label }}
</label>
