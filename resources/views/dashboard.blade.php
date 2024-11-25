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

        <div class="min-vh-100 bg-light py-4">
            <div class="container">
                <div class="card border-0 shadow-lg rounded-3">
                    <!-- Dashboard Header -->
                    <div class="card-header border-0 p-4 bg-gradient text-white d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hospital fs-3 me-3"></i>
                            <h3 class="m-0 fw-bold">Patient Dashboard</h3>
                        </div>
                        <button type="button" class="btn btn-light btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>New Appointment
                        </button>
                    </div>

                    <div class="card-body p-4">
                        <!-- Doctor List Section -->
                        <div class="mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom border-2 p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people-fill fs-4 text-primary me-2"></i>
                                        <h5 class="m-0 fw-bold text-primary">Available Doctors</h5>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($doctors as $doctor)
                                        <li class="list-group-item p-3 hover-bg-light">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                                            <i class="bi bi-person-circle fs-4 text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1 fw-bold">Dr. {{ $doctor->user->f_name }}
                                                                {{ $doctor->user->l_name }}</h6>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                                {{ $doctor->speciality->name }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="row g-2">
                                                        @foreach ($doctor->appointmentSlots as $slot)
                                                            <div class="col-md-6">
                                                                <div class="card border border-light bg-light">
                                                                    <div class="card-body p-2">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <i
                                                                                class="bi bi-calendar2-date text-primary me-2"></i>
                                                                            <small
                                                                                class="fw-semibold">{{ $slot->date }}</small>
                                                                        </div>
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <i class="bi bi-clock text-primary me-2"></i>
                                                                            <small>{{ $slot->start_time }} -
                                                                                {{ $slot->end_time }}</small>
                                                                        </div>
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="bi bi-circle-fill text-{{ $slot->status === 'available' ? 'success' : 'danger' }} me-2"
                                                                                style="font-size: 8px;"></i>
                                                                            <small
                                                                                class="text-capitalize">{{ $slot->status }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Appointments Grid -->
                        <div class="row g-4">
                            <!-- Current Appointments -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header border-0 bg-success bg-gradient p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar2-check fs-4 text-white me-2"></i>
                                            <h5 class="m-0 text-white fw-bold">Current Appointments</h5>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($appointments_booked as $appointment)
                                                <li class="list-group-item p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                                            <i class="bi bi-person-circle fs-5 text-success"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bold">Dr.
                                                            {{ $appointment->doctor->user->f_name }}
                                                            {{ $appointment->doctor->user->l_name }}</h6>
                                                    </div>
                                                    <div class="ms-5">
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-calendar2-date text-success me-2"></i>
                                                            {{ $appointment->date }}
                                                        </p>
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-clock text-success me-2"></i>
                                                            {{ $appointment->start_time }} - {{ $appointment->end_time }}
                                                        </p>
                                                        @if ($appointment->review)
                                                            <div class="bg-light rounded p-2 mt-2">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-chat-quote me-2"></i>
                                                                    {{ $appointment->review->comment }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item p-4 text-center text-muted">
                                                    <i class="bi bi-calendar2-x fs-1"></i>
                                                    <p class="mt-2 mb-0">No current appointments</p>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Completed Appointments -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header border-0 bg-primary bg-gradient p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check2-circle fs-4 text-white me-2"></i>
                                            <h5 class="m-0 text-white fw-bold">Completed Appointments</h5>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($appointments_completed as $appointment)
                                                <li class="list-group-item p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                                            <i class="bi bi-person-circle fs-5 text-primary"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bold">Dr.
                                                            {{ $appointment->doctor->user->f_name }}
                                                            {{ $appointment->doctor->user->l_name }}</h6>
                                                    </div>
                                                    <div class="ms-5">
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-calendar2-date text-primary me-2"></i>
                                                            {{ $appointment->date }}
                                                        </p>
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-clock text-primary me-2"></i>
                                                            {{ $appointment->start_time }} - {{ $appointment->end_time }}
                                                        </p>
                                                        @if ($appointment->review)
                                                            <div class="bg-light rounded p-2 mt-2">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-chat-quote me-2"></i>
                                                                    {{ $appointment->review->comment }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item p-4 text-center text-muted">
                                                    <i class="bi bi-calendar2-x fs-1"></i>
                                                    <p class="mt-2 mb-0">No completed appointments</p>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Canceled Appointments -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header border-0 bg-danger bg-gradient p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-x-circle fs-4 text-white me-2"></i>
                                            <h5 class="m-0 text-white fw-bold">Canceled Appointments</h5>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($appointments_cancled as $appointment)
                                                <li class="list-group-item p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                                                            <i class="bi bi-person-circle fs-5 text-danger"></i>
                                                        </div>
                                                        <h6 class="mb-0 fw-bold">Dr.
                                                            {{ $appointment->doctor->user->f_name }}
                                                            {{ $appointment->doctor->user->l_name }}</h6>
                                                    </div>
                                                    <div class="ms-5">
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-calendar2-date text-danger me-2"></i>
                                                            {{ $appointment->date }}
                                                        </p>
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-clock text-danger me-2"></i>
                                                            {{ $appointment->start_time }} - {{ $appointment->end_time }}
                                                        </p>
                                                        @if ($appointment->review)
                                                            <div class="bg-light rounded p-2 mt-2">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-chat-quote me-2"></i>
                                                                    {{ $appointment->review->comment }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item p-4 text-center text-muted">
                                                    <i class="bi bi-calendar2-x fs-1"></i>
                                                    <p class="mt-2 mb-0">No canceled appointments</p>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    @endcan
</x-app-layout>
