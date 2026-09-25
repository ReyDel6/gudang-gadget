<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lokasi_rak_tujuan' => ['required', 'string', 'max:100'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'lokasi_rak_tujuan.required' => 'Rak tujuan wajib diisi.',
            'qty.min' => 'Jumlah transfer minimal 1.',
        ];
    }
}