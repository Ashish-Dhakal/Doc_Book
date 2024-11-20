<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Appointment Details Container -->
            <div class="bg-white p-8 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Appointment Details</h2>

                <!-- Appointment Details Table -->
                <table class="min-w-full table-auto border-separate border-spacing-4">
                    <tr>
                        <th class="text-left font-medium text-gray-700">Patient</th>
                        <td class="text-gray-800">{{ $appointment->patient->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Doctor</th>
                        <td class="text-gray-800">{{ $appointment->doctor->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Date</th>
                        <td class="text-gray-800">{{ $appointment->date }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Time</th>
                        <td class="text-gray-800">{{ $appointment->time }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Status</th>
                        <td class="text-gray-800 capitalize">{{ ucfirst($appointment->status) }}</td>
                    </tr>
                </table>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Edit
                    </a>
                    <a href="{{ route('appointments.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Back to Appointments
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
