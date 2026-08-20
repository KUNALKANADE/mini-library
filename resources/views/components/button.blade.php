@props(['variant' => 'primary', 'href' => null])

@php
$classes = match($variant) {
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-300 focus:ring-offset-1',
    'danger' => 'text-red-600 hover:text-red-800 hover:bg-red-50',
    'secondary' => 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200',
    default => 'bg-indigo-600 text-white hover:bg-indigo-700',
};
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "px-4 py-2 rounded-lg text-sm font-semibold transition $classes"]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => "px-4 py-2 rounded-lg text-sm font-semibold transition $classes"]) }}>
        {{ $slot }}
    </button>
@endif
