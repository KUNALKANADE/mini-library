<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $category->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="font-semibold text-lg mb-2">Books in this category</h3>
                @if ($category->books->isEmpty())
                    <p class="text-gray-500">No books yet.</p>
                @else
                    <ul class="list-disc list-inside">
                        @foreach ($category->books as $book)
                            <li>
                                <a href="{{ route('books.show', $book) }}" class="text-indigo-600">{{ $book->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-6">
                    <a href="{{ route('categories.index') }}" class="text-gray-600">&larr; Back to Categories</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
