@props(['label', 'name'])

@php
    $defaults = [
        'id' => $name,
        'name' => $name,
        'class' =>
            'appearance-none rounded-xl bg-[#424242] border border-white/10 px-5 py-4 w-full text-white border-gray-600 focus:ring-blue-500 focus:border-blue-500',
    ];
@endphp

<x-forms.field :$label :$name>
    <select {{ $attributes($defaults) }}>
        {{ $slot }}
    </select>
</x-forms.field>
