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
            <button onclick="return confirm('Are you sure you want to delete the board?')" form="delete"
                class="flex gap-2 mx-0 bg-red-500 rounded py-2 px-6 font-bold cursor-pointer">
                <x-svgs.bin />
                Delete board</button>
        </form>

    </div>
    <div class="pt-6">
        <x-page-heading>Share board</x-page-heading>


        <x-forms.form id="share" method="POST" action="{{ route('boards.share', $board->id) }}"
            enctype="multipart/form-data">
            @csrf
            <x-forms.input name="email" label="Share Email" type="email" />
            <x-forms.select name="permission" label="Permission">
                <option value="" disabled selected>Select permission</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </x-forms.select>
            <x-forms.divider />
        </x-forms.form>
        <div class="flex justify-between max-w-2xl mx-auto space-y-6">
            <x-forms.button form="share">Share</x-forms.button>
        </div>


    </div>
    <!-- Add list of shared users here -->
    @if (count($board->sharedUsers) > 0)
        <div class="flex flex-col gap-4 max-w-2xl mx-auto pt-12">
            <x-page-heading>Shared with</x-page-heading>
            @foreach ($board->sharedUsers as $user)
                <div class="flex justify-between gap-2 items-center">
                    <p class="font-medium text-lg">
                        {{ $user->name }} | {{ $user->email }}
                    </p>
                    <div>
                        <x-forms.form id="unshare" method="POST" action="{{ route('boards.unshare', $board->id) }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <x-forms.button onclick="return confirm('Are you sure you want to unshare this board?')"
                                form="unshare"
                                class="flex gap-2 mx-0 bg-red-500 rounded py-2 px-6 font-bold cursor-pointer">
                                X</x-forms.button>

                        </x-forms.form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <div class="flex flex-col gap-4 max-w-2xl mx-auto pt-12">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-success text-red-500">
                {{ session('error') }}
            </div>
        @endif
    </div>
</x-layout>
