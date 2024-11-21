<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @canany(['admin_access', 'patient_access'])

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl rounded-lg my-8 p-8">

                    <!-- Button to create new appointment -->
                    <div class="mb-6 flex justify-end">
                        <a href="{{ route('appointments.create') }}"
                            class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
                            Create Appointment
                        </a>
                    </div>

                    <!-- Appointments Table -->
                    <div class="overflow-x-auto bg-white rounded-lg shadow-md ring-1 ring-gray-200">
                        <table class="min-w-full table-auto border-collapse bg-gray-100 rounded-lg">
                            <thead class="bg-indigo-600 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left">Patient Name</th>
                                    <th class="px-6 py-3 text-left">Doctor</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3 text-left">Start Time</th>
                                    <th class="px-6 py-3 text-left">End Time</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Actions</th>
                                    <th class="px-6 py-3 text-left">Update Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800">
                                @forelse ($appointments as $appointment)
                                    <tr class="border-b hover:bg-gray-50 transition duration-200">
                                        <td class="px-6 py-3">{{ $appointment->patient->user->f_name }}
                                            {{ $appointment->patient->user->l_name }}</td>
                                        <td class="px-6 py-3">{{ $appointment->doctor->user->f_name }}
                                            {{ $appointment->doctor->user->l_name }}</td>
                                        <td class="px-6 py-3">{{ $appointment->date }}</td>
                                        <td class="px-6 py-3">{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}</td>
                                        <td class="px-6 py-3">{{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}</td>
                                        <td class="px-6 py-3 capitalize">{{ $appointment->status }}</td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="text-blue-600 hover:text-blue-800 transition-colors">
                                                View
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">
                                            <form action="{{ route('appointments.updateStatus', $appointment->id) }}"
                                                method="POST" class="inline-block ml-2">
                                                @csrf
                                                @method('POST')
                                                <div class="flex items-center space-x-2">
                                                    <select name="status"
                                                        class="px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                                                        <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="booked" {{ $appointment->status == 'booked' ? 'selected' : '' }}>Booked</option>
                                                        <option value="rescheduled" {{ $appointment->status == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                                        <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No Appointments</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endcan

    @can('doctor_access')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($appointments as $appointment)
                        <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition duration-300">
                            <div class="text-xl font-semibold mb-4">Appointment #{{ $appointment->id }}</div>
                            <div class="mb-2">
                                <span class="font-bold">Patient:</span> {{ $appointment->patient->user->f_name }}
                                {{ $appointment->patient->user->l_name }}
                            </div>
                            <div class="mb-2">
                                <span class="font-bold">Doctor:</span> {{ $appointment->doctor->user->f_name }}
                                {{ $appointment->doctor->user->l_name }}
                            </div>
                            <div class="mb-2">
                                <span class="font-bold">Date:</span> {{ $appointment->date }}
                            </div>
                            <div class="mb-2">
                                <span class="font-bold">Start Time:</span> {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                            </div>
                            <div class="mb-2">
                                <span class="font-bold">End Time:</span> {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
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

    @if (session('status'))
        <div class="bg-green-500 text-white p-4 rounded-md mb-6 shadow-lg transition-all">
            {{ session('status') }}
        </div>
    @endif

</x-app-layout>
