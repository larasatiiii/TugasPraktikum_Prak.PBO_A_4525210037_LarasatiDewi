<?php
declare(strict_types=1);

namespace Perpustakaan\Model;

use InvalidArgumentException;

/**
 * Entitas Anggota Perpustakaan.
 */
final class Anggota
{
    public function __construct(
        private readonly string $nomorAnggota,
        private readonly string $nama,
        private readonly string $jenisKeanggotaan
    ) {
        if (trim($nomorAnggota) === '') {
            throw new InvalidArgumentException("Nomor anggota tidak boleh kosong.");
        }
        if (trim($nama) === '') {
            throw new InvalidArgumentException("Nama tidak boleh kosong.");
        }
    }

    public function getNomorAnggota(): string
    {
        return $this->nomorAnggota;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getJenisKeanggotaan(): string
    {
        return $this->jenisKeanggotaan;
    }

    public function __toString(): string
    {
        return sprintf("[%s] %s (%s)", $this->nomorAnggota, $this->nama, $this->jenisKeanggotaan);
    }
}
