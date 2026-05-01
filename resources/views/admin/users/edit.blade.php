<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Edit User</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }} &middot; joined {{ $user->created_at->format('d M Y') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="text-sm bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-xl p-6 space-y-5">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Used for login by young people without email.</p>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Not required for young children.</p>
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        @foreach(['admin', 'social_worker', 'carer', 'young_person'] as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                {{-- Carer assignment (shown for young_person role) --}}
                <div id="carer-section">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Carer</label>
                    <select name="carer_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">— None —</option>
                        @foreach($carers as $carer)
                        <option value="{{ $carer->id }}" @selected(old('carer_id', $user->carer_id) == $carer->id)>
                            {{ $carer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- New Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg shadow hover:bg-indigo-700 transition text-sm font-medium">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-6 bg-white shadow rounded-xl p-6 border border-red-100">
            <h3 class="text-sm font-semibold text-red-700 mb-3">Danger Zone</h3>
            <p class="text-xs text-gray-500 mb-4">Permanently delete this user and all associated data. This cannot be undone.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This CANNOT be undone.')">
                @csrf @method('DELETE')
                <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    Delete This User
                </button>
            </form>
        </div>
    </div>
</x-app-layout>