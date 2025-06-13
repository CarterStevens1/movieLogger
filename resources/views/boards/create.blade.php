<x-layout>
    <x-page-heading>Create a new board</x-page-heading>


    <x-forms.form method="POST" action="/boards/create" enctype="multipart/form-data">
        <x-forms.input name="name" label="Board name" />
        <x-forms.input name="description" label="Description" />
        <x-forms.input name="tags" label="Tags (comma seperated)" />

        <x-forms.divider />

        <x-forms.button>Create Board</x-forms.button>
    </x-forms.form>
</x-layout>
