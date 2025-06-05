<x-layout>
    <x-page-heading>Edit {{ auth()->user()->name }}</x-page-heading>


    <x-forms.form method="POST" action="/edit" enctype="multipart/form-data">
        <x-forms.input name="current_password" label="Password" type="password" />
        <x-forms.input name="password" label="New password" type="password" />
        <x-forms.input name="password_confirmation" label="New password" type="password" />

        <x-forms.button>Save</x-forms.button>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('Password updated successfully.') }}
            </div>
        @endif
    </x-forms.form>


</x-layout>
