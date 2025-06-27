@props(['variant' => 'primary'])

@php
    $classes = 'rounded py-2 px-6font-bold cursor-pointer';
    if ($variant == 'primary') {
        $classes .= 'bg-blue-800';
    } elseif ($variant == 'secondary') {
        $classes .= '';
    }
@endphp

<a {{ $attributes(['class' => $classes]) }}>{{ $slot }}</a>
