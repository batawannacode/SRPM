{{-- Property Selector --}}
<li class="group px-4 mb-5">
    <x-ui.label class="mb-1 text-xs font-medium text-neutral-700 dark:text-neutral-300 !line-clamp-1">Property</x-ui.label>
    <x-ui.select triggerClass="py-2" class="text-sm" placeholder="{{ $this->property->name ??= 'Choose property...' }}" wire:model.live="selectedProperty">
        @foreach($properties as $key => $property)
        <x-ui.select.option value="{{ $property->id }}">
            {{ $property->name }}
        </x-ui.select.option>
        @endforeach
    </x-ui.select>
</li>

