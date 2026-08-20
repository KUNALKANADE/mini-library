@props(['message' => 'Nothing here yet.'])

<div class="text-center py-12">
    <p class="text-gray-500">{{ $message }}</p>
    @if (isset($action))
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>
