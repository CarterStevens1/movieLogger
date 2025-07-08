@props(['board'])

@php
    $user = \App\Models\User::find($board->user_id);
@endphp

<a href="/my-boards/{{ $board->id }}">
    <x-panel class="flex flex-col gap-x-6 p-6 justify-between min-h-40">
        <div>
            <h3 class="group-hover:text-green-500 text-xl font-bold transition-all duration-300">
                {{ $board->name }}
            </h3>
            @if ($board->description)
                <p>{{ $board->description }}</p>
            @endif

            <p class="self-start text-sm pt-1">Created by: {{ $user->name }}</p>
        </div>
        <p class="text-sm">Created: {{ $board->created_at->format('j M Y') }}</p>
    </x-panel>
</a>
