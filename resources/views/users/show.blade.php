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
                    <h2 class="text-2xl font-semibold mb-4">User Details</h2>

                    <div class="space-y-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <p class="mt-1 text-lg">{{ $user->f_name }}</p>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <p class="mt-1 text-lg">{{ $user->l_name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-lg">{{ $user->email }}</p>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <p class="mt-1 text-lg">{{ ucfirst($user->roles) }}</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-lg">{{ $user->phone ?? 'N/A' }}</p>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <p class="mt-1 text-lg">{{ $user->address ?? 'N/A' }}</p>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
