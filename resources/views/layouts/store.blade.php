<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <title>@yield('title') — {{ $settings['store_name'] }}</title>
        <meta name="description" content="@yield('meta_desc', $settings['tags_line'])">
        @stack('head_meta')
        @vite('resources/css/app.css')
    </head>
    <body class="bg-navy-50 font-sans text-navy-900 antialiased">

        {{-- Navbar --}}
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-navy-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
                <a href="{{ route('shop.home') }}" class="flex items-center gap-2.5">
                    <div class="grid place-items-center w-9 h-9 rounded-xl bg-gold-500 text-navy-900">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <path d="M12 12v3M9 13.5h6"/>
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight text-navy-900">{{ $settings['store_name'] }}</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
                    <a href="{{ route('shop.home') }}" class="text-navy-600 hover:text-gold-600 transition-colors">Beranda</a>
                    <a href="{{ route('shop.katalog') }}" class="text-navy-600 hover:text-gold-600 transition-colors">Katalog</a>
                    <a href="{{ route('shop.home') }}#unggulan" class="text-navy-600 hover:text-gold-600 transition-colors">Unggulan</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ 'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}"
                       target="_blank" rel="noopener"
                       class="hidden sm:flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-full transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                        Chat Kami
                    </a>
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        {{-- Floating WhatsApp --}}
        <a href="{{ 'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}"
           target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 w-14 h-14 grid place-items-center rounded-full bg-emerald-500 hover:bg-emerald-600 text-white shadow-xl shadow-emerald-500/30 transition-transform hover:scale-110"
           aria-label="Chat WhatsApp">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
        </a>

        {{-- Footer --}}
        <footer class="bg-navy-900 text-navy-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p class="font-bold text-white text-lg mb-3">{{ $settings['store_name'] }}</p>
                    <p class="text-sm text-navy-200/80 leading-relaxed">{{ $settings['tags_line'] }}</p>
                    <div class="flex items-center gap-3 mt-4">
                        @if (!empty($settings['instagram_url']) && $settings['instagram_url'] !== '#')
                            <a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener" class="text-navy-200 hover:text-gold-400 transition-colors" aria-label="Instagram">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                            </a>
                        @endif
                        @if (!empty($settings['facebook_url']) && $settings['facebook_url'] !== '#')
                            <a href="{{ $settings['facebook_url'] }}" target="_blank" rel="noopener" class="text-navy-200 hover:text-gold-400 transition-colors" aria-label="Facebook">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3l-.4 3H14v9h-3v-9H8V9h3V6.5C11 4.6 12.2 3 15 3h2v3h-2c-.6 0-1 .4-1 1.2V9z"/></svg>
                            </a>
                        @endif
                        @if (!empty($settings['tiktok_url']) && $settings['tiktok_url'] !== '#')
                            <a href="{{ $settings['tiktok_url'] }}" target="_blank" rel="noopener" class="text-navy-200 hover:text-gold-400 transition-colors" aria-label="TikTok">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.4 2 1.7 3.6 3.5 4v3c-1.3 0-2.5-.4-3.5-1v6.5c0 3-2.2 5.5-5.2 5.5A5.3 5.3 0 0 1 6 15.5c0-2.9 2.3-5.2 5.2-5.2.3 0 .6 0 .8.1v3.2c-.2-.1-.5-.2-.8-.2a2 2 0 1 0 2 2V3h3.3z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Informasi Toko</p>
                    <p class="text-sm text-navy-200/80 leading-relaxed">{{ $settings['store_address'] }}</p>
                    <p class="text-sm text-navy-200/80 mt-2">Jam Operasional:<br>{{ $settings['jam_operasional'] }}</p>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Lokasi</p>
                    @if ($settings['maps_embed'])
                        <div class="rounded-xl overflow-hidden border border-white/10">
                            <iframe src="{{ $settings['maps_embed'] }}" width="100%" height="160" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi {{ $settings['store_name'] }}"></iframe>
                        </div>
                    @else
                        <p class="text-sm text-navy-200/80">Peta belum diatur di Pengaturan Toko.</p>
                    @endif
                </div>
            </div>
            <div class="border-t border-white/10 py-4 text-center text-xs text-navy-200/60">
                © {{ date('Y') }} {{ $settings['store_name'] }} — Semua hak cipta dilindungi.
            </div>
        </footer>

        <script>
            function heroNext() {
                const slider = document.querySelector('.hero-slider');
                if (!slider) return;
                const track = slider.querySelector('.hero-track');
                const active = slider.querySelector('.hero-slide:not(.hidden)');
                if (!track || !active) return;
                const next = active.nextElementSibling || track.firstElementChild;
                active.classList.add('hidden');
                next.classList.remove('hidden');
                slider.querySelectorAll('.hero-dot').forEach((d, i) => {
                    d.classList.toggle('bg-gold-500', d.getAttribute('data-hero-dot') == next.getAttribute('data-hero-index'));
                    d.classList.toggle('bg-navy-200', d.getAttribute('data-hero-dot') != next.getAttribute('data-hero-index'));
                });
            }
            setInterval(heroNext, 6000);
        </script>
    </body>
</html>