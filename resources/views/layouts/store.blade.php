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
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleMobileMenu(true)" class="md:hidden grid place-items-center w-10 h-10 rounded-xl text-navy-700 hover:bg-navy-50 transition-colors -ml-2"
                            aria-label="Buka menu navigasi" aria-controls="mobileMenu" aria-expanded="false">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>
                    <a href="{{ route('shop.home') }}" class="flex items-center">
                        <x-logo size="sm">{{ $settings['store_name'] }}</x-logo>
                    </a>
                </div>

                <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
                    <a href="{{ route('shop.home') }}" class="text-navy-600 hover:text-gold-600 transition-colors">Beranda</a>
                    <a href="{{ route('shop.katalog') }}" class="text-navy-600 hover:text-gold-600 transition-colors">Katalog</a>
                    <a href="{{ route('shop.home') }}#unggulan" class="text-navy-600 hover:text-gold-600 transition-colors">Unggulan</a>
                    <a href="{{ route('shop.home') }}#lokasi" class="text-navy-600 hover:text-gold-600 transition-colors">Lokasi Toko</a>
                    <a href="{{ route('shop.tracking') }}" class="text-navy-600 hover:text-gold-600 transition-colors">Tracking Service</a>
                    <a href="{{ route('shop.intake') }}" class="text-navy-600 hover:text-gold-600 transition-colors">Ajukan Servis</a>
                    <a href="{{ \Illuminate\Support\Facades\Auth::user()?->isReseller() ? route('shop.mitra.beranda') : route('shop.mitra.masuk') }}"
                       class="text-navy-600 hover:text-gold-600 transition-colors">Portal Mitra</a>
                </nav>

                <div class="flex items-center gap-3">
                    @php $jumlahKeranjang = \Illuminate\Support\Facades\Session::get('keranjang_publik', []); $jumlahKeranjang = array_sum($jumlahKeranjang); @endphp
                    <a href="{{ route('shop.keranjang') }}" aria-label="Keranjang belanja"
                       class="relative grid place-items-center w-10 h-10 rounded-xl text-navy-700 hover:bg-navy-50 transition-colors">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        @if ($jumlahKeranjang > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-5 px-1 h-5 grid place-items-center rounded-full bg-gold-500 text-navy-900 text-[10px] font-black">{{ $jumlahKeranjang }}</span>
                        @endif
                    </a>
                    @auth
                        @if (Auth::user()->isReseller())
                            <div class="hidden sm:flex flex-col items-end">
                                <a href="{{ route('shop.mitra.beranda') }}" class="text-xs font-black text-gold-600 hover:text-gold-700">
                                    {{ Auth::user()->name }}
                                </a>
                                <span class="text-[10px] font-semibold text-navy-400">{{ Auth::user()->reseller?->status_label }}</span>
                            </div>
                        @endif
                    @endauth
                    <a href="{{ 'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}"
                       target="_blank" rel="noopener"
                       class="hidden sm:flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-full transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                        Chat Kami
                    </a>
                </div>
            </div>
        </header>

        {{-- Mobile Drawer Menu --}}
        <div id="mobileMenu" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-label="Menu navigasi">
            <div id="mobileOverlay" class="absolute inset-0 bg-navy-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="toggleMobileMenu(false)"></div>
            <aside class="absolute left-0 top-0 h-full w-72 max-w-[85vw] bg-white shadow-2xl -translate-x-full transition-transform duration-300 flex flex-col"
                   aria-label="Tautan menu">
                <div class="flex items-center justify-between px-5 h-16 border-b border-navy-100">
                    <x-logo size="xs">{{ $settings['store_name'] }}</x-logo>
                    <button type="button" onclick="toggleMobileMenu(false)" class="grid place-items-center w-9 h-9 rounded-xl text-navy-500 hover:bg-navy-50 transition-colors" aria-label="Tutup menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('shop.home') }}" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('shop.katalog') }}" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Katalog Lengkap
                    </a>
                    <a href="{{ route('shop.keranjang') }}" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Keranjang Belanja
                    </a>
                    <a href="{{ route('shop.home') }}#unggulan" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Produk Unggulan
                    </a>
                    <a href="{{ route('shop.home') }}#lokasi" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Info Lokasi & Jam Buka
                    </a>
                    <a href="{{ route('shop.tracking') }}" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Tracking Service
                    </a>
                    <a href="{{ route('shop.intake') }}" onclick="toggleMobileMenu(false)" class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold text-navy-800 hover:bg-navy-50 transition-colors">
                        Ajukan Servis
                    </a>
                    <a href="{{ \Illuminate\Support\Facades\Auth::user()?->isReseller() ? route('shop.mitra.beranda') : route('shop.mitra.masuk') }}" onclick="toggleMobileMenu(false)"
                       class="flex items-center justify-between w-full px-3 py-3 rounded-xl font-semibold bg-navy-900 text-white hover:bg-navy-800 transition-colors">
                        Portal Mitra Reseller
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </nav>
                <div class="px-4 pb-5">
                    <a href="{{ 'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) . '/?text=' . rawurlencode('Halo, saya ingin bertanya tentang produk di toko Anda.') }}"
                       target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-3 rounded-xl transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                        Chat CS WhatsApp
                    </a>
                </div>
            </aside>
        </div>

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

        {{-- Toast notifikasi --}}
        <div id="toast" role="status" aria-live="polite"
             class="fixed left-1/2 -translate-x-1/2 top-4 z-[60] px-4 py-2.5 rounded-xl bg-navy-900 text-white text-sm font-semibold shadow-xl opacity-0 pointer-events-none transition-all duration-300 -translate-y-3"></div>

        {{-- Footer --}}
        <footer id="lokasi-footer" class="bg-navy-900 text-navy-100">
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
                    @if ($settings['maps_embed'] && !request()->routeIs('shop.home'))
                        <div class="rounded-xl overflow-hidden border border-white/10">
                            <iframe src="{{ $settings['maps_embed'] }}" width="100%" height="160" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi {{ $settings['store_name'] }}"></iframe>
                        </div>
                    @else
                        <p class="text-sm text-navy-200/80">
                            {{ request()->routeIs('shop.home') ? 'Lihat peta besar di bagian "Kunjungi Toko Kami" di atas.' : 'Peta belum diatur di Pengaturan Toko.' }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="border-t border-white/10 py-4 text-center text-xs text-navy-200/60">
                © {{ date('Y') }} {{ $settings['store_name'] }} — Semua hak cipta dilindungi.
            </div>
        </footer>

        <script>
            function toggleMobileMenu(open) {
                const menu = document.getElementById('mobileMenu');
                const overlay = document.getElementById('mobileOverlay');
                const panel = menu.querySelector('aside');
                const trigger = document.querySelector('button[aria-controls="mobileMenu"]');
                if (open) {
                    menu.classList.remove('hidden');
                    requestAnimationFrame(() => {
                        panel.classList.remove('-translate-x-full');
                        overlay.classList.add('opacity-100');
                    });
                    document.body.classList.add('overflow-hidden');
                    if (trigger) trigger.setAttribute('aria-expanded', 'true');
                } else {
                    panel.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100');
                    document.body.classList.remove('overflow-hidden');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                    setTimeout(() => menu.classList.add('hidden'), 300);
                }
            }

            function showToast(message) {
                const toast = document.getElementById('toast');
                if (!toast) return;
                toast.textContent = message;
                toast.classList.remove('opacity-0', '-translate-y-3');
                toast.classList.add('opacity-100');
                clearTimeout(toast._t);
                toast._t = setTimeout(() => {
                    toast.classList.add('opacity-0', '-translate-y-3');
                    toast.classList.remove('opacity-100');
                }, 2200);
            }

            function initHeroSlider() {
                const sliders = document.querySelectorAll('.hero-slider');
                sliders.forEach(slider => {
                    const slides = [...slider.querySelectorAll('.hero-slide')];
                    const dots = [...slider.querySelectorAll('.hero-dot')];
                    const prev = slider.querySelector('.hero-prev');
                    const next = slider.querySelector('.hero-next');
                    if (!slides.length) return;
                    let current = 0;
                    let timer = null;

                    const goto = (i) => {
                        const n = (i + slides.length) % slides.length;
                        slides.forEach((s, j) => s.classList.toggle('hidden', j !== n));
                        dots.forEach(d => {
                            const on = String(d.dataset.heroDot) === String(slides[n].dataset.heroIndex);
                            d.classList.toggle('bg-gold-500', on);
                            d.classList.toggle('bg-navy-200', !on);
                        });
                        current = n;
                    };
                    const play = () => { if (timer) return; timer = setInterval(() => goto(current + 1), 6000); };
                    const stop = () => { if (timer) { clearInterval(timer); timer = null; } };

                    if (prev) prev.addEventListener('click', () => { stop(); goto(current - 1); play(); });
                    if (next) next.addEventListener('click', () => { stop(); goto(current + 1); play(); });
                    dots.forEach((d, i) => d.addEventListener('click', () => { stop(); goto(i); play(); }));

                    slider.addEventListener('mouseenter', stop);
                    slider.addEventListener('mouseleave', play);
                    slider.addEventListener('touchstart', stop, { passive: true });
                    slider.addEventListener('touchend', play);

                    if (slides.length > 1) play();
                });
            }
            document.addEventListener('DOMContentLoaded', initHeroSlider);
        </script>

        @stack('page_scripts')
    </body>
</html>