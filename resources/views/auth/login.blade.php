<x-guest-layout>
    <div class="max-w-md mx-auto">

        <div class="text-center mb-8">
            <div
                class="mb-4 inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-600 dark:to-purple-700 rounded-lg shadow-lg">
                <i class="bi bi-lock text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Welcome Back') }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ __('Sign in to your account to continue') }}</p>
        </div>


        <x-auth-session-status class="mb-6" :status="session('status')" />


        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/50 p-8 border border-gray-100 dark:border-gray-700">
            <form method="POST" action="{{ route('login') }}">
                @csrf


                <div class="mb-6">
                    <x-input-label for="identifier" :value="__('Identifier')"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-person text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="identifier"
                            class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition"
                            type="text" name="identifier" :value="old('identifier')" required autofocus autocomplete="username"
                            placeholder="{{ __('Enter your identifier') }}" />
                    </div>
                    <x-input-error :messages="$errors->get('identifier')" class="mt-2 text-sm" />
                </div>


                <div class="mb-6">
                    <x-input-label for="password" :value="__('Password')"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-key text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="password"
                            class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                </div>


                <div class="mb-6">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400"
                            name="remember">
                        <span
                            class="ms-2 text-sm text-gray-600 dark:text-gray-400 select-none">{{ __('Remember me') }}</span>
                    </label>
                </div>


                <div class="space-y-4">
                    <x-primary-button
                        class="w-full justify-center py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-indigo-600 dark:to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 dark:hover:from-indigo-700 dark:hover:to-indigo-800 font-semibold transition shadow-lg">
                        {{ __('Sign In') }}
                    </x-primary-button>

                    {{-- @if (Route::has('password.request'))
                    <div class="text-center">
                        <a class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                    @endif --}}
                </div>


                <div class="mt-6 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span
                            class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ __('New to Sekai?') }}</span>
                    </div>
                </div>


                @if (Route::has('register'))
                    <div class="mt-6 text-center">
                        <a href="{{ route('register') }}"
                            class="inline-block px-4 py-2.5 border-2 border-indigo-600 dark:border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-950 transition w-full text-center">
                            {{ __('Create Account') }}
                        </a>
                    </div>
                @endif
            </form>
        </div>


        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            <p>{{ __('Protected by enterprise-grade security provided by Miku') }}</p>
        </div>
    </div>
</x-guest-layout>