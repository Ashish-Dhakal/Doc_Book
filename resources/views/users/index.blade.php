<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="max-w-6xl mx-auto my-8">
                <div class="bg-white p-8 shadow-lg rounded-lg" x-data="{ selectedTab: 'list-patients' }">
                    <!-- Button to create new appointment -->
                    <div class=" mb-4 flex justify-start">
                        <a href="{{ route('users.create') }}"
                            class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
                            Create User
                        </a>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 mb-4">
                        <ul class="flex space-x-4">
                            <li>
                                <button type="button"
                                    class="text-sm font-medium text-black px-4 py-2 hover:text-indigo-600 focus:outline-none"
                                    :class="{ 'border-b-2 border-indigo-600': selectedTab === 'list-patients' }"
                                    @click="selectedTab = 'list-patients'">Patients</button>
                            </li>
                            <li>
                                <button type="button"
                                    class="text-sm font-medium text-black px-4 py-2 hover:text-indigo-600 focus:outline-none"
                                    :class="{ 'border-b-2 border-indigo-600': selectedTab === 'list-doctors' }"
                                    @click="selectedTab = 'list-doctors'">Doctors</button>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <!-- Patient List -->
                    <div x-show="selectedTab === 'list-patients'">
                        <h2 class="text-2xl font-semibold mb-4">Patients List</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">First Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Last Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Phone</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Address</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patients as $patient)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $patient->user->f_name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $patient->user->l_name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $patient->user->email }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $patient->user->phone }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $patient->user->address }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                <!-- Actions: Edit, Delete -->
                                                <a href="{{ route('users.edit', $patient->user->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                                                <form action="{{ route('users.destroy', $patient->user->id) }}" method="POST"
                                                    class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Doctor List -->
                    <div x-show="selectedTab === 'list-doctors'">
                        <h2 class="text-2xl font-semibold mb-4">Doctors List</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">First Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Last Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Specialization
                                        </th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Address</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Phone</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($doctors as $doctor)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $doctor->user->f_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $doctor->user->l_name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $doctor->user->email }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $doctor->speciality->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $doctor->user->address }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">{{ $doctor->user->phone }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                <!-- Actions: Edit, Delete -->
                                                <a href="{{ route('users.edit', $doctor->user->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                                                <form action="{{ route('users.destroy', $doctor->user->id) }}" method="POST"
                                                    class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
