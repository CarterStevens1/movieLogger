<x-layout>
    <x-page-heading>Edit {{ auth()->user()->name }}</x-page-heading>


    <x-forms.form id="update" method="POST" action="/edit" enctype="multipart/form-data">
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
        <x-forms.button form="update">Save</x-forms.button>
        {{-- Get confirmation to deleten before deleting --}}
        <form id="deleteUser" method="POST" action="/destroy">
            @csrf
            @method('POST')
            <button form="deleteUser" class="flex gap-2 mx-0 bg-red-500 rounded py-2 px-6 font-bold cursor-pointer">
                Delete User</button>
        </form>

    </div>


</x-layout>
