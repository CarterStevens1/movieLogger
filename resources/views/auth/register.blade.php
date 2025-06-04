<x-layout>
    <x-page-heading>Register</x-page-heading>


    <x-forms.form method="POST" action="/register" enctype="multipart/form-data">
        <x-forms.input name="name" label="Name" />
        <x-forms.input name="email" label="Email" type="email" />
        <x-forms.input name="password" label="Password" type="password" />
        <x-forms.input name="password_confirmation" label="Confirm Password" type="password" />

        <x-forms.divider />

        <x-forms.button>Create Account</x-forms.button>
    </x-forms.form>
</x-layout>
