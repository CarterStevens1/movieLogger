@props(['board'])

<x-layout>
    <x-page-heading>Edit board: {{ $board->name }} </x-page-heading>


    <x-forms.form id="update" method="POST" action="/my-boards/{{ $board->id }}/edit" enctype="multipart/form-data">
        <x-forms.input name="name" label="Board name" value="{{ $board->name }}" />
        <x-forms.input name="description" label="Description" value="{{ $board->description }}" />
        <x-forms.input name="tags" label="Tags (comma seperated)" value="{{ $board->tags }}" />

        <x-forms.divider />

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('Update board details successfully.') }}
            </div>
        @endif
    </x-forms.form>
    <div class="flex justify-between max-w-2xl mx-auto space-y-6">
        <x-forms.button form="update">Save</x-forms.button>

        {{-- Get confirmation to deleten before deleting --}}
        <form id="delete" method="POST" action="/my-boards/{{ $board->id }}/delete">
            @csrf
            @method('POST')
            <button form="delete" class="flex gap-2 mx-0 bg-red-500 rounded py-2 px-6 font-bold cursor-pointer">
                <x-bin />
                Delete board</button>
        </form>

    </div>
    <div class="pt-6">
        <x-page-heading>Share board</x-page-heading>


        <x-forms.form id="share" method="POST" action="/my-boards/{{ $board->id }}/share"
            enctype="multipart/form-data">
            @csrf
            <x-forms.input name="email" label="Share Email" type="email" />

            <x-forms.divider />

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('Shared email successfully.') }}
                </div>
            @endif
            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
        </x-forms.form>
        <div class="flex justify-between max-w-2xl mx-auto space-y-6">
            <x-forms.button form="share">Share</x-forms.button>
        </div>
    </div>

    <!-- Add list of shared users here -->

</x-layout>
