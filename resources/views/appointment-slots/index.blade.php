<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold mb-6">Appointment Slots</h2>

                <!-- Create Appointment Slot Button -->
                <div class="mb-6">
                    <a href="{{ route('appointment-slots.create') }}"
                        class="inline-block px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Create Appointment Slot
                    </a>
                </div>

                <!-- Appointment Slots Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Doctor</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Date</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Start Time</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">End Time</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointmentSlots as $slot)
                                <tr class="border-t border-b">
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $slot->doctor->user->f_name }}
                                        {{ $slot->doctor->user->l_name }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $slot->date }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900 capitalize">{{ $slot->status }}</td>
                                    <td class="px-6 py-3 text-sm">
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
