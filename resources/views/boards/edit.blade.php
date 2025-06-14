@props(['board'])

<x-layout>
    <x-page-heading>Edit board: {{ $board->name }} </x-page-heading>


    <x-forms.form method="POST" action="/my-boards/{{ $board->id }}/edit" enctype="multipart/form-data">
        <x-forms.input name="name" label="Board name" />
        <x-forms.input name="description" label="Description" />
        <x-forms.input name="tags" label="Tags (comma seperated)" />

        <x-forms.divider />

        <x-forms.button>Save</x-forms.button>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('Update board details successfully.') }}
            </div>
        @endif
    </x-forms.form>


</x-layout>
