<x-layout>
    <section>
        <div class="relative isolate px-6 lg:px-8">
            <div class="fixed inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-green-500 to-secondaryGreen-200 opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
                    style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                </div>
            </div>
            <div class="mx-auto max-w-2xl pb-32 lg:py-32">
                <div class="text-center">
                    <h1 class="text-5xl font-semibold tracking-tight text-balance sm:text-7xl">Boardchive</h1>
                    <p class="mt-8 text-lg font-medium text-pretty sm:text-xl/8">Board creation made easy, it's like
                        excel but on rails for logging.</p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        @auth
                            <a href="{{ url('/my-boards') }}"
                                class="rounded-lg bg-peach-red-600 px-4 py-2 font-semibold text-white shadow-xs hover:bg-transparent border border-peach-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-peach-red-600 transition-all duration-300 ease-in-out">Dashboard</a>
                        @endauth
                        @guest
                            <a href="{{ url('/register') }}"
                                class="rounded-lg bg-peach-red-600 px-4 py-2 font-semibold text-white shadow-xs hover:bg-transparent border border-peach-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-peach-red-600 transition-all duration-300 ease-in-out">Get
                                started today</a>
                        @endguest

                    </div>
                </div>
            </div>
            <div class="fixed inset-x-0 bottom-0 -z-10 transform-gpu overflow-hidden blur-3xl" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-green-500 to-secondaryGreen-200 opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
                    style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                </div>
            </div>
        </div>
    </section>
</x-layout>
