@extends('layouts.store')

@section('title', 'Kalkulator Tukar Tambah')

@section('meta_desc', 'Cek taksiran nilai unit bekas Anda untuk tukar tambah di Gudang Gadget. Proses cepat, inspeksi di tempat, dan langsung bisa jadi potongan harga.')

@section('content')

    <div class="bg-navy-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <p class="text-xs font-bold text-gold-400 uppercase tracking-widest">Tukar Tambah</p>
            <h1 class="text-3xl sm:text-4xl font-black mt-2">Ketuk, kon-sis, ganti baru</h1>
            <p class="mt-3 text-slate-300 max-w-2xl">Mau upgrade gadget? Bawa unit lama Anda, kami hitung taksirannya saat itu juga, dan nilai tukar langsung dipotong dari harga unit baru. Tanpa ribet, tanpa nunggu lama.</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

        <div class="bg-white rounded-2xl border border-navy-100 p-6 sm:p-8">
            <h2 class="font-black text-navy-800 text-lg">Cek Taksiran Unit Bekas Anda</h2>
            <p class="text-sm text-navy-500 mt-1">Pilih brand → model → kondisi unit, dan lihat estimasi nilainya.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">Brand</label>
                    <select id="fBrand" onchange="filterModel()"
                            class="w-full mt-1 rounded-xl border border-navy-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white">
                        <option value="">— Pilih brand —</option>
                        @foreach ($data as $b)
                            <option value="{{ $b['brand'] }}">{{ $b['brand'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">Model & Kapasitas</label>
                    <select id="fModel" disabled onchange="sinkronKapasitas()"
                            class="w-full mt-1 rounded-xl border border-navy-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white disabled:opacity-50">
                        <option value="">— Pilih model —</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">Grade / Kondisi</label>
                    <div class="grid grid-cols-4 gap-1.5 mt-1.5">
                        @foreach (['A', 'B', 'C', 'D'] as $g)
                            <button type="button" data-grade="{{ $g }}"
                                    class="grade-btn rounded-lg py-2 text-sm font-black border-2 border-navy-200 text-navy-500 hover:border-gold-500">Grade {{ $g }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="pilihanRow" class="hidden mt-4 flex items-center gap-2 text-sm font-semibold text-navy-600">
                <span>Unit pilihan:</span>
                <span id="pilihanLabel" class="text-gold-700"></span>
            </div>

            <div id="hasil" class="hidden mt-5 rounded-2xl border-2 border-gold-400 bg-gold-50 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Estimasi Nilai Tukar</p>
                    <p id="nilaiTeks" class="text-3xl font-black text-navy-900 mt-1">Rp 0</p>
                    <p id="gradeCatatan" class="text-xs text-navy-500 mt-1 font-medium"></p>
                </div>
                <div class="text-sm text-navy-700 space-y-1.5">
                    <p>✔ Nilai di atas adalah <b>estimasi</b> — harga final setelah inspeksi fisik unit di toko.</p>
                    <p>✔ Nilai penuh dipotong langsung dari harga gadget baru Anda.</p>
                    <a id="waLink" href="#" target="_blank"
                       class="inline-block mt-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black px-4 py-2 rounded-lg transition-colors">
                        Tanya via WhatsApp →
                    </a>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-navy-100 bg-navy-50 p-4 text-xs text-navy-600 space-y-1">
                <p><b>Grade A</b> — mulus tanpa baret berarti, fungsi 100% normal.</p>
                <p><b>Grade B</b> — bekas wajar, masih rapi, fungsi normal.</p>
                <p><b>Grade C</b> — baret terlihat ± halus, kecil, atau fungsi tetap normal.</p>
                <p><b>Grade D</b> — kerusakan fisik / fungsi bermasalah; nilai menyesuaikan hasil inspeksi.</p>
            </div>
        </div>
    </div>

    @php
        $json = $data->map(fn ($b) => [
            'brand' => $b['brand'],
            'models' => $b['models'],
        ]);
    @endphp

    <script>
        const data = @json($json);
        const fmtMata = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        function modifModelGrup(brand) {
            return (data.find(b => b.brand === brand) || { models: [] }).models;
        }

        function filterModel() {
            const brand = document.getElementById('fBrand').value;
            const sel = document.getElementById('fModel');
            sel.innerHTML = '<option value="">— Pilih model —</option>';
            sel.disabled = !brand;
            if (!brand) { bersihHasil(); return; }
            (modifModelGrup(brand) || []).forEach(m => {
                const o = document.createElement('option');
                o.value = m.id;
                o.textContent = m.model + (m.capacity ? ' (' + m.capacity + ')' : '');
                o.dataset.capacity = m.capacity || '';
                sel.appendChild(o);
            });
            bersihHasil();
        }

        function sinkronKapasitas() {
            bersihHasil();
        }

        function unitTerpilih() {
            const id = document.getElementById('fModel').value;
            if (!id) return null;
            for (const b of data) {
                const ditemukan = (b.models || []).find(m => String(m.id) === id);
                if (ditemukan) return ditemukan;
            }
            return null;
        }

        function bersihHasil() {
            document.getElementById('hasil').classList.add('hidden');
            document.getElementById('pilihanRow').classList.add('hidden');
            document.querySelectorAll('.grade-btn').forEach(b => {
                b.classList.remove('border-gold-500', 'bg-gold-100', 'text-navy-900');
                b.classList.add('border-navy-200', 'text-navy-500');
            });
        }

        document.querySelectorAll('.grade-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const u = unitTerpilih();
                if (!u) { alert('Pilih model & kapasitas dulu.'); return; }
                const grade = btn.dataset.grade;
                const nilai = u[grade.toLowerCase()] || 0;
                document.querySelectorAll('.grade-btn').forEach(b => {
                    const aktif = b.dataset.grade === grade;
                    b.classList.toggle('border-gold-500', aktif);
                    b.classList.toggle('bg-gold-100', aktif);
                    b.classList.toggle('text-navy-900', aktif);
                    b.classList.toggle('border-navy-200', !aktif);
                    b.classList.toggle('text-navy-500', !aktif);
                });
                const brand = document.getElementById('fBrand').value;
                const capacity = u.capacity ? ' ' + u.capacity : '';
                document.getElementById('pilihanLabel').textContent = brand + ' ' + u.model + capacity + ' · Grade ' + grade;
                document.getElementById('pilihanRow').classList.remove('hidden');
                document.getElementById('nilaiTeks').textContent = fmtMata(nilai);
                document.getElementById('gradeCatatan').textContent = 'Estimasi Grade ' + grade + '. Harga final menunggu inspeksi fisik di toko.';
                document.getElementById('hasil').classList.remove('hidden');
                const teksPesan = 'Halo Gudang Gadget, saya mau tanya-tanya tukar tambah: ' + brand + ' ' + u.model +
                    (capacity ? ' ' + capacity : '') + ' kondisi Grade ' + grade + '. Estimasi nilainya Rp ' +
                    Math.round(nilai).toLocaleString('id-ID') + '. Apakah bisa dipotong dari harga unit baru?';
                document.getElementById('waLink').href =
                    'https://wa.me/?text=' + encodeURIComponent(teksPesan);
            });
        });
    </script>

@endsection