@props(['status'])

@php
$classes = match($status) {
    'active' => 'bg-green-100 text-green-800',
    'overdue' => 'bg-red-100 text-red-800',
    'returned' => 'bg-gray-100 text-gray-800',
    'fulfilled' => 'bg-blue-100 text-blue-800',
    'cancelled' => 'bg-gray-100 text-gray-500',
    default => 'bg-gray-100 text-gray-800',
};
@endphp

<span class="px-2 py-1 text-xs font-medium rounded-full {{ $classes }}">
    {{ ucfirst($status) }}
</span>
