<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Loan Details</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-semibold mb-2">{{ $loan->book->title }}</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd><x-status-badge :status="$loan->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Borrowed</dt>
                        <dd>{{ $loan->borrowed_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Due</dt>
                        <dd>{{ $loan->due_at->format('M j, Y') }}</dd>
                    </div>
                    @if ($loan->returned_at)
                        <div>
                            <dt class="text-sm text-gray-500">Returned</dt>
                            <dd>{{ $loan->returned_at->format('M j, Y') }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6">
                    <a href="{{ route('loans.index') }}" class="text-gray-600">&larr; Back to My Loans</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
