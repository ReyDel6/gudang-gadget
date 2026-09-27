<?php

namespace App\Support;

class Pembayaran
{
    public const CASH = 'cash';
    public const QRIS = 'qris';
    public const TRANSFER = 'transfer';
    public const DEBIT = 'debit';
    public const SPLIT = 'split';

    public const METODE = [
        self::CASH => 'Tunai',
        self::QRIS => 'QRIS',
        self::TRANSFER => 'Transfer Bank',
        self::DEBIT => 'Debit / EDC',
        self::SPLIT => 'Split',
    ];

    public const STATUS = [
        'paid' => 'Lunas',
        'void' => 'Batal / Void',
        'refunded' => 'Retur',
    ];

    public static function labelMetode(?string $kode): string
    {
        if ($kode && isset(self::METODE[$kode])) {
            return self::METODE[$kode];
        }

        return 'Tunai';
    }

    public static function labelStatus(?string $kode): string
    {
        if ($kode && isset(self::STATUS[$kode])) {
            return self::STATUS[$kode];
        }

        return 'Lunas';
    }
}