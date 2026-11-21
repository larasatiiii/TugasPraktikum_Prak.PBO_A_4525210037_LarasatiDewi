<?php
declare(strict_types=1);

namespace Perpustakaan\Tests\Repository;

use Perpustakaan\Denda\DendaMahasiswa;
use Perpustakaan\Exception\AnggotaTidakDitemukanException;
use Perpustakaan\Exception\BukuTidakDitemukanException;
use PHPUnit\Framework\TestCase;
use Perpustakaan\Model\Buku;
use Perpustakaan\Repository\InMemoryBookRepository;
use Perpustakaan\Repository\KoleksiBuku as RepositoryKoleksiBuku;
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
    private RepositoryKoleksiBuku $koleksi;

    protected function setUp(): void
    {
        // Dijalankan ulang sebelum SETIAP pengujian.
        $this->koleksi = new RepositoryKoleksiBuku();
    }

    public function testItemYangDisimpanDapatDitemukanKembali(): void
    {
        // TODO 1
        $buku = new Buku('978-1', 'Pemrograman PHP', 'Penulis A', 5);
        $this->koleksi->simpan($buku);

        $hasil = $this->koleksi->cariByIsbn('978-1');

        $this->assertNotNull($hasil, 'Buku seharusnya ditemukan');
        $this->assertEquals('Pemrograman PHP', $hasil->getJudul(), 'Judul buku tidak sesuai');

    }

    public function testMencariIsbnYangTidakAdaMengembalikanNull(): void
    {
        // TODO 2
        $hasil = $this->koleksi->cariByIsbn('ISBN-KOSONG');
        
        $this->assertNull($hasil, 'Mencari ISBN yang tidak ada harus mengembalikan null');
    }

    public function testKoleksiBaruBerisiNolItem(): void
    {
        // TODO 3: perhatikan bahwa count($this->koleksi) bekerja
        //         karena kelasnya implements Countable.
        $this->assertCount(0, $this->koleksi, 'Jumlah item pada koleksi baru harus 0');
        $this->assertEmpty($this->koleksi->semua(), 'Array koleksi harus kosong');
    }

    public function testFilterHanyaMengembalikanYangLolosPredikat(): void
    {
        // TODO 4
        $this->koleksi->simpan(new Buku('978-1', 'Buku A', 'Penulis A', 0));
        $this->koleksi->simpan(new Buku('978-2', 'Buku B', 'Penulis B', 5));

        $hasilFilter = $this->koleksi->filter(fn(Buku $b) => $b->getJumlahEksampelar() > 0);

        $this->assertCount(1, $hasilFilter, 'Hanya boleh ada 1 buku yang lolos filter (stok > 0)');
        $this->assertEquals('Buku B', $hasilFilter->semua()[0]->getJudul());
    }

    public function testJumlahkanMenghitungTotalDenganBenar(): void
    {
        // TODO 5
        $this->koleksi->simpan(new Buku('978-1', 'Buku A', 'Penulis A', 2));
        $this->koleksi->simpan(new Buku('978-2', 'Buku B', 'Penulis B', 3));

        $total = $this->koleksi->jumlahkan(fn(Buku $b) => $b->getJumlahEksampelar());

        $this->assertEquals(5.0, $total, 'Total eksemplar harus 5');
    }

    // TODO 6: terjemahkan minimal LIMA skenario dari skenario-uji.md sesi 10
    //         menjadi kasus uji di sini, termasuk satu yang memastikan
    //         exception yang benar dilemparkan.
    //         Petunjuk: $this->expectException(BukuTidakDitemukanException::class);

    public function testSimpanBukuDenganIsbnSamaAkanMenimpaDataLama(): void
    {
        $this->koleksi->simpan(new Buku('978-3', 'Buku Lama', 'Penulis X', 1));
        $this->koleksi->simpan(new Buku('978-3', 'Buku Baru', 'Penulis X', 2));

        $this->assertCount(1, $this->koleksi, 'Jumlah buku harus tetap 1 karena ISBN sama ditimpa');
        $this->assertEquals('Buku Baru', $this->koleksi->cariByIsbn('978-3')->getJudul());
    }

    public function testMapMengubahBentukDataDenganBenar(): void
    {
        $this->koleksi->simpan(new Buku('978-1', 'Buku A', 'Penulis', 1));
        $this->koleksi->simpan(new Buku('978-2', 'Buku B', 'Penulis', 1));

        $hasilMap = $this->koleksi->map(fn(Buku $b) => $b->getJudul());

        $this->assertEquals(['Buku A', 'Buku B'], $hasilMap);
    }

    public function testKelompokkanMemisahkanBerdasarkanPenulis(): void
    {
        $this->koleksi->simpan(new Buku('1', 'Buku A', 'Penulis X', 1));
        $this->koleksi->simpan(new Buku('2', 'Buku B', 'Penulis X', 1));
        $this->koleksi->simpan(new Buku('3', 'Buku C', 'Penulis Y', 1));

        $hasilKelompok = $this->koleksi->kelompokkan(fn(Buku $b) => $b->getPenulis());

        $this->assertCount(2, $hasilKelompok['Penulis X']);
        $this->assertCount(1, $hasilKelompok['Penulis Y']);
    }

    public function testExceptionPinjamBukuTidakAda(): void
    {
        $service = new LibraryService(new InMemoryBookRepository(), new DendaMahasiswa());
        $service->daftarkanAnggota('ANG-001');

        // Memastikan method membuang BukuTidakDitemukanException
        $this->expectException(BukuTidakDitemukanException::class);
        $service->pinjam('ANG-001', 'ISBN-KOSONG');
    }

    public function testExceptionPinjamAnggotaTidakTerdaftar(): void
    {
        $service = new LibraryService(new InMemoryBookRepository(), new DendaMahasiswa());

        // Memastikan method membuang AnggotaTidakDitemukanException
        $this->expectException(AnggotaTidakDitemukanException::class);
        $service->pinjam('ANG-KOSONG', '978-1');
    }
}
