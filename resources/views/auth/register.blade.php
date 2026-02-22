<x-guest-layout>
    <div class="max-w-md mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="mb-4 inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 dark:from-purple-600 dark:to-pink-700 rounded-lg shadow-lg">
                <i class="bi bi-person-plus text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create Account</h1>
            <p class="text-gray-600 dark:text-gray-400">Join Sekai to get started</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/50 p-8 border border-gray-100 dark:border-gray-700">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <script>
                    // Randomize placeholder name on page load
                    document.addEventListener('DOMContentLoaded', function() {
                    const nameInput = document.getElementById('name');

                    const placeholders = [
                        'Kanade Yoisaki',
                        'Hoshino Ichika',
                        'Mafuyu Asahina',
                        'Emu Otori',
                        'Shizuku Hinomori',
                        'Ena Shinonome',
                        'Mizuki Akiyama',
                        'Saki Tenma',
                        'Hatsune Miku',
                        'Nene Kusanagi',
                        'Airi Momoi',
                        'Saturday Tasogare',
                        'Dawn Hisomeru',
                        'Tsuki Tasogare',
                        'Lucy Ivory',
                        'Miki Arranoia',
                        'Grzegorz Brzęczyszczykiewicz'
                    ];

                    const placeholderWeights = [
                        4,      // kanade <3
                        1,      // ichika
                        1,      // mafuyu
                        1,      // emu
                        1,      // shizuku
                        1,      // ena
                        1,      // mizuki
                        1,      // saki
                        0.39,   // miku
                        1,      // nene
                        0.5,    // airi
                        0.5,    // saturday
                        0.125,  // dawn
                        0.125,  // tsuki
                        0.125,  // lucy
                        0.125,  // miki
                        0.00001 // this guy
                    ];

                    const totalWeight = placeholderWeights.reduce((a, b) => a + b, 0);
                    const random = Math.random() * totalWeight;

                    let cumulativeWeight = 0;
                    let selected = placeholders[0];

                    for (let i = 0; i < placeholders.length; i++) {
                        cumulativeWeight += placeholderWeights[i];
                        if (random < cumulativeWeight) {
                            selected = placeholders[i];
                            break;
                        }
                    }

                    nameInput.placeholder = selected;
                });
                </script>

                <!-- Name -->
                <div class="mb-6">
                    <x-input-label for="name" :value="__('Full Name')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-person text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="name" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm" />
                </div>

                <!-- Email Address -->
                <div class="mb-6">
                    <x-input-label for="email" :value="__('Email Address')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-envelope text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="email" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="your@email.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
                </div>

                <!-- Identifier -->
                <div class="mb-6">
                    <x-input-label for="identifier" :value="__('ID Number')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-card-text text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="identifier" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition" type="text" name="identifier" :value="old('identifier')" required autocomplete="identifier" placeholder="12345678" />
                    </div>
                    <x-input-error :messages="$errors->get('identifier')" class="mt-2 text-sm" />
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <x-input-label for="password" :value="__('Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-key text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="password" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-key-fill text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <x-text-input id="password_confirmation" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm" />
                </div>

                <!-- Register Button -->
                <div class="space-y-4">
                    <x-primary-button class="w-full justify-center py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-600 dark:to-pink-700 hover:from-purple-700 hover:to-pink-700 dark:hover:from-purple-700 dark:hover:to-pink-800 font-semibold transition shadow-lg">
                        {{ __('Create Account') }}
                    </x-primary-button>
                </div>

                <!-- Divider -->
                <div class="mt-6 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">Already have an account?</span>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="inline-block px-4 py-2.5 border-2 border-indigo-600 dark:border-indigo-500 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-950 transition w-full text-center">
                        {{ __('Sign In') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            <p>By signing up, you agree to our Terms of Service</p>
        </div>
    </div>
</x-guest-layout>
