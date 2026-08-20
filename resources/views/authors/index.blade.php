<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Authors</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-end mb-4">
                    <x-button href="{{ route('authors.create') }}">+ New Author</x-button>
                </div>

                @if ($authors->isEmpty())
                    <x-empty-state message="No authors yet.">
                        <x-slot name="action">
                            <x-button href="{{ route('authors.create') }}">+ New Author</x-button>
                        </x-slot>
                    </x-empty-state>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Name</th>
                                <th class="py-2">Books</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($authors as $author)
                                <tr class="border-b">
                                    <td class="py-2">{{ $author->name }}</td>
                                    <td class="py-2">{{ $author->books_count }}</td>
                                    <td class="py-2 space-x-2">
                                        <x-button variant="secondary" href="{{ route('authors.show', $author) }}">View</x-button>
                                        <x-button variant="secondary" href="{{ route('authors.edit', $author) }}">Edit</x-button>
                                        <form action="{{ route('authors.destroy', $author) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete this author?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button variant="danger" type="submit">Delete</x-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $authors->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
