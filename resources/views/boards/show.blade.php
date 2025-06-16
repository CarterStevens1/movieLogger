@props(['board'])

<x-layout>
    <div class="flex justify-between items-center pb-8">
        <x-page-heading class="text-center mb-0!">{{ $board->name }}</x-page-heading>
        <div>
            <x-button href="{{ url('my-boards/' . $board->id . '/edit') }}" class="mx-0! ms-auto!">Edit board</x-button>
            @if (Auth::user()->id === $board->user_id)
                <x-button href="{{ url('my-boards/' . $board->id . '/unshare') }}" class="mx-0! ms-auto!">Unshare
                    board</x-button>
            @endif
        </div>
    </div>
    <hr class="w-full border-t border-white/10">


    {{-- Add ability to create tags and delete tags --}}
    <div class="flex flex-col items-center justify-center">
        {{-- Create hr as a blade template --}}
        <hr class="w-full border-t border-white/10">
        <div class="pt-6">
            <h2 class="text-2xl font-bold text-center">Current board tags</h2>
            <div class="flex flex-wrap justify-center">
                <div class="flex items-center gap-6 pt-6">
                    @php
                        $tagsArray = explode(',', $board->tags);
                    @endphp
                    @foreach ($tagsArray as $tag)
                        <x-tag :tag="$tag" class="rounded-2xl" />
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Add table with ability to add rows and columns making it fully editable - This will be moved into its own blade template --}}
    <section class="mt-20 text-center">
        <p>INSERT TABLE HERE</p>
    </section>
</x-layout>
