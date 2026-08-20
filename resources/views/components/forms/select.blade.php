@props(['label', 'name', 'options', 'selected' => null])

<div class="mb-4">
    <label for="{{ $name }}" class="block font-medium text-sm text-gray-700">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'mt-1 block w-full border-gray-300 rounded-md shadow-sm']) }}
    >
        <option value="">-- Select {{ $label }} --</option>
        @foreach ($options as $option)
            <option value="{{ $option->id }}" @selected(old($name, $selected) == $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
