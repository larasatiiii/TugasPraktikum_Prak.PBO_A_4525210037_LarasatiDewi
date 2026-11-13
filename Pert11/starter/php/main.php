<?php
declare(strict_types=1);

/**
 * Titik masuk utama aplikasi Sistem Perpustakaan (PHP / Composer).
 * Seluruh require_once manual DIHAPUS, digantikan oleh autoload PSR-4 Composer.
 */
require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;
use Perpustakaan\Model\Anggota;
use Perpustakaan\Model\Buku;
use Perpustakaan\Service\PerpustakaanService;
use Perpustakaan\Util\Format;

echo "=======================================================================\n";
echo "    SISTEM PERPUSTAKAAN UNIVERSITAS PANCASILA (COMPOSER MODUL 9)      \n";
echo "=======================================================================\n\n";

$service = new PerpustakaanService();

// 1. Tambah Koleksi Buku beserta Bab (Komposisi)
$b1 = new Buku("B-001", "Pemrograman Berorientasi Objek", "Dr. A. W. Pribadi", 4);
$b1->tambahBab(1, "Konsep Objek dan Kelas");
$b1->tambahBab(2, "Enkapsulasi & Immutability");
$b1->tambahBab(3, "Package & Build Tools");

$b2 = new Buku("B-002", "Struktur Data & Algoritma", "Ir. Budi Raharjo", 2);
$b2->tambahBab(1, "Array & Linked List");
$b2->tambahBab(2, "Tree & Graph");

$b3 = new Buku("B-003", "Rekayasa Perangkat Lunak Modern", "Prof. R. Pressman", 1);
$b3->tambahBab(1, "Agile & Scrum Process");
$b3->tambahBab(2, "Clean Architecture & Design Pattern");

$service->tambahKoleksi($b1);
$service->tambahKoleksi($b2);
$service->tambahKoleksi($b3);

// 2. Daftarkan Anggota
$a1 = new Anggota("A-2024001", "Siti Aisyah", "Mahasiswa");
$a2 = new Anggota("A-2024002", "Bambang Pamungkas", "Mahasiswa");
$a3 = new Anggota("D-1985010", "Dr. Hendra Gunawan", "Dosen");

$service->daftarkanAnggota($a1);
$service->daftarkanAnggota($a2);
$service->daftarkanAnggota($a3);

// 3. Simulasi Transaksi Peminjaman Menggunakan Carbon
$tglPinjam = Carbon::create(2026, 10, 20);
$p1 = $service->pinjam("A-2024001", "B-001", $tglPinjam, 14);
$p2 = $service->pinjam("D-1985010", "B-003", $tglPinjam, 14);

// Simulasi pengembalian terlambat 5 hari setelah jatuh tempo
$tglKembaliP1 = (clone $p1->getTanggalJatuhTempo())->addDays(5);
$dendaP1 = $service->kembalikan($p1->getIdPeminjaman(), $tglKembaliP1);

// 4. Tampilkan Daftar Buku
echo "[1] DAFTAR KOLEKSI BUKU:\n";
printf("%-8s | %-32s | %-20s | %-5s | %-10s\n", "Kode", "Judul Buku", "Penulis", "Stok", "Jumlah Bab");
echo str_repeat("-", 85) . "\n";
foreach ($service->getDaftarKoleksi() as $buku) {
    printf(
        "%-8s | %-32s | %-20s | %-5d | %-10s\n",
        $buku->getNomorInduk(),
        $buku->getJudul(),
        $buku->getPenulis(),
        $buku->getJumlahEksemplar(),
        count($buku->getBab()) . " bab"
    );
}
echo "\n";

// 5. Tampilkan Daftar Transaksi Peminjaman
echo "[2] DAFTAR TRANSAKSI PEMINJAMAN:\n";
printf("%-8s | %-16s | %-10s | %-28s | %-16s | %-16s | %-20s\n",
    "ID", "Peminjam", "Peran", "Buku", "Tgl Pinjam", "Jatuh Tempo", "Status");
echo str_repeat("-", 125) . "\n";
foreach ($service->getDaftarPeminjaman() as $p) {
    $status = $p->sudahKembali()
        ? "Kembali (" . Format::formatTanggal($p->getTanggalKembali()) . ")"
        : "MASIH DIPINJAM";

    printf(
        "%-8s | %-16s | %-10s | %-28s | %-16s | %-16s | %-20s\n",
        $p->getIdPeminjaman(),
        $p->getAnggota()->getNama(),
        $p->getAnggota()->getJenisKeanggotaan(),
        $p->getBuku()->getJudul(),
        Format::formatTanggal($p->getTanggalPinjam()),
        Format::formatTanggal($p->getTanggalJatuhTempo()),
        $status
    );
}
echo "\n";

// 6. Rincian Perhitungan Denda dengan Carbon
echo "[3] SIMULASI PERHITUNGAN DENDA KETERLAMBATAN:\n";
echo "  ID Transaksi        : " . $p1->getIdPeminjaman() . "\n";
echo "  Peminjam            : " . $p1->getAnggota()->getNama() . " (" . $p1->getAnggota()->getJenisKeanggotaan() . ")\n";
echo "  Tgl Jatuh Tempo     : " . Format::formatTanggal($p1->getTanggalJatuhTempo()) . "\n";
echo "  Tgl Dikembalikan    : " . Format::formatTanggal($p1->getTanggalKembali()) . "\n";
echo "  Keterlambatan       : " . $p1->getTanggalJatuhTempo()->diffInDays($p1->getTanggalKembali()) . " hari\n";
echo "  Total Denda         : " . Format::formatRupiah($dendaP1) . "\n";
echo "\nProgram PHP selesai dieksekusi dengan sukses melalui autoload PSR-4.\n";
