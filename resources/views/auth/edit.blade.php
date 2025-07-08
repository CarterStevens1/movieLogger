<x-layout>
    <x-page-heading>Edit {{ auth()->user()->name }}</x-page-heading>


    <x-forms.form id="update" method="POST" action="/edit" enctype="multipart/form-data">
        <x-forms.input name="name" label="Name" type="text" value="{{ auth()->user()->name }}" />
        <x-forms.input name="current_password" label="Password" type="password" />
        <x-forms.input name="password" label="New password" type="password" />
        <x-forms.input name="password_confirmation" label="New password" type="password" />

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-success">
                {{ session('error') }}
            </div>
        @endif
    </x-forms.form>
    <x-forms.divider />
    <div class="flex justify-between max-w-2xl mx-auto space-y-6">
        <x-global.button variant="save" form="update">Save</x-global.button>
        {{-- Get confirmation to deleten before deleting --}}
        <form id="deleteUser" method="POST" action="/destroy">
            @csrf
            @method('POST')
            <x-global.button onclick="return confirm('Are you sure you want to delete your account?')" variant="danger"
                form="deleteUser">
                Delete User</x-global.button>
        </form>

    </div>


</x-layout>
