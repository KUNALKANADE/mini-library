<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Category</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-forms.text-input label="Name" name="name" :value="$category->name" />

                    <div class="flex justify-end space-x-2">
                        <x-button variant="secondary" href="{{ route('categories.index') }}">Cancel</x-button>
                        <x-button type="submit">Update</x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
