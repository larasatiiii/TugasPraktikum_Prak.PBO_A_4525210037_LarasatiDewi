<?php
declare(strict_types=1);

namespace Perpustakaan\Model;

use InvalidArgumentException;
use IllegalStateException;

/**
 * Entitas Buku dalam Sistem Perpustakaan.
 */
final class Buku
{
    /** @var Bab[] */
    private array $bab = [];

    public function __construct(
        private readonly string $nomorInduk,
        private readonly string $judul,
        private readonly string $penulis,
        private int $jumlahEksemplar
    ) {
        if ($this->jumlahEksemplar < 0) {
            throw new InvalidArgumentException("Jumlah eksemplar tidak boleh negatif.");
        }
    }

    /**
     * Bab dibuat di dalam Buku (konsep Komposisi).
     */
    public function tambahBab(int $nomor, string $judulBab): void
    {
        $this->bab[] = new Bab($nomor, $judulBab);
    }

    /**
     * @return Bab[]
     */
    public function getBab(): array
    {
        return $this->bab;
    }

    public function tersedia(): bool
    {
        return $this->jumlahEksemplar > 0;
    }

    public function kurangiStok(): void
    {
        if (!$this->tersedia()) {
            throw new \RuntimeException("Stok buku habis.");
        }
        $this->jumlahEksemplar--;
    }

    public function tambahStok(): void
    {
        $this->jumlahEksemplar++;
    }

    public function getNomorInduk(): string
    {
        return $this->nomorInduk;
    }

    public function getJudul(): string
    {
        return $this->judul;
    }

    public function getPenulis(): string
    {
        return $this->penulis;
    }

    public function getJumlahEksemplar(): int
    {
        return $this->jumlahEksemplar;
    }

    public function __toString(): string
    {
        return sprintf(
            "%-10s %-32s %-20s [%d eks, %d bab]",
            $this->nomorInduk,
            $this->judul,
            $this->penulis,
            $this->jumlahEksemplar,
            count($this->bab)
        );
    }
}
