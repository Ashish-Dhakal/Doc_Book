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
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="h4 text-secondary mb-4">Doctor Dashboard</h3>

                            <div class="row g-4">
                                <!-- Total Patients -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <h4 class="h5 text-secondary">Total Patients</h4>
                                            <p class="display-6 mb-0">{{ $totalPatients }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Appointments -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <h4 class="h5 text-secondary">Completed Appointments</h4>
                                            <p class="display-6 mb-0">{{ $completedAppointments }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appointments Today -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <h4 class="h5 text-secondary">Appointments Today</h4>
                                            <p class="display-6 mb-0">{{ $todayAppointments }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('admin_access')
        <div class="py-5 bg-light min-vh-100">
            <div class="container">
                <div class="card border-0 shadow-lg rounded-3">
                    <div class="card-body p-4">
                        <!-- Dashboard Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-primary fw-bold mb-0">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Admin Dashboard
                            </h3>
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Add New Doctor
                            </button>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-primary bg-opacity-10 h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle bg-primary p-3 me-3">
                                                <i class="bi bi-people-fill text-white fs-4"></i>
                                            </div>
                                            <h4 class="fw-bold text-primary mb-0">Total Patients</h4>
                                        </div>
                                        <h2 class="display-4 fw-bold text-primary mb-0">{{ $patients->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-success bg-opacity-10 h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle bg-success p-3 me-3">
                                                <i class="bi bi-hospital-fill text-white fs-4"></i>
                                            </div>
                                            <h4 class="fw-bold text-success mb-0">Total Doctors</h4>
                                        </div>
                                        <h2 class="display-4 fw-bold text-success mb-0">{{ $doctors->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-info bg-opacity-10 h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle bg-info p-3 me-3">
                                                <i class="bi bi-clipboard2-pulse-fill text-white fs-4"></i>
                                            </div>
                                            <h4 class="fw-bold text-info mb-0">Total Specialities</h4>
                                        </div>
                                        <h2 class="display-4 fw-bold text-info mb-0">{{ $speciality->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor List -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white p-4 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-people me-2"></i>
                                        Doctor List
                                    </h4>
                                    <div class="input-group w-auto">
                                        <input type="text" id="doctorSearch" class="form-control"
                                            placeholder="Search doctors...">
                                        <button class="btn btn-primary" id="searchButton">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="px-4">Doctor Name</th>
                                                <th>Speciality</th>
                                                <th>Appointment Slots</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="doctorList">
                                            @foreach ($doctors as $doctor)
                                                <tr>
                                                    <td class="px-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                                                <i class="bi bi-person-circle text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0 fw-bold">{{ $doctor->user->f_name }}
                                                                    {{ $doctor->user->l_name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            {{ $doctor->speciality->name }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @foreach ($doctor->appointmentSlots as $slot)
                                                            <div class="mb-1 small">
                                                                <span class="text-muted">{{ $slot->date }}</span> |
                                                                <span class="fw-medium">{{ $slot->start_time }} -
                                                                    {{ $slot->end_time }}</span> |
                                                                <span
                                                                    class="badge bg-{{ $slot->status === 'available' ? 'success' : 'danger' }}">
                                                                    {{ $slot->status }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Today's Appointments -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white border-0 p-4">
                                        <h4 class="fw-bold text-success mb-0">
                                            <i class="bi bi-calendar2-check me-2"></i>
                                            Today's Appointments
                                        </h4>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($appointments as $appointment)
                                                <li class="list-group-item p-4">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1 fw-bold">Dr.
                                                                {{ $appointment->doctor->user->f_name }}
                                                                {{ $appointment->doctor->user->l_name }}</h6>
                                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                                {{ $appointment->doctor->speciality->name }}
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                {{ $appointment->date }}
                                                            </div>
                                                            <div class="small text-muted">
                                                                <i class="bi bi-clock me-1"></i>
                                                                {{ $appointment->start_time }} -
                                                                {{ $appointment->end_time }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item p-4 text-center text-muted">
                                                    <i class="bi bi-calendar2-x fs-1"></i>
                                                    <p class="mt-2 mb-0">No appointments scheduled for today</p>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Appointments -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white border-0 p-4">
                                        <h4 class="fw-bold text-warning mb-0">
                                            <i class="bi bi-hourglass-split me-2"></i>
                                            Pending Appointments
                                        </h4>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($pending_appointments as $appointment)
                                                <li class="list-group-item p-4">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <h6 class="mb-1">
                                                                <i class="bi bi-person me-2 text-primary"></i>
                                                                {{ $appointment->patient->user->f_name }}
                                                                {{ $appointment->patient->user->l_name }}
                                                            </h6>
                                                            <h6 class="mb-0">
                                                                <i class="bi bi-hospital me-2 text-success"></i>
                                                                Dr. {{ $appointment->doctor->user->f_name }}
                                                                {{ $appointment->doctor->user->l_name }}
                                                            </h6>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="small text-muted">
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                {{ $appointment->date }}
                                                            </div>
                                                            <div class="small text-muted">
                                                                <i class="bi bi-clock me-1"></i>
                                                                {{ $appointment->start_time }}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-success">
                                                                    <i class="bi bi-check-lg"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger">
                                                                    <i class="bi bi-x-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item p-4 text-center text-muted">
                                                    <i class="bi bi-inbox fs-1"></i>
                                                    <p class="mt-2 mb-0">No pending appointments</p>
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

        <script>
            document.getElementById('doctorSearch').addEventListener('input', function() {
                let query = this.value;

                fetch(`/doctors/search?query=${query}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let tbody = document.getElementById('doctorList');
                        tbody.innerHTML = '';

                        if (data.doctors.length > 0) {
                            data.doctors.forEach(doctor => {
                                let slots = doctor.appointment_slots.map(slot => `
                    <div class="mb-1 small">
                        <span class="text-muted">${slot.date}</span> |
                        <span class="fw-medium">${slot.start_time} - ${slot.end_time}</span> |
                        <span class="badge bg-${slot.status === 'available' ? 'success' : 'danger'}">
                            ${slot.status}
                        </span>
                    </div>
                `).join('');

                                tbody.innerHTML += `
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="bi bi-person-circle text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">${doctor.user.f_name} ${doctor.user.l_name}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                ${doctor.speciality.name}
                            </span>
                        </td>
                        <td>${slots}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                            });
                        } else {
                            tbody.innerHTML = `<tr><td colspan="4" class="text-center">No doctors found</td></tr>`;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        </script>
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
