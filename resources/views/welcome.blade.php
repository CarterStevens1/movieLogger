<x-layout>

    @auth
        {{-- Add ability to create a new board --}}
        <section class="pt-10">
            <h2 class="text-2xl font-bold">Create a new board</h2>
            <div class="pt-10">
                <x-boards.createBoard />
            </div>
        </section>

        <section class="pt-10">
            <h2 class="text-2xl font-bold">Recently edited boards</h2>
        </section>
        {{-- Add ability to see top tags --}}
        <section class="pt-10">
            <h2 class="text-2xl font-bold">Top tags</h2>
        </section>

    @endauth

    @guest
        <section class="pt-10">
            <h1 class="text-2xl font-bold text-center">Log in or create an account to start creating boards</h1>
        </section>
    @endguest


</x-layout>
