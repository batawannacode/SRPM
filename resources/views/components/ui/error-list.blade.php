@props(['errorClass' => 'text-xs'])
<div {{ $attributes->merge(["class"=>"text-start mb-4"]) }}>
    @if ($errors->any())
    @foreach($errors->all() as $error)
    <x-ui.error class="{{ $errorClass }}" :messages="$error" />
    @endforeach
    @endif
</div>
