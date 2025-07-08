<x-layout>
    <x-page-header>
        <x-page-heading class="mb-0!">Log in</x-page-heading>
    </x-page-header>

    <x-forms.form method="POST" action="/login" enctype="multipart/form-data">
        <x-forms.input name="login" label="Email or Username" type="text" />
        <x-forms.input name="password" label="Password" type="password" />

        <x-forms.button>Log in</x-forms.button>
    </x-forms.form>
</x-layout>
