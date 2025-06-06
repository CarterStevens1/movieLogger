<x-layout>
    <x-page-heading class="text-center">Movie Board</x-page-heading>

    {{-- Add ability to create tags and delete tags --}}
    <div class="flex flex-col items-center justify-center">
        {{-- Create hr as a blade template --}}
        <hr class="w-full border-t border-white/10">
        <div class="pt-6">
            <h2 class="text-2xl font-bold text-center">Current board tags</h2>
            <div class="flex flex-wrap justify-center">
                <div class="flex items-center gap-6 pt-6">
                    <x-tag size="small" class="bg-amber-500 text-white"> 101 Movies</x-tag>
                    <x-tag size="small" class="bg-amber-500 text-white"> 101 Movies</x-tag>
                    <x-tag size="small" class="bg-amber-500 text-white"> 101 Movies</x-tag>
                </div>
            </div>
        </div>
    </div>

    {{-- Add table with ability to add rows and columns making it fully editable - This will be moved into its own blade template --}}
    <section class="mt-20 text-center">
        <p>INSERT TABLE HERE</p>
    </section>
</x-layout>
