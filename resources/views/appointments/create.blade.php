<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="container p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Appointment</h2>

                    <form action="{{ route('appointments.store') }}" method="POST">
                        @csrf

                        @can('admin_access')
                            <!-- Patient Dropdown -->
                            <div class="mb-4">
                                <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                                <select name="patient_id" id="patient_id"
                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select a Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->user->f_name }}
                                            {{ $patient->user->l_name }}</option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        @endcan

                        <!-- Doctor Dropdown -->
                        <div class="mb-4">
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                            <select name="doctor_id" id="doctor_id"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select a Doctor</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->user->f_name }}
                                        {{ $doctor->user->l_name }}</option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date Input -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Appointment Date</label>
                            <input type="date" name="date" id="date"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            @error('date')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Start Time Input -->
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" name="start_time" id="start_time"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                            @error('start_time')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Time Input -->
                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" name="end_time" id="end_time"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                            @error('end_time')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Create Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
