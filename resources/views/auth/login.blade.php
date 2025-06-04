<x-layout>
    <x-page-heading>Log in</x-page-heading>


    <x-forms.form method="POST" action="/login" enctype="multipart/form-data">
        <x-forms.input name="email" label="Email" type="email" />
        <x-forms.input name="password" label="Password" type="password" />

        <x-forms.button>Log in</x-forms.button>
    </x-forms.form>
</x-layout>
