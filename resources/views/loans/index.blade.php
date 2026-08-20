<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Loans</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($loans->isEmpty())
                    <x-empty-state message="You haven't borrowed any books yet." />
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Book</th>
                                <th class="py-2">Borrowed</th>
                                <th class="py-2">Due</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loans as $loan)
                                <tr class="border-b">
                                    <td class="py-2">
                                        <a href="{{ route('books.show', $loan->book) }}" class="text-indigo-600">
                                            {{ $loan->book->title }}
                                        </a>
                                    </td>
                                    <td class="py-2">{{ $loan->borrowed_at->format('M j, Y') }}</td>
                                    <td class="py-2">{{ $loan->due_at->format('M j, Y') }}</td>
                                    <td class="py-2"><x-status-badge :status="$loan->status" /></td>
                                    <td class="py-2">
                                        @if ($loan->status !== 'returned')
                                            <form action="{{ route('loans.return', $loan) }}" method="POST">
                                                @csrf
                                                <x-button type="submit" variant="secondary">Return</x-button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $loans->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
