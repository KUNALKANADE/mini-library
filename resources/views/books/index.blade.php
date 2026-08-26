<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Books</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter bar -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                <form method="GET" action="{{ route('books.index') }}"
                      class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Search
                        </label>

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by title..."
                               class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Author
                        </label>

                        <select name="author_id"
                                onchange="this.form.submit()"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>

                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    @selected(request('author_id') == $author->id)>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Category
                        </label>

                        <select name="category_id"
                                onchange="this.form.submit()"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(request('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Availability
                        </label>

                        <select name="availability"
                                onchange="this.form.submit()"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>

                            <option value="available"
                                @selected(request('availability') === 'available')>
                                Available
                            </option>

                            <option value="unavailable"
                                @selected(request('availability') === 'unavailable')>
                                Unavailable
                            </option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <select name="sort"
                                onchange="this.form.submit()"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="title"
                                @selected(request('sort', 'title') === 'title')>
                                Title
                            </option>

                            <option value="author"
                                @selected(request('sort') === 'author')>
                                Author
                            </option>

                            <option value="created_at"
                                @selected(request('sort') === 'created_at')>
                                Date Added
                            </option>
                        </select>

                        <select name="direction"
                                onchange="this.form.submit()"
                                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="asc"
                                @selected(request('direction', 'asc') === 'asc')>
                                Asc
                            </option>

                            <option value="desc"
                                @selected(request('direction') === 'desc')>
                                Desc
                            </option>
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-3 lg:col-span-6 flex justify-end gap-2 pt-1">

                        @if (request()->anyFilled([
                            'search',
                            'author_id',
                            'category_id',
                            'availability'
                        ]))
                            <a href="{{ route('books.index') }}"
                               class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">
                                Clear filters
                            </a>
                        @endif

                        <x-button type="submit">
                            Search
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Table card -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">

                <!-- Table header -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">

                    <p class="text-sm text-gray-500">
                        {{ $books->total() }}
                        {{ Str::plural('book', $books->total()) }}
                        found
                    </p>

                    @can('create', App\Models\Book::class)
                        <div class="flex items-center gap-2">

                            <!-- Import Books -->
                            <form action="{{ route('books.import') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="flex items-center gap-2">

                                @csrf

                                <input type="file"
                                       name="file"
                                       accept=".xlsx,.xls,.csv"
                                       required
                                       class="text-sm border border-slate-200 rounded-lg px-3 py-1.5">

                                <x-button type="submit" variant="secondary">
                                    Import
                                </x-button>
                            </form>

                            <!-- Create New Book -->
                            <x-button href="{{ route('books.create') }}">
                                + New Book
                            </x-button>

                        </div>
                    @endcan
                </div>

                @if ($books->isEmpty())

                    <x-empty-state message="No books match your filters.">
                        <x-slot name="action">
                            <a href="{{ route('books.index') }}"
                               class="text-indigo-600 text-sm font-medium hover:text-indigo-800">
                                Clear filters
                            </a>
                        </x-slot>
                    </x-empty-state>

                @else

                    <table class="w-full text-left">

                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                <th class="px-6 py-3">Title</th>
                                <th class="px-6 py-3">Author</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3">Available</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach ($books as $book)

                                <tr class="hover:bg-gray-50 transition-colors">

                                    <!-- Title -->
                                    <td class="px-6 py-3 font-medium text-gray-900">
                                        <a href="{{ route('books.show', $book) }}"
                                           class="hover:text-indigo-600">
                                            {{ $book->title }}
                                        </a>
                                    </td>

                                    <!-- Author -->
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $book->author->name }}
                                    </td>

                                    <!-- Category -->
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $book->category->name }}
                                    </td>

                                    <!-- Availability -->
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">

                                            <x-status-badge
                                                :status="$book->isAvailable() ? 'active' : 'overdue'" />

                                            <span class="text-sm text-gray-500">
                                                {{ $book->available_copies }}
                                                /
                                                {{ $book->total_copies }}
                                            </span>

                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-3">

                                        <div class="flex justify-end gap-3 text-sm">

                                            <!-- View -->
                                            <a href="{{ route('books.show', $book) }}"
                                               class="text-gray-500 hover:text-gray-800">
                                                View
                                            </a>

                                            <!-- Edit -->
                                            @can('update', $book)
                                                <a href="{{ route('books.edit', $book) }}"
                                                   class="text-indigo-600 hover:text-indigo-800">
                                                    Edit
                                                </a>
                                            @endcan

                                            <!-- Delete -->
                                            @can('delete', $book)

                                                <form action="{{ route('books.destroy', $book) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete this book?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-500 hover:text-red-700">
                                                        Delete
                                                    </button>

                                                </form>

                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $books->links() }}
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>
