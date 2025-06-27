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
            <div class="relative isolate px-6 pt-14 lg:px-8">
                <div class="fixed inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                    aria-hidden="true">
                    <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                    </div>
                </div>
                <div class="mx-auto max-w-2xl py-32">
                    <div class="text-center">
                        <h1 class="text-5xl font-semibold tracking-tight text-balance sm:text-7xl">Boardchive</h1>
                        <p class="mt-8 text-lg font-medium text-pretty sm:text-xl/8">Board creation made easy, it's like
                            excel but on rails for logging.</p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <a href="{{ url('/register') }}"
                                class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Get
                                started today</a>
                        </div>
                    </div>
                </div>
                <div class="fixed inset-x-0 bottom-0 -z-10 transform-gpu overflow-hidden blur-3xl" aria-hidden="true">
                    <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                    </div>
                </div>
            </div>
        </section>
    @endguest


</x-layout>
