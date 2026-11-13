<?php

namespace Perpustakaan\Exception;

use Perpustakaan\Exception\PerpustakaanException;

final class AnggotaTidakDitemukanException extends PerpustakaanException
{
    // Mengikuti pola BukuTidakDitemukanException. Membawa $idAnggota.
    public function __construct(private readonly string $idAnggota)
    {
        parent::__construct("Anggota dengan ID {$idAnggota} tidak ditemukan.");
    }

    public function getIdAnggota(): string
    {
        return $this->idAnggota;
    }
}
