<?php

namespace App\Http\Requests;

use App\Models\StokLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockAdjustRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gadget_id' => ['required', 'integer', 'exists:products,id'],
            'jenis' => [
                'required',
                Rule::in([
                    StokLog::TIPE_PENERIMAAN,
                    StokLog::TIPE_PENGELUARAN,
                    StokLog::TIPE_RETUR,
                    'Penyesuaian (+)', 'Penyesuaian (-)',
                ]),
            ],
            'qty' => ['required', 'integer', 'min:1'],
            'alasan' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'gadget_id.required' => 'Pilih produk terlebih dahulu.',
            'gadget_id.exists' => 'Produk tidak ditemukan.',
            'jenis.in' => 'Jenis mutasi tidak valid.',
            'qty.required' => 'Jumlah mutasi wajib diisi.',
            'qty.min' => 'Jumlah mutasi minimal 1.',
            'alasan.required' => 'Alasan mutasi wajib diisi.',
            'alasan.max' => 'Alasan maksimal 255 karakter.',
        ];
    }
}