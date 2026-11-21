<?php
require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;
use Perpustakaan\Denda\AturanDendaFactory;
use Perpustakaan\Exception\PerpustakaanException;
use Perpustakaan\Repository\InMemoryBookRepository;
use Perpustakaan\Service\LibraryService;
use Perpustakaan\Service\PerpustakaanService;

/*
 * DEMO: titik masuk program (manual Dependency Injection)
 */
$repo = new InMemoryBookRepository();
$aturanDenda = AturanDendaFactory::untuk('mahasiswa');
$s = new LibraryService($repo, $aturanDenda);
$s->tambahBuku('111', 'Laskar Pelangi','Andrea Hirarta', 1);
$s->tambahBuku('222', 'Bumi Manusia','Pramoedya',  5);
$s->daftarkanAnggota('A-001');
$s->daftarkanAnggota('A-002');

$percobaan = [
    ['A-001', '111'],   // berhasil
    ['A-002', '111'],   // buku habi
    ['A-002', '999'],   // buku tidak ada
    ['X-999', '222'],   // anggota tidak terdaftar
    ['A-001', '222'],   // berhasil
    ['A-001', '222'],   // berhasil
    ['A-001', '222'],   // batas pinjam tercapai
];

foreach ($percobaan as [$id, $isbn]) {
    echo "Anggota $id meminjam ISBN '$isbn' ... " ,PHP_EOL;
    try {
        $s->pinjam($id, $isbn);
        echo "[BERHASIL]\n";
        
        // uncheked
    } catch (PerpustakaanException $e) {
        echo "[GAGAL] " . $e->getMessage() . "\n";
    }
}

$perpusService = new PerpustakaanService();

$perpusService->tampilkanLaporanFungsional($s);

createReport($s);

function createReport(LibraryService $s){
    echo PHP_EOL . "--- Ekspor Laporan Buku ---" . PHP_EOL;
    $namaFile = 'laporan_buku.txt';
    
    // Membuka file dalam mode write ('w')
    $fileHandle = fopen(__DIR__ . '/' . $namaFile, 'w');

    if (!$fileHandle) {
        echo "[ERROR] Gagal membuka file untuk menulis.\n";
        exit(1);
    }

    try {
        fwrite($fileHandle, "==========================================================================\n");
        fwrite($fileHandle, "                    LAPORAN DAFTAR BUKU PERPUSTAKAAN                      \n");
        fwrite($fileHandle, "==========================================================================\n");
        fwrite($fileHandle, sprintf("%-12s | %-35s | %-8s | %-15s\n", "ISBN", "JUDUL BUKU", "STOK", "STATUS"));
        fwrite($fileHandle, "--------------------------------------------------------------------------\n");

        $daftarJudul = $s->getSemuaJudul();

        foreach ($daftarJudul as $isbn => $judulBuku) {
            $stokSisa = $s->sisaEksemplar($isbn);
            $status = ($stokSisa > 0) ? "Tersedia" : "Tidak Tersedia";
            fwrite($fileHandle, sprintf("%-12s | %-35s | %-8d | %-15s\n", $isbn, $judulBuku, $stokSisa, $status));
        }

        fwrite($fileHandle, "==========================================================================\n");

        // throw new \Exception("Simulasi error tak terduga di tengah penulisan file!");

        echo ">> BERHASIL: Laporan buku beserta statusnya disimpan ke berkas '{$namaFile}'.\n";

    } catch (\Exception $e) {
        echo "[ERROR SISTEM] Gagal menyelesaikan laporan: " . $e->getMessage() . "\n";
    } finally {
        if (is_resource($fileHandle)) {
            fclose($fileHandle);
            echo "[INFO] File '{$namaFile}' telah ditutup dengan aman melalui blok finally.\n";
        }
    }
}
