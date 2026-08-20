<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Reservations</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($reservations->isEmpty())
                    <x-empty-state message="You have no reservations." />
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Book</th>
                                <th class="py-2">Reserved On</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr class="border-b">
                                    <td class="py-2">
                                        <a href="{{ route('books.show', $reservation->book) }}" class="text-indigo-600">
                                            {{ $reservation->book->title }}
                                        </a>
                                    </td>
                                    <td class="py-2">{{ $reservation->created_at->format('M j, Y') }}</td>
                                    <td class="py-2"><x-status-badge :status="$reservation->status" /></td>
                                    <td class="py-2">
                                        @if ($reservation->status === 'active')
                                            <form action="{{ route('reservations.cancel', $reservation) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="danger" type="submit">Cancel</x-button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
