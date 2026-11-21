<?php
declare(strict_types=1);

namespace Perpustakaan\Service;

use Perpustakaan\Denda\AturanDenda;
use Perpustakaan\Exception\AnggotaTidakDitemukanException;
use Perpustakaan\Exception\BatasPinjamTerlampauiException;
use Perpustakaan\Exception\BukuSedangDipinjamException;
use Perpustakaan\Exception\BukuTidakDitemukanException;
use Perpustakaan\Model\Buku;
use Perpustakaan\Repository\BookRepository;

final class LibraryService
{
    public const int BATAS_PINJAM = 3;

    /** @var array<string,int> */
    private array $pinjaman = [];

    // TODO 5: terima BookRepository dan AturanDenda lewat constructor (tanpa new di dalam service)
    public function __construct(
        private readonly BookRepository $repo,
        private readonly AturanDenda $aturanDenda,
    ) {}

    // TODO 6: pindahkan method aturan bisnis ke sini dan gunakan $this->repo
    public function tambahBuku(string $isbn, string $judulBuku, string $penulis, int $eksemplar): void
    {
        $buku = $this->repo->cariByIsbn($isbn);

        if ($buku !== null) {
            for ($i = 0; $i < $eksemplar; $i++) {
                $buku->tambahStok();
            }
        } else {
            $bukuBaru = new Buku($isbn, $judulBuku, $penulis, $eksemplar);
            $this->repo->simpan($bukuBaru);
        }
    }

    public function daftarkanAnggota(string $idAnggota): void
    {
        $this->pinjaman[$idAnggota] ??= 0;
    }

    public function pinjam(string $idAnggota, string $isbn): void
    {
        if (!array_key_exists($idAnggota, $this->pinjaman)) {
            throw new AnggotaTidakDitemukanException($idAnggota);
        }

        $bukuDipinjam = $this->repo->cariByIsbn($isbn);

        if ($bukuDipinjam === null) {
            throw new BukuTidakDitemukanException($isbn);
        }

        if (!$bukuDipinjam->tersedia()) {
            throw new BukuSedangDipinjamException($isbn, $bukuDipinjam->getJudul());
        }

        if ($this->pinjaman[$idAnggota] >= self::BATAS_PINJAM) {
            throw new BatasPinjamTerlampauiException($idAnggota, self::BATAS_PINJAM);
        }

        $this->repo->kurangiStok($bukuDipinjam);
        $this->pinjaman[$idAnggota]++;
    }

    public function hitungDenda(int $hariTerlambat): float
    {
        return $this->aturanDenda->hitung($hariTerlambat);
    }

    public function getNamaAturanDenda(): string
    {
        return $this->aturanDenda->nama();
    }

    public function sisaEksemplar(string $isbn): int
    {
        $buku = $this->repo->cariByIsbn($isbn);
        return $buku !== null ? $buku->getJumlahEksampelar() : 0;
    }

    /** @return array<string,string> */
    public function getSemuaJudul(): array
    {
        $semuaJudul = [];
        foreach ($this->repo->cariSemua() as $buku) {
            $semuaJudul[$buku->getNomorInduk()] = $buku->getJudul();
        }
        return $semuaJudul;
    }

    /** @return Buku[] */
    public function getBukuTersedia(): array
    {
        return array_values(array_filter(
            $this->repo->cariSemua(),
            fn(Buku $b) => $b->tersedia()
        ));
    }

    public function getSemuaJudulDigabung(): string
    {
        $daftarJudul = array_map(
            fn(Buku $b) => $b->getJudul(),
            $this->repo->cariSemua()
        );
        return implode(', ', $daftarJudul);
    }

    /** @return array<string,int> */
    public function getJumlahBukuPerPenulis(): array
    {
        $hasil = [];
        foreach ($this->repo->cariSemua() as $buku) {
            $penulis = $buku->getPenulis();
            $hasil[$penulis] = ($hasil[$penulis] ?? 0) + 1;
        }
        return $hasil;
    }
}
