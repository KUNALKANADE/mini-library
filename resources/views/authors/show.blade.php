<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $author->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-gray-700 mb-6">{{ $author->bio ?? 'No bio available.' }}</p>

                <h3 class="font-semibold text-lg mb-2">Books by this author</h3>
                @if ($author->books->isEmpty())
                    <p class="text-gray-500">No books yet.</p>
                @else
                    <ul class="list-disc list-inside">
                        @foreach ($author->books as $book)
                            <li>
                                <a href="{{ route('books.show', $book) }}" class="text-indigo-600">{{ $book->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-6">
                    <a href="{{ route('authors.index') }}" class="text-gray-600">&larr; Back to Authors</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
