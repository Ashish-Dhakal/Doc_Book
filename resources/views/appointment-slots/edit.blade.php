<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto my-8">
                <div class="bg-white p-8 shadow-lg rounded-lg">
                    <h2 class="text-2xl font-semibold mb-4">Edit Appointment Slot</h2>

                    <form action="{{ route('appointment-slots.update', $appointmentSlot->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">

                            <!-- Doctor -->
                            <div>
                                <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                                <select name="doctor_id" id="doctor_id"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    <option value="">Select Doctor</option>
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            {{ $appointmentSlot->doctor_id == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->user->f_name }} {{ $doctor->user->l_name }}</option>
                                    @endforeach
                                </select>
                                @error('doctor_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date -->
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" id="date" name="date"
                                    value="{{ old('date', $appointmentSlot->date) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Start Time -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start
                                    Time</label>
                                <input type="time" id="start_time" name="start_time"
                                    value="{{ old('start_time', $appointmentSlot->start_time) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('start_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="time" id="end_time" name="end_time"
                                    value="{{ old('end_time', $appointmentSlot->end_time) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('end_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="available"
                                        {{ old('status', $appointmentSlot->status) == 'available' ? 'selected' : '' }}>
                                        Available</option>
                                    <option value="booked"
                                        {{ old('status', $appointmentSlot->status) == 'booked' ? 'selected' : '' }}>
                                        Booked</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Update
                                    Slot</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
