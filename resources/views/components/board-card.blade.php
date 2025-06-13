@props(['board'])

<a href="/my-boards/{{ $board->id }}">
    <x-panel class="flex flex-col gap-x-6 p-6">
        <div class="flex items-center mt-auto flex-wrap gap-3">
            {{-- @foreach ($board->tags as $tag)
                <x-tag :tag="$tag" size="small" />
            @endforeach --}}

        </div>
        <div class="pt-8">
            <h3 class="group-hover:text-blue-600 text-xl font-bold transition-all duration-300">
                {{ $board->name }}
            </h3>
            <p>{{ $board->description }}</p>
            <div class="self-start text-sm">Created by: XXX</div>
        </div>
    </x-panel>
</a>
