<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Book</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('books.store') }}" method="POST">
                    @csrf

                    <x-forms.text-input label="Title" name="title" />
                    <x-forms.text-input label="ISBN" name="isbn" />
                    <x-forms.textarea label="Description" name="description" />
                    <x-forms.text-input label="Total Copies" name="total_copies" type="number" :value="1" />
                    <x-forms.select label="Author" name="author_id" :options="$authors" />
                    <x-forms.select label="Category" name="category_id" :options="$categories" />

                    <div class="flex justify-end space-x-2">
                        <x-button variant="secondary" href="{{ route('books.index') }}">Cancel</x-button>
                        <x-button type="submit">Create</x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
