@extends('layouts.store')

@section('title', 'Permohonan Servis Terkirim')

@section('meta_desc', 'Permohonan servis Anda telah kami terima. Pantau status pengerjaan secara online di ' . $settings['store_name'] . '.')

@section('content')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-12">

        <div class="text-center mb-8">
            <div class="grid place-items-center w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 mb-4">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-navy-900">Permohonan Servis Terkirim!</h1>
            <p class="text-sm text-navy-500 mt-2 max-w-md mx-auto">
                Terima kasih! Tim {{ $settings['store_name'] }} akan memeriksa gadget Anda dan menghubungi untuk konfirmasi.
            </p>
        </div>

        @if ($tiket)
            <div class="bg-white rounded-2xl border border-navy-100 p-6 text-center shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-navy-400">Nomor Tiket Servis</p>
                <div class="inline-flex items-center gap-2 mt-2 px-5 py-3 rounded-xl bg-navy-900 text-gold-400">
                    <span class="font-mono font-black text-2xl tracking-wider">{{ $tiket->no_tiket }}</span>
                    <button type="button" onclick="navigator.clipboard?.writeText('{{ $tiket->no_tiket }}').then(()=>showToast('Nomor tiket disalin'))"
                            class="text-gold-500 hover:text-gold-300" aria-label="Salin nomor tiket">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-navy-600 mt-4">
                    Unit: <b>{{ $tiket->device_brand }} {{ $tiket->device_model }}</b>
                </p>
                <p class="text-sm text-navy-500">Keluhan: {{ $tiket->problem_description }}</p>

                <div class="mt-6 space-y-3">
                    <a href="{{ route('shop.tracking', ['q' => $tiket->no_tiket]) }}"
                       class="block w-full bg-navy-800 hover:bg-navy-700 text-white font-black py-3.5 rounded-xl text-sm transition-colors">
                        Pantau Status Servis Saya
                    </a>
                    <a href="{{ 'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) . '/?text=' . rawurlencode('Halo, saya sudah mengajukan servis dengan tiket ' . $tiket->no_tiket . ' (' . $tiket->device_brand . ' ' . $tiket->device_model . ') dan ingin konfirmasi.') }}"
                       target="_blank" rel="noopener"
                       class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3.5 rounded-xl text-sm transition-colors">
                        Chat Konfirmasi via WhatsApp
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-navy-100 p-6 text-center shadow-sm">
                <p class="text-sm text-navy-600">Nomor tiket tidak ditemukan.</p>
            </div>
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('shop.home') }}" class="text-sm font-bold text-gold-600 hover:text-gold-700">← Kembali ke beranda toko</a>
        </div>
    </div>

@endsection