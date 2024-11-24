<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate">
                {{ __('Dashboard') }}
            </h2>
            <div class="mt-2 md:mt-0">
                <p class="text-lg text-indigo-600 font-medium">
                    Welcome, {{ Auth::user()->f_name }} {{ Auth::user()->l_name }}
                </p>
            </div>
        </div>
    </x-slot>
    @can('doctor_access')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700">Doctor Dashboard</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">

                            <!-- Total Patients -->
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                                <h4 class="text-xl font-medium text-gray-700">Total Patients</h4>
                                <p class="text-2xl font-semibold text-gray-800">{{ $totalPatients }}</p>
                            </div>

                            <!-- Completed Appointments -->
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                                <h4 class="text-xl font-medium text-gray-700">Completed Appointments</h4>
                                <p class="text-2xl font-semibold text-gray-800">{{ $completedAppointments }}</p>
                            </div>

                            <!-- Appointments Today -->
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                                <h4 class="text-xl font-medium text-gray-700">Appointments Today</h4>
                                <p class="text-2xl font-semibold text-gray-800">{{ $todayAppointments }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('admin_access')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700">Admin Dashboard</h3>

                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h4 class="text-xl font-semibold text-indigo-700 mb-4 flex items-center">
                                Doctor List
                            </h4>
                            <ul class="space-y-4">
                                @foreach ($doctors as $doctor)
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-lg font-medium text-gray-800">
                                                    {{ $doctor->user->f_name }} {{ $doctor->user->l_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">Speciality:
                                                    {{ $doctor->speciality->name }}</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @foreach ($doctor->appointmentSlots as $slot)
                                                    <p class="mt-1">Date: {{ $slot->date }} | Time:
                                                        {{ $slot->start_time }} - {{ $slot->end_time }} | Status:
                                                        {{ $slot->status }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-6 flex-auto">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Total Patients -->
                                <div class="bg-gray-100 p-4 rounded-lg shadow">
                                    <h4 class="text-xl font-semibold text-gray-600">Total Patients</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ $patients->count() }}</p>
                                </div>

                                <!-- Total Doctors -->
                                <div class="bg-gray-100 p-4 rounded-lg shadow">
                                    <h4 class="text-xl font-semibold text-gray-600">Total Doctors</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ $doctors->count() }}</p>
                                </div>

                                <!-- Total Specialities -->
                                <div class="bg-gray-100 p-4 rounded-lg shadow">
                                    <h4 class="text-xl font-semibold text-gray-600">Total Specialities</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ $speciality->count() }}</p>
                                </div>
                            </div>
                        </div>


                        <div class="mt-8">
                            <!-- Doctors with Appointments Today -->
                            <h4 class="text-lg font-semibold text-gray-700">Doctors with Appointments Today</h4>
                            <ul class="mt-4">
                                @forelse ($appointments as $appointment)
                                    <li class="mb-2">
                                        <span class="font-medium">{{ $appointment->doctor->user->f_name }}
                                            {{ $appointment->doctor->user->l_name }}</span>
                                        ({{ $appointment->doctor->speciality->name }})
                                    </li>
                                    {{-- appointment time --}}
                                    {{ $appointment->date }} {{ $appointment->start_time }} -
                                    {{ $appointment->end_time }}
                                @empty
                                    <li>No appointments today</li>
                                @endforelse
                                {{-- @foreach ($appointments as $appointment)
                                    <li class="mb-2">
                                        <span class="font-medium">{{ $appointment->doctor->user->f_name }}
                                            {{ $appointment->doctor->user->l_name }}</span>
                                        ({{ $appointment->doctor->speciality->name }})
                                    </li>
                                    {{ $appointment->date }} {{ $appointment->start_time }} -
                                    {{ $appointment->end_time }}
                                @endforeach --}}
                            </ul>
                        </div>

                        <div class="mt-8">
                            <!-- Pending Appointments -->
                            <h4 class="text-lg font-semibold text-gray-700">Pending Appointments</h4>
                            <ul class="mt-4">
                                @foreach ($pending_appointments as $appointment)
                                    <li class="mb-2">
                                        Patient: {{ $appointment->patient->user->f_name }}
                                        {{ $appointment->patient->user->l_name }}
                                        - Doctor: {{ $appointment->doctor->user->f_name }}
                                        {{ $appointment->doctor->user->l_name }}
                                        - Date: {{ $appointment->date }} {{ $appointment->start_time }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-8">
                            <!-- Unavailable and Booked Appointment Slots -->
                            <h4 class="text-lg font-semibold text-gray-700">Unavailable and Booked Appointment Slots Status
                            </h4>
                            <ul class="mt-4">
                                @foreach ($pending_appointments as $slot)
                                    <li class="mb-2">
                                        Doctor: {{ $slot->doctor->user->f_name }} {{ $slot->doctor->user->l_name }}
                                        - Date: {{ $slot->date }}
                                        - Time: {{ $slot->start_time }} - {{ $slot->end_time }}
                                        -status: {{ $slot->status }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan


    @can('patient_access')
        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-indigo-100 via-indigo-200 to-indigo-300 border-b border-gray-200">
                        <h3 class="text-2xl font-semibold text-gray-800">Patient Dashboard</h3>
                    </div>

                    <!-- Doctor List Section -->
                    <div class="space-y-8 mt-8 px-6 sm:px-8">
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h4 class="text-xl font-semibold text-indigo-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-700" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2a6 6 0 00-12 0v12a6 6 0 0012 0V2zm-2 10a4 4 0 01-8 0V4a4 4 0 018 0v8z" />
                                </svg>
                                Doctor List
                            </h4>
                            <ul class="space-y-4">
                                @foreach ($doctors as $doctor)
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-lg font-medium text-gray-800">
                                                    {{ $doctor->user->f_name }} {{ $doctor->user->l_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">Speciality:
                                                    {{ $doctor->speciality->name }}</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @foreach ($doctor->appointmentSlots as $slot)
                                                    <p class="mt-1">Date: {{ $slot->date }} | Time:
                                                        {{ $slot->start_time }} - {{ $slot->end_time }} | Status:
                                                        {{ $slot->status }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Appointments Section -->
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h4 class="text-xl font-semibold text-green-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-700" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4 4a4 4 0 018 0v8a4 4 0 01-8 0V4z" clip-rule="evenodd" />
                                </svg>
                                Appointments
                            </h4>
                            <ul class="space-y-4">

                                @forelse ($appointments_booked as $appointment)
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-lg font-medium text-gray-800">
                                                    Dr. {{ $appointment->doctor->user->f_name }}
                                                    {{ $appointment->doctor->user->l_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">Date: {{ $appointment->date }} | Time:
                                                    {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <p class="mt-1">Review:
                                                    {{ $appointment->review ? $appointment->review->comment : 'No comment on this appointment' }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>

                                @empty
                                    <li>No appointments</li>
                                @endforelse


                            </ul>
                        </div>

                        <!-- Completed Appointments Section -->
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h4 class="text-xl font-semibold text-blue-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-700" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M2 7.5C2 5.57 3.57 4 5.5 4h9a3.5 3.5 0 013.5 3.5v7a3.5 3.5 0 01-3.5 3.5H5.5A3.5 3.5 0 012 14.5v-7zM5.5 5a2.5 2.5 0 00-2.5 2.5v7a2.5 2.5 0 002.5 2.5h9a2.5 2.5 0 002.5-2.5v-7a2.5 2.5 0 00-2.5-2.5h-9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Completed Appointments
                            </h4>
                            <ul class="space-y-4">
                                @forelse ($appointments_completed as $appointment)
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-lg font-medium text-gray-800">
                                                    Dr. {{ $appointment->doctor->user->f_name }}
                                                    {{ $appointment->doctor->user->l_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">Date: {{ $appointment->date }} | Time:
                                                    {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <p class="mt-1">Review:
                                                    {{ $appointment->review ? $appointment->review->comment : 'No comment on this appointment' }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md text-gray-500">No completed appointments
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Canceled Appointments Section -->
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <h4 class="text-xl font-semibold text-red-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-700" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 1a9 9 0 11-9 9 9 9 0 019-9zm0 2a7 7 0 107 7 7 7 0 00-7-7z"
                                        clip-rule="evenodd" />
                                </svg>
                                Canceled Appointments
                            </h4>
                            <ul class="space-y-4">
                                @forelse ($appointments_cancled as $appointment)
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-lg font-medium text-gray-800">
                                                    Dr. {{ $appointment->doctor->user->f_name }}
                                                    {{ $appointment->doctor->user->l_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">Date: {{ $appointment->date }} | Time:
                                                    {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <p class="mt-1">Review:
                                                    {{ $appointment->review ? $appointment->review->comment : 'No comment on this appointment' }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="bg-gray-50 p-4 rounded-lg shadow-md text-gray-500">No canceled appointments
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endcan
</x-app-layout>
