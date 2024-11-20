<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg my-8 p-8">
                <!-- Button to create new appointment -->
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('appointments.create') }}"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Create Appointment
                    </a>
                </div>

                <!-- Appointments Table -->
                <table class="min-w-full table-auto border-collapse bg-gray-100 rounded-lg shadow-md">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">Patient Name</th>
                            <th class="px-6 py-3 text-left">Doctor</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Start Time</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse ($appointments as $appointment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $appointment->patient->user->f_name }}
                                    {{ $appointment->patient->user->l_name }}</td>
                                <td class="px-6 py-3">{{ $appointment->doctor->user->f_name }}
                                    {{ $appointment->doctor->user->l_name }}</td>
                                <td class="px-6 py-3">{{ $appointment->date }}</td>
                                <td class="px-6 py-3">{{ $appointment->time }}</td>
                                <td class="px-6 py-3 capitalize">{{ $appointment->status }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('appointments.show', $appointment->id) }}"
                                        class="text-blue-600 hover:text-blue-800 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No Appointments</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
