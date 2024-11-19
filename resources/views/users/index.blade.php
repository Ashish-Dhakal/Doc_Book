<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="container mx-auto py-8">
                    <h1 class="text-3xl font-bold mb-6">Users</h1>
                    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded mb-4 inline-block">Create New User</a>
                    
                    @if (session('success'))
                        <div class="bg-green-200 text-green-800 p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                
                    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-6 py-4">First Name</th>
                                <th class="px-6 py-4">Last Name</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-t">
                                    <td class="px-6 py-4">{{ $user->f_name }}</td>
                                    <td class="px-6 py-4">{{ $user->l_name }}</td>
                                    <td class="px-6 py-4">{{ $user->email }}</td>
                                    <td class="px-6 py-4">{{ $user->roles }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('users.show', $user) }}" class="text-blue-500">View</a> | 
                                        <a href="{{ route('users.edit', $user) }}" class="text-yellow-500">Edit</a> | 
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500">Delete</button>
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
</x-app-layout>
