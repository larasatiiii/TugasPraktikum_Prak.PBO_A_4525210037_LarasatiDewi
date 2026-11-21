<?php
declare(strict_types=1);

namespace Perpustakaan\Tests\Service;

use InvalidArgumentException;
use Perpustakaan\Denda\AturanDendaFactory;
use Perpustakaan\Denda\DendaAlumni;
use Perpustakaan\Denda\DendaBebas;
use Perpustakaan\Denda\DendaDosen;
use Perpustakaan\Denda\DendaMahasiswa;
use Perpustakaan\Model\Buku;
use Perpustakaan\Repository\BookRepository;
use Perpustakaan\Repository\InMemoryBookRepository;
use Perpustakaan\Service\LibraryService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class LibraryServiceTest extends TestCase
{
    public function testTambahDanPinjamDenganInMemoryRepository(): void
    {
        $repo = new InMemoryBookRepository();
        $service = new LibraryService($repo, new DendaMahasiswa());

        $service->tambahBuku('978-1', 'Clean Code', 'Robert C. Martin', 2);
        $service->daftarkanAnggota('A-001');

        $service->pinjam('A-001', '978-1');
        $this->assertEquals(1, $service->sisaEksemplar('978-1'));
    }

    public function testStrategyAturanDendaDanFactory(): void
    {
        $repo = new InMemoryBookRepository();

        $this->assertEquals(3000.0, (new LibraryService($repo, new DendaMahasiswa()))->hitungDenda(3));
        $this->assertEquals(1500.0, (new LibraryService($repo, new DendaDosen()))->hitungDenda(3));
        $this->assertEquals(0.0, (new LibraryService($repo, new DendaBebas()))->hitungDenda(3));
        $this->assertEquals(2250.0, (new LibraryService($repo, new DendaAlumni()))->hitungDenda(3));
        $this->assertEquals(0.0, AturanDendaFactory::untuk('mahasiswa')->hitung(-2));

        $alumni = AturanDendaFactory::untuk('alumni');
        $this->assertInstanceOf(DendaAlumni::class, $alumni);

        $this->expectException(InvalidArgumentException::class);
        AturanDendaFactory::untuk('tamu');
    }

    public function testDenganRepositoryPalsuYangSelaluMelemparException(): void
    {
        $failingRepo = new class implements BookRepository {
            public function simpan(Buku $buku): void
            {
                throw new RuntimeException('Koneksi basis data gagal saat simpan');
            }

            public function cariByIsbn(string $isbn): ?Buku
            {
                throw new RuntimeException('Koneksi basis data gagal saat cariByIsbn');
            }

            public function cariSemua(): array
            {
                throw new RuntimeException('Koneksi basis data gagal saat cariSemua');
            }

            public function kurangiStok(Buku $buku): void
            {
                throw new RuntimeException('Koneksi basis data gagal saat kurangiStok');
            }
        };

        $service = new LibraryService($failingRepo, new DendaMahasiswa());
        $service->daftarkanAnggota('A-001');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Koneksi basis data gagal');
        $service->pinjam('A-001', '978-1');
    }
}
