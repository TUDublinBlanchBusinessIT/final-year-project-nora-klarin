<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-800">Users</h2>
            <a href="{{ route('admin.users.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition text-sm font-medium">
                + Create New User
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-4">

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter by role --}}
        <div class="flex gap-2 flex-wrap">
            @foreach(['All', 'admin', 'social_worker', 'carer', 'young_person'] as $filter)
            <a href="{{ $filter === 'All' ? route('admin.users.index') : route('admin.users.index', ['role' => $filter]) }}"
               class="px-3 py-1.5 rounded-full text-sm font-medium border transition
                   {{ (request('role', 'All') === $filter) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">
                {{ ucfirst(str_replace('_', ' ', $filter)) }}
            </a>
            @endforeach
        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DOB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users->when(request('role') && request('role') !== 'All', fn($c) => $c->where('role', request('role'))) as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $user->username ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $user->email ?? '—' }}</td>
                        <td class="px-6 py-3">
                            @php
                                $roleColors = [
                                    'admin'         => 'bg-purple-100 text-purple-700',
                                    'social_worker' => 'bg-sky-100 text-sky-700',
                                    'carer'         => 'bg-amber-100 text-amber-700',
                                    'young_person'  => 'bg-emerald-100 text-emerald-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-500">
                            {{ $user->dob ? $user->dob->format('d M Y') : '—' }}
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 flex gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg hover:bg-indigo-100 transition font-medium">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded-lg hover:bg-red-100 transition font-medium">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>