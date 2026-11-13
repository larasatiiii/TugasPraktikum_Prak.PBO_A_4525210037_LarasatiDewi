<?php
declare(strict_types=1);

namespace Perpustakaan\Tests;

use PHPUnit\Framework\TestCase;
use Perpustakaan\Exception\BatasPinjamTerlampauiException;
use Perpustakaan\Exception\BukuSedangDipinjamException;
use Perpustakaan\Exception\BukuTidakDitemukanException;
use Perpustakaan\Model\Buku;
use Perpustakaan\Model\KoleksiBuku;
use Perpustakaan\Service\LibraryService;

/**
 * Sesi 11 — unit test PHPUnit.
 *
 * Letakkan di tests/ dan pastikan autoload-dev pada composer.json sudah
 * memetakan Perpustakaan\Tests\ ke tests/.
 *
 * Jalankan: ./vendor/bin/phpunit tests
 */
final class KoleksiBukuTest extends TestCase
{
    private KoleksiBuku $koleksi;

    protected function setUp(): void
    {
        // Dijalankan ulang sebelum SETIAP pengujian.
        $this->koleksi = new KoleksiBuku();
    }

    /** Mengisi koleksi dengan tiga buku: stok 2, 0, dan 1. */
    private function isiTigaBuku(): void
    {
        $this->koleksi->simpan(new Buku('978-1', 'PBO', 'Budi', 2));
        $this->koleksi->simpan(new Buku('978-2', 'Basis Data', 'Sari', 0));
        $this->koleksi->simpan(new Buku('978-3', 'Web', 'Budi', 1));
    }

    public function testItemYangDisimpanDapatDitemukanKembali(): void
    {
        $this->koleksi->simpan(new Buku('978-1', 'PBO', 'Budi', 2));

        $hasil = $this->koleksi->cariByIsbn('978-1');

        $this->assertNotNull($hasil);
        $this->assertSame('PBO', $hasil->getJudul());
    }

    public function testMencariIsbnYangTidakAdaMengembalikanNull(): void
    {
        $this->assertNull($this->koleksi->cariByIsbn('999'));
    }

    public function testKoleksiBaruBerisiNolItem(): void
    {
        // count() bekerja karena kelasnya implements Countable.
        $this->assertCount(0, $this->koleksi);
        $this->assertSame(0, count($this->koleksi));
    }

    public function testFilterHanyaMengembalikanYangLolosPredikat(): void
    {
        $this->isiTigaBuku();

        $tersedia = $this->koleksi->filter(fn (Buku $b): bool => $b->tersedia());

        $this->assertCount(2, $tersedia);
        $this->assertSame(
            ['978-1', '978-3'],
            array_map(fn (Buku $b): string => $b->getNomorInduk(), $tersedia->semua())
        );
    }

    public function testJumlahkanMenghitungTotalDenganBenar(): void
    {
        $this->isiTigaBuku();

        $total = $this->koleksi->jumlahkan(fn (Buku $b): float => $b->getJumlahEksemplar());

        $this->assertSame(3.0, $total);
    }

    // --- Pengujian tambahan dari skenario sesi 10 ---

    public function testSimpanIsbnYangSamaMenimpaBukuLama(): void
    {
        $this->koleksi->simpan(new Buku('978-1', 'PBO', 'Budi', 2));
        $this->koleksi->simpan(new Buku('978-1', 'PBO Edisi 2', 'Budi', 2));

        $this->assertCount(1, $this->koleksi);
        $this->assertSame('PBO Edisi 2', $this->koleksi->cariByIsbn('978-1')?->getJudul());
    }

    public function testFilterMengembalikanIndeksBerurutan(): void
    {
        $this->isiTigaBuku();

        $tersedia = $this->koleksi->filter(fn (Buku $b): bool => $b->tersedia());

        // Tanpa array_values, indeksnya menjadi [0, 2] dan json_encode berubah menjadi objek.
        $this->assertSame([0, 1], array_keys($tersedia->semua()));
    }

    public function testMapMengembalikanDaftarJudul(): void
    {
        $this->isiTigaBuku();

        $judul = $this->koleksi->map(fn (Buku $b): string => $b->getJudul());

        $this->assertSame(['PBO', 'Basis Data', 'Web'], $judul);
    }

    public function testKelompokkanBerdasarkanPenulis(): void
    {
        $this->isiTigaBuku();

        $kelompok = $this->koleksi->kelompokkan(fn (Buku $b): string => $b->getPenulis());

        $this->assertSame(['Budi', 'Sari'], array_keys($kelompok));
        $this->assertCount(2, $kelompok['Budi']);
        $this->assertCount(1, $kelompok['Sari']);
    }

    public function testPinjamIsbnTidakTerdaftarMelemparBukuTidakDitemukan(): void
    {
        $layanan = new LibraryService();
        $layanan->daftarkanAnggota('A1');

        $this->expectException(BukuTidakDitemukanException::class);
        $layanan->pinjam('A1', '000');
    }

    public function testPinjamMelewatiBatasMelemparBatasPinjamTerlampaui(): void
    {
        $layanan = new LibraryService();
        $layanan->tambahBuku('978-1', 'PBO', 5);
        $layanan->daftarkanAnggota('A1');
        for ($i = 0; $i < LibraryService::BATAS_PINJAM; $i++) {
            $layanan->pinjam('A1', '978-1');
        }

        $this->expectException(BatasPinjamTerlampauiException::class);
        $layanan->pinjam('A1', '978-1');
    }

    public function testPinjamBukuHabisMelemparBukuSedangDipinjam(): void
    {
        $layanan = new LibraryService();
        $layanan->tambahBuku('978-1', 'PBO', 1);
        $layanan->daftarkanAnggota('A1');
        $layanan->daftarkanAnggota('A2');
        $layanan->pinjam('A1', '978-1');

        $this->expectException(BukuSedangDipinjamException::class);
        $layanan->pinjam('A2', '978-1');
    }
}
