<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto my-8">
                <div class="bg-white p-8 shadow-lg rounded-lg">
                    <h2 class="text-2xl font-semibold mb-4">Appointment Slots</h2>

                    <div class="mb-6">
                        <a href="{{ route('appointment-slots.create') }}"
                            class="px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Create
                            Appointment Slot</a>
                    </div>

                    <table class="min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left">Doctor</th>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-left">Start Time</th>
                                <th class="px-6 py-3 text-left">End Time</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointmentSlots as $slot)
                                <tr>
                                    <td class="px-6 py-3">{{ $slot->doctor->user->f_name }}
                                        {{ $slot->doctor->user->l_name }}</td>
                                    <td class="px-6 py-3">{{ $slot->date }}</td>
                                    <td class="px-6 py-3">{{ $slot->start_time }}</td>
                                    <td class="px-6 py-3">{{ $slot->end_time }}</td>
                                    <td class="px-6 py-3">{{ ucfirst($slot->status) }}</td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('appointment-slots.show', $slot->id) }}"
                                            class="text-blue-600 hover:text-blue-800">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>





