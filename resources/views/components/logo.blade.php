@props([
    'size' => 'md', // 'sm', 'md', 'lg', 'xl'
    'withText' => true,
    'textClass' => 'text-navy-900',
    'subtext' => null,
    'variant' => 'default', // 'default' (gold + navy), 'white' (for dark background)
])

@php
    $sizeMap = [
        'xs' => ['box' => 'w-7 h-7 rounded-lg', 'icon' => 'w-4 h-4', 'title' => 'text-sm font-bold', 'sub' => 'text-[10px]'],
        'sm' => ['box' => 'w-8 h-8 rounded-xl', 'icon' => 'w-5 h-5', 'title' => 'text-base font-bold', 'sub' => 'text-[10px]'],
        'md' => ['box' => 'w-10 h-10 rounded-xl', 'icon' => 'w-6 h-6', 'title' => 'text-lg font-black tracking-tight', 'sub' => 'text-xs'],
        'lg' => ['box' => 'w-12 h-12 rounded-2xl', 'icon' => 'w-7 h-7', 'title' => 'text-xl font-black tracking-tight', 'sub' => 'text-xs'],
        'xl' => ['box' => 'w-16 h-16 rounded-3xl', 'icon' => 'w-10 h-10', 'title' => 'text-2xl font-black tracking-tight', 'sub' => 'text-sm'],
    ];
    $current = $sizeMap[$size] ?? $sizeMap['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <!-- 3D Isometric Tech Cube Icon Mark -->
    <div class="relative {{ $current['box'] }} flex-shrink-0 grid place-items-center bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950 text-gold-400 shadow-md shadow-navy-950/20 border border-gold-500/30 overflow-hidden group">
        <!-- Subtle Glow Layer -->
        <div class="absolute inset-0 bg-gradient-to-tr from-gold-500/10 via-transparent to-amber-400/20 pointer-events-none"></div>

        <!-- Isometric Cube SVG -->
        <svg class="{{ $current['icon'] }} transform transition-transform group-hover:scale-110 duration-300" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Top Plane (Gold Ambient) -->
            <path d="M16 2.5L28 8.5L16 14.5L4 8.5L16 2.5Z" fill="url(#topGrad_{{ $size }})" stroke="#FDE68A" stroke-width="0.75" stroke-linejoin="round"/>
            
            <!-- Left Plane (Deep Navy / Slate) -->
            <path d="M4 8.5V20.5L16 26.5V14.5L4 8.5Z" fill="url(#leftGrad_{{ $size }})" stroke="rgba(255,255,255,0.15)" stroke-width="0.75" stroke-linejoin="round"/>
            
            <!-- Right Plane (Amber Tech Slate) -->
            <path d="M16 14.5V26.5L28 20.5V8.5L16 14.5Z" fill="url(#rightGrad_{{ $size }})" stroke="rgba(245,158,11,0.3)" stroke-width="0.75" stroke-linejoin="round"/>
            
            <!-- Floating Smart Device Screen on Top -->
            <path d="M16 5.5L23 9L16 12.5L9 9L16 5.5Z" fill="#090D16"/>
            <path d="M16 6.8L21 9.3L16 11.8L11 9.3L16 6.8Z" fill="url(#screenGrad_{{ $size }})"/>
            <circle cx="16" cy="9.3" r="1" fill="#FFFFFF"/>
            
            <!-- Vertical Center Seam Highlight -->
            <line x1="16" y1="14.5" x2="16" y2="26.5" stroke="#F59E0B" stroke-width="1" stroke-linecap="round" stroke-opacity="0.8"/>

            <defs>
                <linearGradient id="topGrad_{{ $size }}" x1="4" y1="2.5" x2="28" y2="14.5" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FDE68A"/>
                    <stop offset="50%" stop-color="#F59E0B"/>
                    <stop offset="100%" stop-color="#D97706"/>
                </linearGradient>
                <linearGradient id="leftGrad_{{ $size }}" x1="4" y1="8.5" x2="16" y2="26.5" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#334155"/>
                    <stop offset="100%" stop-color="#0F172A"/>
                </linearGradient>
                <linearGradient id="rightGrad_{{ $size }}" x1="16" y1="14.5" x2="28" y2="26.5" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#1E293B"/>
                    <stop offset="100%" stop-color="#090D16"/>
                </linearGradient>
                <linearGradient id="screenGrad_{{ $size }}" x1="11" y1="6.8" x2="21" y2="11.8" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#38BDF8"/>
                    <stop offset="100%" stop-color="#0284C7"/>
                </linearGradient>
            </defs>
        </svg>
    </div>

    @if ($withText)
        <div class="flex flex-col leading-none">
            <span class="{{ $current['title'] }} {{ $textClass }}">
                {{ $slot->isNotEmpty() ? $slot : 'Gudang Gadget' }}
            </span>
            @if ($subtext)
                <span class="{{ $current['sub'] }} font-medium text-navy-400 mt-0.5 tracking-normal">
                    {{ $subtext }}
                </span>
            @endif
        </div>
    @endif
</div>
