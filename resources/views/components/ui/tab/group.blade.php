@aware(['variant' => 'outlined'])

@php


$variantClasses = match($variant){
    'outlined' => [
        'overflow-x-auto flex flex-col relative',
    ],
    'non-contained' => [
        // all handled on thier wrapper below
    ],
    'pills' => [
        'my-2'
    ],
    default => []
};
$classes = [
    'flex [:where(&)]:items-center [:where(&)]:justify-center', // all tabs group are centred until they can be overiden without !
    ...$variantClasses,
];
@endphp

<ul
    {{ $attributes->class(Arr::toCssClasses($classes))}}
    x-ref="tabItem"
    x-on:keydown.right.prevent.stop="$focus.wrap().next()"
    x-on:keydown.home.prevent.stop="$focus.first()"
    x-on:keydown.page-up.prevent.stop="$focus.first()"
    x-on:keydown.left.prevent.stop="$focus.wrap().prev()"
    x-on:keydown.end.prevent.stop="$focus.last()"
    x-on:keydown.page-down.prevent.stop="$focus.last()"
    role="tablist"
    data-slot="tabs-group"
>

{{-- non contained needs a wrapper for   --}}
@if($variant === 'non-contained')
    <div class="flex justify-between gap-1 w-full bg-white/5 border-2 border-black/10 dark:border-white/10 rounded-(--noncontained-variant-radius) p-(--noncontained-variant-padding) [--noncontained-variant-radius:var(--radius-box)] [--noncontained-variant-padding:--spacing(.75)]">
        {{ $slot }}
    </div>
@else
    <div class=" w-full z-10">
    {{ $slot }}
    </div>
    <hr class="border-none bg-neutral-300 dark:bg-neutral-700 w-full h-[2px] absolute bottom-0" />
@endif
</ul>
