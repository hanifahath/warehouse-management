<x-guest-layout>

    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">
            Login to Your Account
        </h1>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('email') }}" required autofocus autocomplete="username">

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required autocomplete="current-password">

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center mb-4">
                <input id="remember_me" type="checkbox" name="remember"
                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">

                <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">

                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">
                    Forgot Password?
                </a>
                @endif

                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow">
                    Log In
                </button>

            </div>

        </form>

    </div>

</x-guest-layout>
