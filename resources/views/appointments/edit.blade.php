<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="container">
                <h2>Edit Appointment</h2>

                <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="patient_id">Patient</label>
                        <input type="text" id="patient_name" class="form-control" 
                               value="{{ $appointment->patient->user->f_name }}" 
                               disabled>
                        <input type="hidden" name="patient_id" value="{{ $appointment->patient->id }}">
                    </div>

                    {{-- <div class="form-group">
                        <label for="doctor_id">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-control">
                          
                            @foreach ($doctors as $doctor)
                            <option value="{{$appointment->doctor_id}}">{{$appointment->doctor->user->f_name}} {{$appointment->doctor->user->l_name}}</option>
                                <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $appointment->doctor->user->f_name }} {{ $appointment->doctor->user->l_name }}</option>
                            @endforeach 
                        </select>
                    </div> --}}


                    <div class="form-group">
                        <label for="doctor_id">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-control">
                            <option value="{{ $appointment->doctor_id }}" selected>
                                {{ $appointment->doctor->user->f_name }} {{ $appointment->doctor->user->l_name }}
                            </option>


                            @foreach ($doctors as $doctor)
                                @if ($doctor->id !== $appointment->doctor_id)
                                    <option value="{{ $doctor->id }}"
                                        {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->user->f_name }} {{ $doctor->user->l_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="date">Appointment Date</label>
                        <input type="date" name="date" id="date" class="form-control"
                            value="{{ $appointment->date }}" required>
                    </div>

                    <div class="form-group">
                        <label for="time">Appointment Time</label>
                        <input type="time" name="time" id="time" class="form-control"
                            value="{{ $appointment->time }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
