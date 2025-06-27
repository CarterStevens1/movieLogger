@props(['variant' => 'primary'])

@php
    $classes = 'rounded-lg py-2 px-4 font-bold cursor-pointer transition-all duration-300 ease-in-out';
    if ($variant == 'primary') {
        $classes .= ' bg-peach-red-500 hover:bg-peach-red-700';
    } elseif ($variant == 'secondary') {
        $classes .= ' bg-green-500 hover:bg-green-700';
    }
@endphp

<a {{ $attributes(['class' => $classes]) }}>{{ $slot }}</a>
