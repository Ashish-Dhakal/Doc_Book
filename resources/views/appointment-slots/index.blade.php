<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment Slots') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl rounded-lg">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Appointment Slots</h2>

                <!-- Create Appointment Slot Button -->
                <div class="mb-6">
                    <a href="{{ route('appointment-slots.create') }}"
                        class="inline-block px-6 py-3 text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                        Create Appointment Slot
                    </a>
                </div>

                <!-- Appointment Slots Table -->
                <div class="overflow-x-auto bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                    <table class="min-w-full table-auto text-sm text-gray-700">
                        <thead class="bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase">
                            <tr>
                                <th class="px-6 py-3">Doctor</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Start Time</th>
                                <th class="px-6 py-3">End Time</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointmentSlots as $slot)
                                <tr class="border-t border-b hover:bg-gray-50">
                                    <td class="px-6 py-3">{{ $slot->doctor->user->f_name }} {{ $slot->doctor->user->l_name }}</td>
                                    <td class="px-6 py-3">{{ $slot->date }}</td>
                                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}</td>
                                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</td>
                                    <td class="px-6 py-3 capitalize">{{ $slot->status }}</td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('appointment-slots.show', $slot->id) }}"
                                            class="text-indigo-600 hover:text-indigo-800 transition duration-200">
                                            View 
                                        </a> |
                                        {{-- delete --}}
                                        <form action="{{ route('appointment-slots.destroy', $slot->id) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 transition duration-200">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $appointmentSlots->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
