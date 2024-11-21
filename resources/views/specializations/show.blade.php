<!-- resources/views/specialities/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Speciality Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class=" flex align-middle">
                    <h3 class="text-lg font-medium">Name:</h3>
                    <h2>{{ $specialization->name }}</h2>
                </div>
                <div class="mt-4">
                    <a href="{{ route('specializations.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
