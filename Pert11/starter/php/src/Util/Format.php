<?php
declare(strict_types=1);

namespace Perpustakaan\Util;

use Carbon\Carbon;

/**
 * Utilitas pemformatan mata uang Rupiah dan tanggal Indonesia.
 */
final class Format
{
    private function __construct() {}

    public static function formatRupiah(int|float $nominal): string
    {
        return 'Rp' . number_format($nominal, 2, ',', '.');
    }

    public static function formatTanggal(?Carbon $tanggal): string
    {
        if ($tanggal === null) {
            return '-';
        }
        return $tanggal->locale('id')->isoFormat('D MMMM Y');
    }
}
