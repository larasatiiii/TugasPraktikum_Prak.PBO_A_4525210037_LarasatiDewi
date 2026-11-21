<?php
namespace Perpustakaan\Service;

use Perpustakaan\Model\Buku;

class PerpustakaanService 
{
    /**
     * @var Buku[]
     */
    private array $buku = [];

    public function tambahKoleksi(Buku $buku): void 
    {
        $this->buku[] = $buku;
    }

    public function cariKoleksi(string $nomorInduk): ?Buku 
    {
        foreach ($this->buku as $item) {
            if ($item->getNomorInduk() === $nomorInduk) {
                return $item;
            }
        }
        return null;
    }

    public function pinjam(string $nomorAnggota, string $nomorInduk): void 
    {
        echo "Anggota {$nomorAnggota} meminjam buku {$nomorInduk}\n";
    }

    public function kembalikan(string $nomorAnggota, string $nomorInduk): void 
    {
        echo "Anggota: {$nomorAnggota} mengembalikan buku {$nomorInduk}\n";
    }

    public static function tampilkanLaporanFungsional(LibraryService $libService): void
    {
        echo "\n=== LAPORAN FUNGSIONAL (HASIL SESI 11) ===\n";

        echo "\n1. Daftar Buku Tersedia:\n";
        $bukuTersedia = $libService->getBukuTersedia();
        if (empty($bukuTersedia)) {
            echo "   (Tidak ada buku yang tersedia saat ini)\n";
        } else {
            foreach ($bukuTersedia as $buku) {
                // Mencetak judul, penulis, dan sisa stok
                echo "   - " . $buku->getJudul() . " oleh " . $buku->getPenulis() . 
                     " (Sisa: " . $buku->getJumlahEksampelar() . ")\n";
            }
        }

        // 2. Menampilkan gabungan semua judul buku
        echo "\n2. Gabungan Semua Judul Buku:\n";
        $gabunganJudul = $libService->getSemuaJudulDigabung();
        if ($gabunganJudul === "") {
            echo "   (Belum ada judul buku di perpustakaan)\n";
        } else {
            echo "   " . $gabunganJudul . "\n";
        }

        echo "\n3. Jumlah Buku per Penulis:\n";
        $jumlahPerPenulis = $libService->getJumlahBukuPerPenulis();
        if (empty($jumlahPerPenulis)) {
            echo "   (Data penulis kosong)\n";
        } else {
            foreach ($jumlahPerPenulis as $penulis => $jumlah) {
                echo "   - " . $penulis . " : " . $jumlah . " judul buku\n";
            }
        }
        
        echo "==========================================\n";
    }
}