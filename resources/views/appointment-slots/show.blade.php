
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
                    <h2 class="text-2xl font-semibold mb-4">Appointment Slot Details</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Doctor</label>
                            <p class="mt-1 text-lg">{{ $appointmentSlot->doctor->user->f_name }}
                                {{ $appointmentSlot->doctor->user->l_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <p class="mt-1 text-lg">{{ $appointmentSlot->date }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Time</label>
                            <p class="mt-1 text-lg">{{ $appointmentSlot->start_time }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Time</label>
                            <p class="mt-1 text-lg">{{ $appointmentSlot->end_time }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-lg">{{ ucfirst($appointmentSlot->status) }}</p>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('appointment-slots.edit', $appointmentSlot->id) }}"
                                class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Edit</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
