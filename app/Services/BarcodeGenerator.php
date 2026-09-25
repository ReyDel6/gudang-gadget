<?php

namespace App\Services;

class BarcodeGenerator
{
    private const NARROW = 1;
    private const WIDE = 2;
    private const GAP = 2;
    private const HEIGHT = 42;

    /**
     * Peta karakter Code 39 (1 = elemen lebar, 0 = elemen sempit).
     * Elemen berselang-seling bar/space mulai dari bar.
     */
    private const PATTERNS = [
        '0' => '000110100', '1' => '100100001', '2' => '001100001', '3' => '101100000',
        '4' => '000110001', '5' => '100110000', '6' => '001110000', '7' => '000100101',
        '8' => '100100100', '9' => '001100100', 'A' => '100001001', 'B' => '001001001',
        'C' => '101001000', 'D' => '000011001', 'E' => '100011000', 'F' => '001011000',
        'G' => '000001101', 'H' => '100001100', 'I' => '001001100', 'J' => '000011100',
        'K' => '100000011', 'L' => '001000011', 'M' => '101000010', 'N' => '000010011',
        'O' => '100010010', 'P' => '001010010', 'Q' => '000000111', 'R' => '100000110',
        'S' => '001000110', 'T' => '000010110', 'U' => '110000001', 'V' => '011000001',
        'W' => '111000000', 'X' => '010010001', 'Y' => '110010000', 'Z' => '011010000',
        '-' => '010000101', '.' => '110000100', ' ' => '011000100', '$' => '010101000',
        '/' => '010100010', '+' => '010001010', '%' => '000101010', '*' => '010010100',
    ];

    public static function code39(?string $text): string
    {
        $text = (string) $text;
        $chars = [];
        foreach (str_split(strtoupper($text)) as $ch) {
            if (isset(self::PATTERNS[$ch])) {
                $chars[] = $ch;
            }
        }

        $symbols = array_merge(['*'], $chars, ['*']);
        if (count($symbols) <= 2) {
            return '';
        }

        $scale = 2;
        $x = 0;
        $rects = [];

        foreach ($symbols as $idx => $char) {
            if ($idx > 0) {
                $x += self::GAP;
            }
            $pattern = self::PATTERNS[$char];
            for ($i = 0; $i < 9; $i++) {
                $width = $pattern[$i] === '1' ? self::WIDE : self::NARROW;
                if ($i % 2 === 0) { // bar
                    $rects[] = "    <rect x=\"{$x}\" y=\"0\" width=\"{$width}\" height=\"" . self::HEIGHT . "\" fill=\"#0E1D30\"/>";
                }
                $x += $width;
            }
        }

        $svgWidth = $x + 8;
        $rects = implode("\n", $rects);

        return '<svg class="barcode-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $svgWidth . ' ' . (self::HEIGHT + 18) . '" role="img" aria-label="Barcode ' . htmlspecialchars($text) . '">
' . $rects . '
    <text x="' . ($svgWidth / 2) . '" y="' . (self::HEIGHT + 14) . '" text-anchor="middle" font-family="monospace" font-size="12" fill="#0E1D30">' . htmlspecialchars($text ?: '—') . '</text>
</svg>';
    }
}