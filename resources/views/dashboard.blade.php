<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600 mb-1">Overview</p>
        <h2 class="font-bold text-2xl text-slate-900">
            Welcome back, {{ explode(' ', auth()->user()->name)[0] }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $activeLoans = auth()->user()->loans()->where('status', '!=', 'returned')->count();
                $activeReservations = auth()->user()->reservations()->where('status', 'active')->count();
                $totalBooks = \App\Models\Book::count();
                $availableBooks = \App\Models\Book::where('available_copies', '>', 0)->count();
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">My Active Loans</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $activeLoans }}</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">My Reservations</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $activeReservations }}</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Books in Catalog</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $totalBooks }}</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Available Now</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $availableBooks }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <a href="{{ route('books.index') }}" class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5 hover:border-indigo-300 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center mb-3 group-hover:bg-indigo-100 transition">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900">Browse Books</p>
                    <p class="text-sm text-slate-500 mt-0.5">Search the full catalog</p>
                </a>

                <a href="{{ route('loans.index') }}" class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5 hover:border-indigo-300 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center mb-3 group-hover:bg-indigo-100 transition">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900">My Loans</p>
                    <p class="text-sm text-slate-500 mt-0.5">Track due dates &amp; returns</p>
                </a>

                <a href="{{ route('reservations.index') }}" class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-5 hover:border-indigo-300 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center mb-3 group-hover:bg-indigo-100 transition">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900">My Reservations</p>
                    <p class="text-sm text-slate-500 mt-0.5">See your place in queue</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
