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
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" transform=""
                    id="injected-svg">
                    <path
                        d="M17 6V4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v2H2v2h2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8h2V6zM9 4h6v2H9zM6 20V8h12v12z">
                    </path>
                    <path d="M9 10h2v8H9zM13 10h2v8h-2z"></path>
                </svg>
                Delete board</button>
        </form>

    </div>


</x-layout>
