@props([])

<a href="{{ $href }}"
   {{ $attributes->class($computedClass) }}
   @if($active) aria-current="page" @endif>
    {{ $slot }}
</a>
