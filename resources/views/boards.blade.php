@props(['boards', 'sharedBoards'])

<x-layout>
    <div class="flex flex-wrap justify-between items-center pb-8 gap-4">
        <x-page-heading class="text-center mb-0!">Dashboard</x-page-heading>
        <x-global.buttonLink href="{{ Route('boards.create') }}" class="mx-0!">Create a new
            board</x-global.buttonLink>
    </div>
    <hr class="w-full border-t border-white/10">



    <section class="pt-10">
        <h2 class="text-2xl font-bold">My Boards</h2>

        <div class="pt-10 grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
            {{-- Display all boards you've created grab the user id and check for all boards that user has created --}}
            <x-boards.createBoard />
            @foreach ($boards as $board)
                <x-boards.board-card :board="$board" />
            @endforeach

        </div>
    </section>

    <section class="pt-10">
        <h2 class="text-2xl font-bold">Boards shared with me</h2>
        <div class="pt-10 grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
            @if ($sharedBoards->isEmpty())
                <p>No boards shared with you.</p>
            @else
                <ul>
                    @foreach ($sharedBoards as $board)
                        <x-boards.board-card :board="$board" />
                    @endforeach
                </ul>
            @endif

        </div>
    </section>
</x-layout>
