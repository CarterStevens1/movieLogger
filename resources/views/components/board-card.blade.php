@props(['board'])

@php
    $user = \App\Models\User::find($board->user_id);
@endphp

<a href="/my-boards/{{ $board->id }}">
    <x-panel class="flex flex-col gap-x-6 p-6">
        <div class="flex items-center mt-auto flex-wrap gap-3">
            {{-- @foreach ($board->tags as $tag)
                <x-tag :tag="$tag" size="small" />
            @endforeach --}}

        </div>
        <div class="pt-8">
            <p class="text-sm">{{ $board->created_at->format('j M Y') }}</p>
            <h3 class="group-hover:text-blue-600 text-xl font-bold transition-all duration-300">
                {{ $board->name }}
            </h3>
            <p>{{ $board->description }}</p>
            <div class="self-start text-sm pt-4">Created by: {{ $user->name }}</div>
        </div>
    </x-panel>
</a>
