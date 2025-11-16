@props(['text' => '', 'text_class' => ''])
<div class="flex items-center justify-center py-3">
    <div class="text-center">
        @if (isset($icon))
        {{ $icon }}
        @else
        <svg {{ $attributes->merge(['class' => 'mx-auto h-12 w-12 text-neutral-400']) }}
            fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @endif
        <h3 class="mt-2 text-sm font-medium text-neutral-400 {{ $text_class }}">
            {{ $text }}</h3>
    </div>
</div>
