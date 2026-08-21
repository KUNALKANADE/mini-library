<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Book Information --}}
                <dl class="grid grid-cols-2 gap-4 mb-6">

                    {{-- Author --}}
                    <div>
                        <dt class="text-sm text-gray-500">
                            Author
                        </dt>

                        <dd>
                            <a
                                href="{{ route('authors.show', $book->author) }}"
                                class="text-indigo-600 hover:text-indigo-800"
                            >
                                {{ $book->author->name }}
                            </a>
                        </dd>
                    </div>

                    {{-- Category --}}
                    <div>
                        <dt class="text-sm text-gray-500">
                            Category
                        </dt>

                        <dd>
                            <a
                                href="{{ route('categories.show', $book->category) }}"
                                class="text-indigo-600 hover:text-indigo-800"
                            >
                                {{ $book->category->name }}
                            </a>
                        </dd>
                    </div>

                    {{-- ISBN --}}
                    <div>
                        <dt class="text-sm text-gray-500">
                            ISBN
                        </dt>

                        <dd>
                            {{ $book->isbn ?? '—' }}
                        </dd>
                    </div>

                    {{-- Availability --}}
                    <div>
                        <dt class="text-sm text-gray-500">
                            Availability
                        </dt>

                        <dd>
                            {{ $book->available_copies }}
                            /
                            {{ $book->total_copies }}
                        </dd>
                    </div>

                </dl>


                {{-- Borrow / Reservation Section --}}
                @if ($book->isAvailable())

                    {{-- Book is available --}}
                    <form
                        action="{{ route('loans.borrow', $book) }}"
                        method="POST"
                        class="mt-4 mb-6"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-black font-semibold px-4 py-2 rounded"
                        >
                            Borrow this book
                        </button>
                    </form>

                @else

                    {{-- Book is unavailable --}}

                    @php
                        $userReservation = $book->reservations()
                            ->where('user_id', auth()->id())
                            ->where('status', 'active')
                            ->first();

                        $waitingCount = $book->reservations()
                            ->where('status', 'active')
                            ->count();
                    @endphp

                    <div class="mt-4 mb-6">

                        {{-- Waiting message --}}
                        <p class="text-sm text-gray-500 mb-3">
                            No copies currently available.

                            @if ($waitingCount > 0)
                                {{ $waitingCount }} member(s) waiting.
                            @else
                                No members currently waiting.
                            @endif
                        </p>


                        {{-- User already has reservation --}}
                        @if ($userReservation)

                            <form
                                action="{{ route('reservations.cancel', $userReservation) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="bg-gray-600 hover:bg-gray-700 text-black font-semibold px-4 py-2 rounded"
                                >
                                    Cancel my reservation
                                </button>
                            </form>

                        {{-- User does not have reservation --}}
                        @else

                            <form
                                action="{{ route('reservations.reserve', $book) }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-black font-

                                    mibold px-4 py-2 rounded"
                                >
                                    Reserve this book
                                </button>
                            </form>

                        @endif

                    </div>

                @endif


                {{-- Description --}}
                <p class="text-gray-700 mb-6">
                    {{ $book->description ?? 'No description available.' }}
                </p>


                {{-- Back to Books --}}
                <a
                    href="{{ route('books.index') }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    &larr; Back to Books
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
