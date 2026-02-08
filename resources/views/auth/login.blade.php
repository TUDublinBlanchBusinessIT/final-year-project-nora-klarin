<x-guest-layout>

  <div class="flex flex-col items-center mb-3">
    <img src="{{ asset('images/CareHub.png') }}" 
         alt="CareHub Logo" 
         class="w-28 h-28 mb-3 mx-auto">

    <p class="text-center text-gray-700 text-base font-semibold">
        Care. Support. Connection.
    </p>
</div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

<div>
    <x-input-label for="username" :value="__('Username')" />
    <x-text-input 
        id="username" 
        class="block mt-1 w-full" 
        type="text" 
        name="username" 
        :value="old('username')" 
        required 
        autofocus 
        autocomplete="username" 
    />
    <x-input-error :messages="$errors->get('username')" class="mt-2" />
</div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
     <div class="mt-6">
    <x-primary-button class="w-full justify-center px-6 py-3 bg-blue-700 hover:bg-blue-800">
    {{ __('Log in') }}
</x-primary-button>


    @if (Route::has('password.request'))
        <div class="text-center mt-3">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        </div>
    @endif
</div>


    </form>
</x-guest-layout>
