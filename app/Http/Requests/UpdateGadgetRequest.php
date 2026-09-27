<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGadgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:Tersedia,Habis,Tidak Dijual'],
            'tanggal_pembelian' => ['nullable', 'date'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($this->route('id'))],
            'supplier' => ['nullable', 'string', 'max:255'],
            'lokasi_rak' => ['nullable', 'string', 'max:100'],
            'harga_beli' => ['nullable', 'numeric', 'min:0'],
            'harga_jual' => ['nullable', 'numeric', 'min:0'],
            'harga_promo' => ['nullable', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'is_published' => ['nullable', 'in:0,1'],
            'is_featured' => ['nullable', 'in:0,1'],
            'condition' => ['nullable', 'in:new,like-new,used'],
            'warranty_info' => ['nullable', 'string', 'max:255'],
            'specifications' => ['nullable', 'array'],
            'tier_grosir_min_qty' => ['nullable', 'integer', 'min:3'],
            'tier_grosir_price' => ['nullable', 'numeric', 'min:0'],
            'tier_partai_min_qty' => ['nullable', 'integer', 'min:4'],
            'tier_partai_price' => ['nullable', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'sku.unique' => 'SKU sudah digunakan produk lain.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}