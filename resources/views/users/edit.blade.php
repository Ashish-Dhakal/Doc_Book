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
                    <h2 class="text-2xl font-semibold mb-4">Edit User</h2>
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <!-- First Name -->
                            <div>
                                <label for="f_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" id="f_name" name="f_name"
                                    value="{{ old('f_name', $user->f_name) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('f_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="l_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" id="l_name" name="l_name"
                                    value="{{ old('l_name', $user->l_name) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('l_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required readonly>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" id="password" name="password"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-sm text-gray-500">Leave blank to keep current password</p>
                            </div>
                            
                             <!-- Last Name -->
                             <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" id="hourly_rate" name="hourly_rate"
                                    value="{{ old('hourly_rate', $user->doctor->hourly_rate) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('hourly_rate')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="roles" class="block text-sm font-medium text-gray-700">Role</label>
                                <input type="text" id="roles" name="roles"
                                    value="{{ old('roles', $user->roles) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    readonly>
                                @error('roles')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($user->roles == 'doctor')
                                <div>
                                    <div>
                                        <label for="specility"
                                            class="block text-sm font-medium text-gray-700">Role</label>
                                        <select id="specility" name="speciality_id"
                                            class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Select Specialization</option>
                                            @foreach ($specialities as $speciality)
                                                <option value="{{ $speciality->id }}"
                                                    {{ old('roles') == $speciality->id ? 'selected' : '' }}>
                                                    {{ $speciality->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('speciality_id')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    @error('speciality_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif


                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone
                                    (Optional)</label>
                                <input type="text" id="phone" name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Address
                                    (Optional)</label>
                                <textarea id="address" name="address"
                                    class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring
                                                 <textarea id="address"
                                    name="address" class="mt-1 block w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
