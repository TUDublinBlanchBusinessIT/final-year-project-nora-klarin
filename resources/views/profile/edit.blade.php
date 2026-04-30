<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="theme-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="theme-card p-4 sm:p-8 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="theme-card p-4 sm:p-8 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="theme-card p-4 sm:p-8 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-semibold mb-4">
                        Customize CareHub
                    </h3>

                    @if (session('status') === 'customization-updated')
                        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800">
                            Customization updated successfully.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.customization.update') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Dashboard Theme
                            </label>
                            <select name="theme" class="theme-input w-full rounded-lg">
                                <option value="calm" {{ auth()->user()->theme === 'calm' ? 'selected' : '' }}>Calm</option>
                                <option value="bright" {{ auth()->user()->theme === 'bright' ? 'selected' : '' }}>Bright</option>
                                <option value="simple" {{ auth()->user()->theme === 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="dark" {{ auth()->user()->theme === 'dark' ? 'selected' : '' }}>Dark Mode</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Chatbot Name
                            </label>
                            <input
                                type="text"
                                name="chatbot_name"
                                value="{{ old('chatbot_name', auth()->user()->chatbot_name) }}"
                                class="theme-input w-full rounded-lg"
                                placeholder="e.g. Support Buddy"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Dashboard Layout
                            </label>
                            <select name="dashboard_layout" class="theme-input w-full rounded-lg">
                                <option value="standard" {{ auth()->user()->dashboard_layout === 'standard' ? 'selected' : '' }}>
                                    Standard (Full view)
                                </option>
                                <option value="minimal" {{ auth()->user()->dashboard_layout === 'minimal' ? 'selected' : '' }}>
                                    Minimal (Focused view)
                                </option>
                            </select>
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                            >
                                Save Customization
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>