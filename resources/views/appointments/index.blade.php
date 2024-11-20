<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @canany(['admin_access', 'patient_access'])


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
                                <th class="px-6 py-3 text-left">End Time</th>
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
                                    <td class="px-6 py-3">{{ $appointment->start_time }}</td>
                                    <td class="px-6 py-3">{{ $appointment->end_time }}</td>
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
    @endcan

    @can('doctor_access')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($appointments as $appointment)
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <div class="text-xl font-semibold mb-4">Appointment #{{ $appointment->id }}</div>
                        <div class="mb-2">
                            <span class="font-bold">Patient ID:</span> {{ $appointment->patient->user->f_name }} {{ $appointment->patient->user->l_name }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">Doctor ID:</span> {{ $appointment->doctor->user->f_name }} {{ $appointment->doctor->user->l_name }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">Date:</span> {{ $appointment->date }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">Start Time:</span> {{ $appointment->start_time }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">End Time:</span> {{ $appointment->end_time }}
                        </div>
                        <div class="mb-4">
                            <span class="font-bold">Status:</span> {{ $appointment->status }}
                        </div>
                        <a href="{{ route('appointments.show', $appointment->id) }}" 
                           class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                            View Appointment
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endcan
    

</x-app-layout>
