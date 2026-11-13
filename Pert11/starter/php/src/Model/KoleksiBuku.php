<?php
declare(strict_types=1);

namespace Perpustakaan\Model;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Sesi 11 — PHP tidak punya generics.
 *
 * Padanannya ada tiga, dan biasanya dipakai bersama:
 *   1. Anotasi PHPDoc (@param Buku[], @return Buku[]) — dibaca PHPStan/Psalm dan IDE
 *   2. Typed collection class — kelas pembungkus yang MEMVALIDASI isinya (berkas ini)
 *   3. Union type untuk kasus sederhana
 *
 * Laravel memakai pendekatan kedua secara luas lewat kelas Collection.
 *
 * IteratorAggregate -> objek ini bisa dipakai langsung di foreach
 * Countable         -> objek ini bisa dipakai langsung di count()
 */
final class KoleksiBuku implements IteratorAggregate, Countable
{
    /** @var array<string,Buku> isbn => Buku */
    private array $items = [];

    /** @param Buku[] $buku */
    public function __construct(array $buku = [])
    {
        foreach ($buku as $b) {
            $this->simpan($b);   // lewat simpan() agar type hint-nya berlaku
        }
    }

    /** Simpan buku dengan kunci ISBN-nya. ISBN yang sama menimpa buku lama. */
    public function simpan(Buku $b): void
    {
        $this->items[$b->getNomorInduk()] = $b;
    }

    /** Kembalikan Buku atau null. Perhatikan tipe kembaliannya: ?Buku */
    public function cariByIsbn(string $isbn): ?Buku
    {
        return $this->items[$isbn] ?? null;
    }

    /** @return Buku[] */
    public function semua(): array { return array_values($this->items); }

    /**
     * Kembalikan KoleksiBuku baru berisi item yang lolos predikat.
     * PERHATIAN: array_filter(array, callable) — array DULU.
     * array_values dipakai agar indeksnya tidak berlubang.
     */
    public function filter(callable $predikat): self
    {
        return new self(array_values(array_filter($this->items, $predikat)));
    }

    /**
     * Kembalikan larik hasil pemetaan.
     * PERHATIAN: array_map(callable, array) — callable DULU.
     * Urutan argumennya BERBEDA dari array_filter. Ini sumber
     * kesalahan yang sangat sering terjadi.
     *
     * @return mixed[]
     */
    public function map(callable $f): array
    {
        return array_map($f, array_values($this->items));
    }

    /** Jumlahkan hasil $f atas seluruh item. */
    public function jumlahkan(callable $f): float
    {
        return array_reduce(
            $this->items,
            fn (float $total, Buku $b): float => $total + $f($b),
            0.0
        );
    }

    /**
     * Kelompokkan item berdasarkan kunci hasil $kunci($buku).
     * @return array<string,Buku[]>
     */
    public function kelompokkan(callable $kunci): array
    {
        $hasil = [];
        foreach ($this->items as $b) {
            $hasil[(string) $kunci($b)][] = $b;
        }
        return $hasil;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator(array_values($this->items));
    }

    public function count(): int { return count($this->items); }
}
