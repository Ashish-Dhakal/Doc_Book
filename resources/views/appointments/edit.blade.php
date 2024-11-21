<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Appointment</h2>

                <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Patient Field -->
                    <div class="mb-4">
                        <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                        <input type="text" id="patient_id" name="patient_id" value="{{ $appointment->patient->user->f_name }} {{ $appointment->patient->user->l_name }}"
                            class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-4 text-gray-700" readonly>
                        <input type="hidden" name="patient_id" value="{{ $appointment->patient->id }}">
                        @error('patient_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor Field -->
                    <div class="mb-4">
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="mt-1 block w-full bg-white border border-gray-300 rounded-md py-2 px-4 text-gray-700">
                            <option value="{{ $appointment->doctor_id }}" selected>
                                {{ $appointment->doctor->user->f_name }} {{ $appointment->doctor->user->l_name }}
                            </option>
                            @foreach ($doctors as $doctor)
                                @if ($doctor->id !== $appointment->doctor_id)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->user->f_name }} {{ $doctor->user->l_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Field -->
                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700">Appointment Date</label>
                        <input type="date" name="date" id="date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-4 text-gray-700" value="{{ $appointment->date }}" required>
                        @error('date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Time Field -->
                    <div class="mb-4">
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Appointment Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-4 text-gray-700" value="{{ $appointment->start_time }}" required>
                        @error('start_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Time Field -->
                    <div class="mb-4">
                        <label for="end_time" class="block text-sm font-medium text-gray-700">Appointment End Time</label>
                        <input type="time" name="end_time" id="end_time" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-4 text-gray-700" value="{{ $appointment->end_time }}" required>
                        @error('end_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full mt-4 py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        Update Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
