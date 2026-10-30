<?php
declare(strict_types=1);

namespace Perpustakaan\Service;

use Carbon\Carbon;
use InvalidArgumentException;
use RuntimeException;
use Perpustakaan\Model\Anggota;
use Perpustakaan\Model\Buku;
use Perpustakaan\Model\Peminjaman;

/**
 * Lapisan Layanan Sistem Perpustakaan.
 */
final class PerpustakaanService
{
    /** @var Buku[] */
    private array $daftarKoleksi = [];

    /** @var Anggota[] */
    private array $daftarAnggota = [];

    /** @var Peminjaman[] */
    private array $daftarPeminjaman = [];

    private int $counterPeminjaman = 1;

    public function tambahKoleksi(Buku $buku): void
    {
        if ($this->cariKoleksi($buku->getNomorInduk()) === null) {
            $this->daftarKoleksi[] = $buku;
        }
    }

    public function cariKoleksi(string $nomorInduk): ?Buku
    {
        foreach ($this->daftarKoleksi as $b) {
            if (strcasecmp($b->getNomorInduk(), $nomorInduk) === 0) {
                return $b;
            }
        }
        return null;
    }

    /**
     * @return Buku[]
     */
    public function getDaftarKoleksi(): array
    {
        return $this->daftarKoleksi;
    }

    public function daftarkanAnggota(Anggota $anggota): void
    {
        if ($this->cariAnggota($anggota->getNomorAnggota()) === null) {
            $this->daftarAnggota[] = $anggota;
        }
    }

    public function cariAnggota(string $nomorAnggota): ?Anggota
    {
        foreach ($this->daftarAnggota as $a) {
            if (strcasecmp($a->getNomorAnggota(), $nomorAnggota) === 0) {
                return $a;
            }
        }
        return null;
    }

    /**
     * @return Anggota[]
     */
    public function getDaftarAnggota(): array
    {
        return $this->daftarAnggota;
    }

    public function pinjam(string $nomorAnggota, string $nomorInduk, Carbon $tanggalPinjam, int $lamaHari = 14): Peminjaman
    {
        $anggota = $this->cariAnggota($nomorAnggota);
        if ($anggota === null) {
            throw new InvalidArgumentException("Anggota tidak ditemukan: " . $nomorAnggota);
        }

        $buku = $this->cariKoleksi($nomorInduk);
        if ($buku === null) {
            throw new InvalidArgumentException("Buku tidak ditemukan: " . $nomorInduk);
        }

        if (!$buku->tersedia()) {
            throw new RuntimeException("Stok buku sedang habis: " . $buku->getJudul());
        }

        // Hitung peminjaman aktif
        $pinjamanAktif = 0;
        foreach ($this->daftarPeminjaman as $p) {
            if ($p->getAnggota()->getNomorAnggota() === $nomorAnggota && !$p->sudahKembali()) {
                $pinjamanAktif++;
            }
        }

        if ($pinjamanAktif >= 3) {
            throw new RuntimeException("Anggota {$anggota->getNama()} sudah mencapai batas maksimal 3 peminjaman aktif.");
        }

        $buku->kurangiStok();
        $idPeminjaman = sprintf("PJ-%03d", $this->counterPeminjaman++);
        $peminjaman = new Peminjaman($idPeminjaman, $anggota, $buku, $tanggalPinjam, $lamaHari);
        $this->daftarPeminjaman[] = $peminjaman;

        return $peminjaman;
    }

    public function kembalikan(string $idPeminjaman, Carbon $tanggalKembali): int
    {
        $target = null;
        foreach ($this->daftarPeminjaman as $p) {
            if (strcasecmp($p->getIdPeminjaman(), $idPeminjaman) === 0) {
                $target = $p;
                break;
            }
        }

        if ($target === null) {
            throw new InvalidArgumentException("Data peminjaman tidak ditemukan: " . $idPeminjaman);
        }

        if ($target->sudahKembali()) {
            throw new RuntimeException("Peminjaman ini sudah dikembalikan.");
        }

        $target->setTanggalKembali($tanggalKembali);
        $target->getBuku()->tambahStok();

        return $this->hitungDenda($target);
    }

    public function hitungDenda(Peminjaman $peminjaman): int
    {
        if ($peminjaman->getTanggalKembali() === null) {
            return 0;
        }

        // Menghitung selisih hari keterlambatan menggunakan Carbon
        $jatuhTempo = $peminjaman->getTanggalJatuhTempo()->startOfDay();
        $kembali = $peminjaman->getTanggalKembali()->startOfDay();

        if ($kembali->lte($jatuhTempo)) {
            return 0;
        }

        $hariTerlambat = (int) $jatuhTempo->diffInDays($kembali);

        $jenis = strtolower($peminjaman->getAnggota()->getJenisKeanggotaan());
        if (str_contains($jenis, 'mahasiswa')) {
            $tarif = 1000;
        } elseif (str_contains($jenis, 'dosen')) {
            $tarif = 500;
        } else {
            $tarif = 0; // Anggota kehormatan
        }

        return $hariTerlambat * $tarif;
    }

    /**
     * @return Peminjaman[]
     */
    public function getDaftarPeminjaman(): array
    {
        return $this->daftarPeminjaman;
    }
}
