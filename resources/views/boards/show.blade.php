@props(['board'])

<x-layout>
    <div class="flex justify-between items-center pb-8">
        <x-page-heading class="text-center mb-0!">{{ $board->name }}</x-page-heading>
        <div>
            @if (Auth::user()->id === $board->user_id)
                <x-global.buttonLink href="{{ url('my-boards/' . $board->id . '/edit') }}" class="mx-0! ms-auto!">Edit
                    board</x-global.buttonLink>
            @endif
        </div>
    </div>
    <hr class="w-full border-t border-white/10">


    {{-- Add ability to create tags and delete tags --}}

    <div class="flex justify-between flex-col xl:flex-row items-start pt-6 gap-6">
        <div>
            @if ($board->tags !== null)
                <h2 class="text-2xl font-bold">Current board tags</h2>

                <div class="flex flex-wrap items-center gap-6 pt-6">
                    @php
                        $tagsArray = collect(explode(',', $board->tags))
                            ->filter() // Remove empty values
                            ->map(function ($tag, $index) {
                                $colors = ['#ef4444', '#f59e0b', '#10b981', '#6366f1', '#8b5cf6', '#ec4899', '#06b6d4'];
                                return [
                                    'name' => trim($tag),
                                    'color' => $colors[$index % count($colors)],
                                ];
                            })
                            ->values()
                            ->toArray();
                    @endphp
                    @foreach ($tagsArray as $tag)
                        <x-tag :tag="$tag" class="rounded-2xl" />
                    @endforeach

                </div>
                <hr class="block xl:hidden w-full border-t border-white/10">
            @endif
        </div>

        {{-- Only allow if user is the same as the board owner --}}
        @if (Auth::user()->id === $board->user_id)
            <!-- Hidden file input for CSV import -->
            <input type="file" id="csvFileInput" accept=".csv" style="display: none;" onchange="importCSV(event)">
            <div class="flex items-start justify-center xl:flex-row gap-4">
                <x-global.button variant="peach" onclick="document.getElementById('csvFileInput').click()">
                    Import CSV
                </x-global.button>
                <x-global.button variant="peach" onclick="exportCSV()">
                    Export CSV
                </x-global.button>
            </div>
        @endif
    </div>

    <section class="mt-20 text-center -mx-10 xl:mx-0">
        <div class="w-full flex items-start overflow-auto border border-solid border-white/20">
            <x-boards.board-table :board="$board" />
        </div>
    </section>
</x-layout>
