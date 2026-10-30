<?php
declare(strict_types=1);

namespace Perpustakaan\Model;

/**
 * Representasi Bab dalam Buku (Komposisi).
 */
final class Bab
{
    public function __construct(
        private readonly int $nomor,
        private readonly string $judul
    ) {}

    public function getNomor(): int
    {
        return $this->nomor;
    }

    public function getJudul(): string
    {
        return $this->judul;
    }

    public function __toString(): string
    {
        return sprintf("Bab %d: %s", $this->nomor, $this->judul);
    }
}
