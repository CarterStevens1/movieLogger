@props(['variant' => ''])

@php
    $classes =
        'flex min-w-25 cursor-pointer justify-center gap-2 rounded-lg px-4 py-2 font-bold transition-all duration-300 ease-in-out';
    if ($variant == 'danger') {
        $classes .= ' bg-red-500 hover:bg-transparent outline-red-500 outline';
    } elseif ($variant == 'save') {
        $classes .= ' bg-green-500 hover:bg-green-700 outline-green-500 outline hover:outline-green-700';
    } elseif ($variant == 'peach') {
        $classes .=
            ' bg-peach-red-500 hover:bg-peach-red-700 outline-peach-red-500 outline hover:outline-peach-red-700';
    }
@endphp

<button {{ $attributes(['class' => $classes]) }}>
    {{ $slot }}
</button>
