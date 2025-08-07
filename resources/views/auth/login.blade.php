<x-layout>
    <x-page-header>
        <x-page-heading class="mb-0!">Log in</x-page-heading>
    </x-page-header>
    <section class="max-w-2xl mx-auto space-y-6 w-full">
        <x-forms.form method="POST" action="/login" enctype="multipart/form-data">
            <x-forms.input name="login" label="Email or Username" type="text" />
            <x-forms.input name="password" label="Password" type="password" />

            <x-forms.button>Log in</x-forms.button>
        </x-forms.form>


        <div
            class="relative flex items-center justify-center my-4 after:content-[''] after:absolute after:right-0 after:left-0 after:h-0.5 after:w-full after:bg-gray-300">
            <span class="px-4 bg-[#2c2c2c] z-1">or</span>
        </div>
        <a href="/auth/redirect"
            class="w-full p-4 bg-[#424242] flex justify-center items-center rounded-xl [&_path]:fill-white border-white/10 border hover:bg-[#2e2e2e]">
            <x-svgs.github />
            Log in with Github</a>
    </section>
</x-layout>
