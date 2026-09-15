<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar — Gudang Gadget</title>
        @vite('resources/css/app.css')
    </head>
    <body class="h-full bg-navy-900 font-sans text-navy-900">

        <div class="flex min-h-screen">

            {{-- Panel form (kiri) --}}
            <div class="flex-1 flex items-center justify-center bg-navy-50 px-6 py-12">
                <div class="w-full max-w-sm">

                    <a href="{{ route('login') }}" class="lg:hidden flex items-center justify-center gap-2 mb-8">
                        <div class="grid place-items-center w-10 h-10 rounded-xl bg-navy-700 text-gold-400">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                <path d="M12 12v3M9 13.5h6"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-navy-800">Gudang <span class="text-gold-500">Gadget</span></span>
                    </a>

                    <h2 class="text-2xl font-bold text-navy-800">Daftar akun</h2>
                    <p class="text-sm text-navy-400 mt-1 mb-8">Isi data di bawah untuk membuat akun baru.</p>

                    @if (session('success'))
                        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm flex items-center gap-2">
                            <span>✓</span>{{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm flex items-start gap-2">
                            <span class="font-semibold">!</span>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="bg-white rounded-2xl border border-navy-100 p-6 space-y-5 shadow-sm">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1.5">Nama Lengkap</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <input type="text" name="name" required value="{{ old('name') }}" autofocus autocomplete="name"
                                       placeholder="Nama Anda"
                                       class="w-full rounded-xl border border-navy-100 bg-navy-50/50 pl-10 pr-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                            </div>
                            @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1.5">Email</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                                        <path d="m22 7-10 6L2 7"/>
                                    </svg>
                                </span>
                                <input type="email" name="email" required value="{{ old('email') }}" autocomplete="email"
                                       placeholder="nama@email.com"
                                       class="w-full rounded-xl border border-navy-100 bg-navy-50/50 pl-10 pr-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                            </div>
                            @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1.5">Password</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input type="password" name="password" required autocomplete="new-password"
                                       placeholder="Minimal 8 karakter"
                                       class="w-full rounded-xl border border-navy-100 bg-navy-50/50 pl-10 pr-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                            </div>
                            @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1.5">Konfirmasi Password</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input type="password" name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Ulangi password"
                                       class="w-full rounded-xl border border-navy-100 bg-navy-50/50 pl-10 pr-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                            </div>
                            @error('password_confirmation') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                            Daftar
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </button>
                    </form>

                    <p class="text-sm text-navy-500 text-center mt-6">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-semibold text-gold-600 hover:text-gold-500">Masuk</a>
                    </p>
                </div>
            </div>

            {{-- Panel branding (kanan) --}}
            <div class="hidden lg:flex w-1/2 relative overflow-hidden bg-navy-800 text-white flex-col justify-between p-12">
                <div class="absolute inset-0 opacity-40" style="background:
                    radial-gradient(600px 300px at 110% -10%, rgba(232,163,61,0.35), transparent 60%),
                    radial-gradient(500px 400px at -10% 110%, rgba(61,100,145,0.55), transparent 60%);"></div>

                <div class="relative flex items-center justify-end">
                    <a href="{{ route('login') }}" class="flex items-center gap-2">
                        <div class="grid place-items-center w-10 h-10 rounded-xl bg-gold-500 text-navy-900">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                <path d="M12 12v3M9 13.5h6"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight">Gudang <span class="text-gold-400">Gadget</span></span>
                    </a>
                </div>

                <div class="relative text-right ml-auto">
                    <p class="text-gold-400 font-semibold text-sm uppercase tracking-widest mb-3">Mulai sekarang</p>
                    <h1 class="text-3xl font-bold leading-snug">
                        Buat akun dan<br>kelola inventaris Anda.
                    </h1>
                    <p class="text-navy-100/80 mt-4 max-w-md ml-auto leading-relaxed">
                        Daftar untuk mulai mencatat produk, pantau stok, dan cek status
                        gadget setiap saat.
                    </p>
                </div>

                <div class="relative flex items-center justify-end gap-2 text-sm text-navy-100/60">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Data Anda aman
                </div>
            </div>
        </div>

    </body>
</html>