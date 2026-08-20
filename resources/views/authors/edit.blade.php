<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Author</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('authors.update', $author) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-forms.text-input label="Name" name="name" :value="$author->name" />
                    <x-forms.textarea label="Bio" name="bio" :value="$author->bio" />

                    <div class="flex justify-end space-x-2">
                        <x-button variant="secondary" href="{{ route('authors.index') }}">Cancel</x-button>
                        <x-button type="submit">Update</x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
