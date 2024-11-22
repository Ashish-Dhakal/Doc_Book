<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Speciality Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-lg p-8">
                <!-- Specialization Name -->
                <div class="flex items-center space-x-4 mb-6">
                    <h3 class="text-xl font-semibold text-gray-700">Specialty Name:</h3>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $specialization->name }}</h2>
                </div>

                <!-- Doctors List -->
                <div class="mt-6">
                    <h4 class="text-lg font-medium text-gray-700 mb-4">Doctors in this Specialty:</h4>
                    @forelse ($doctors as $doctor)
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="text-gray-900 font-semibold">{{ $doctor->user->f_name }}
                                {{ $doctor->user->l_name }}</span>
                        </div>
                    @empty
                        <p class="text-gray-600">No doctors available for this specialty.</p>
                    @endforelse
                </div>

                <!-- Back Button -->
                <div class="mt-6">
                    <a href="{{ route('specializations.index') }}"
                        class="inline-block bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-300">Back
                        to Specializations</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
