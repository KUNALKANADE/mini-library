<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Book</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('books.update', $book) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-forms.text-input label="Title" name="title" :value="$book->title" />
                    <x-forms.text-input label="ISBN" name="isbn" :value="$book->isbn" />
                    <x-forms.textarea label="Description" name="description" :value="$book->description" />
                    <x-forms.text-input label="Total Copies" name="total_copies" type="number" :value="$book->total_copies" />
                    <p class="text-xs text-gray-500 -mt-3 mb-4">Currently {{ $book->available_copies }} available.</p>
                    <x-forms.select label="Author" name="author_id" :options="$authors" :selected="$book->author_id" />
                    <x-forms.select label="Category" name="category_id" :options="$categories" :selected="$book->category_id" />

                    <div class="flex justify-end space-x-2">
                        <x-button variant="secondary" href="{{ route('books.index') }}">Cancel</x-button>
                        <x-button type="submit">Update</x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
