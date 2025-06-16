@props(['boards', 'sharedBoards'])

<x-layout>
    <div class="flex justify-between items-center pb-8">
        <x-page-heading class="text-center mb-0!">My Boards</x-page-heading>
        <x-button href="{{ Route('boards.create') }}" class="mx-0! ms-auto!">Create a new board</x-button>
    </div>
    <hr class="w-full border-t border-white/10">



    <section class="pt-10">
        <h2 class="text-2xl font-bold">My Created Boards</h2>

        <div class="pt-10 grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
            {{-- Display all boards you've created grab the user id and check for all boards that user has created --}}
            @foreach ($boards as $board)
                <x-board-card :board="$board" />
            @endforeach

        </div>
    </section>
    {{-- Add ability to view all boards you've created --}}
    {{-- Ability to share a board with another user --}}
    {{-- Ability to delete a board --}}
    {{-- Ability to edit a board --}}



    {{-- OPTIONAL: Add ability to view shared boards with you --}}
    <section class="pt-10">
        <h2 class="text-2xl font-bold">Boards shared with me</h2>
        <div class="pt-10 grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
            @if ($sharedBoards->isEmpty())
                <p>No boards shared with you.</p>
            @else
                <ul>
                    @foreach ($sharedBoards as $board)
                        <x-board-card :board="$board" />
                    @endforeach
                </ul>
            @endif

        </div>
    </section>
</x-layout>
