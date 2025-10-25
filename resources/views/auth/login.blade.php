<x-authentication-layout>
    <!-- Brand Header -->
    <div class="mb-6 text-center">
        <div class="flex flex-col items-center gap-2">
            <div class="w-16 h-16 rounded-xl bg-white dark:bg-gray-700 shadow-lg flex items-center justify-center ring-1 ring-gray-200 dark:ring-gray-600">
                <img src="{{ asset('images/bakrie-logo.png') }}" alt="Logo Bakrie" class="w-14 h-14 object-contain">
            </div>
            <h1 class="text-2xl md:text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-orange-600 via-amber-600 to-yellow-600 dark:from-amber-300 dark:via-yellow-200 dark:to-orange-200">
                Sistem Pengelolaan Surat
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-tight">
                Universitas Bakrie &middot; Manajemen Surat Masuk &amp; Keluar Terintegrasi
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-600 px-4 py-2 text-sm text-green-700 dark:text-green-300 flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Card Container -->
    <div class="relative">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md ring-1 ring-gray-200 dark:ring-gray-700 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="8.5" cy="7" r="4" /><path stroke-linecap="round" stroke-linejoin="round" d="M20 8v6M23 11h-6" /></svg>
                Masuk ke Akun Anda
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Gunakan kredensial resmi kampus. Hubungi BTI jika mengalami kendala.</p>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <!-- Username / Email -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <x-label for="username" class="text-sm font-medium text-gray-700 dark:text-gray-300">Username / Email <span class="text-red-500">*</span></x-label>
                        @error('username')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <x-input id="username" type="text" name="username" :value="old('username')" required autofocus placeholder="contoh: admin / user@bakrie.ac.id" class="w-full" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <x-label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi <span class="text-red-500">*</span></x-label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300" href="{{ route('password.request') }}">Lupa?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <x-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full pr-10" />
                        <button type="button" x-data="{ show: false }" @click="show = !show; $el.previousElementSibling.type = show ? 'text' : 'password'" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-amber-600 focus:outline-none" tabindex="-1">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.956 9.956 0 012.223-3.592m3.147-2.183A9.956 9.956 0 0112 5c4.477 0 8.267 2.943 9.542 7a10.025 10.025 0 01-4.043 5.197M15 12a3 3 0 00-3-3" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" /></svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember & Role Info -->
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500" />
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                    <div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" /></svg>
                        <span>Versi Beta Internal</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <x-button class="w-full justify-center bg-amber-600 hover:bg-amber-500 focus:ring-amber-400">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" /></svg>
                        <span>Masuk</span>
                    </x-button>
                </div>
            </form>

            <!-- Validation Errors (global) -->
            <x-validation-errors class="mt-4" />

            <!-- Tips -->
            <div class="mt-6 bg-amber-50 dark:bg-gray-700/50 border border-amber-200 dark:border-gray-600 rounded-lg p-4 text-xs text-amber-700 dark:text-amber-300 flex gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div>
                    <p class="font-medium mb-1">Panduan:</p>
                    <ul class="list-disc ml-4 space-y-0.5">
                        <li>Gunakan <strong>username</strong> internal atau email bakrie.ac.id</li>
                        <li>Hubungi <span class="font-medium">BTI</span> untuk reset akses</li>
                        <li>Aktifkan tanda tangan digital di Profil bila dibutuhkan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center space-y-3">
        {{-- <div class="text-sm text-gray-600 dark:text-gray-400">
            Belum punya akun? <a class="font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300" href="{{ route('register') }}">Daftar</a>
        </div> --}}
        <div class="text-[11px] tracking-wide text-gray-400 dark:text-gray-500">
            &copy; {{ date('Y') }} Universitas Bakrie
        </div>
    </div>
</x-authentication-layout>
