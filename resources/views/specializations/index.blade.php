<!-- resources/views/specialities/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Specialities') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Add New Speciality Button -->
                <a href="{{ route('specializations.create') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded mb-6 inline-block hover:bg-blue-600 transition">
                    Add New Speciality
                </a>

                <!-- Specialities Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-6 py-3 text-sm font-semibold text-gray-700">ID</th>
                                <th class="px-6 py-3 text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-6 py-3 text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($specialities as $speciality)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-800">{{ $speciality->id }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-800">{{ $speciality->name }}</td>
                                    <td class="px-6 py-3 text-sm space-x-2">
                                        <a href="{{ route('specializations.show', $speciality) }}"
                                            class="text-blue-500 hover:text-blue-700">View</a>
                                        <a href="{{ route('specializations.edit', $speciality) }}"
                                            class="text-yellow-500 hover:text-yellow-700">Edit</a>
                                        <form action="{{ route('specializations.destroy', $speciality) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-4">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $specialities->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
