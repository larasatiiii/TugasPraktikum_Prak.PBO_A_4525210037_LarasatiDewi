<?php
declare(strict_types=1);

namespace Perpustakaan\Model;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Entitas Transaksi Peminjaman.
 * Menggunakan nesbot/carbon untuk kalkulasi tanggal jatuh tempo.
 */
final class Peminjaman
{
    private readonly Carbon $tanggalJatuhTempo;
    private ?Carbon $tanggalKembali = null;

    public function __construct(
        private readonly string $idPeminjaman,
        private readonly Anggota $anggota,
        private readonly Buku $buku,
        private readonly Carbon $tanggalPinjam,
        int $lamaHari
    ) {
        if (trim($idPeminjaman) === '') {
            throw new InvalidArgumentException("ID peminjaman tidak boleh kosong.");
        }
        // Menggunakan method addDays dari Carbon
        $this->tanggalJatuhTempo = (clone $this->tanggalPinjam)->addDays($lamaHari);
    }

    public function getIdPeminjaman(): string
    {
        return $this->idPeminjaman;
    }

    public function getAnggota(): Anggota
    {
        return $this->anggota;
    }

    public function getBuku(): Buku
    {
        return $this->buku;
    }

    public function getTanggalPinjam(): Carbon
    {
        return $this->tanggalPinjam;
    }

    public function getTanggalJatuhTempo(): Carbon
    {
        return $this->tanggalJatuhTempo;
    }

    public function getTanggalKembali(): ?Carbon
    {
        return $this->tanggalKembali;
    }

    public function setTanggalKembali(Carbon $tanggalKembali): void
    {
        $this->tanggalKembali = $tanggalKembali;
    }

    public function sudahKembali(): bool
    {
        return $this->tanggalKembali !== null;
    }

    public function __toString(): string
    {
        return sprintf(
            "%s: %s meminjam '%s' (Jatuh tempo: %s)",
            $this->idPeminjaman,
            $this->anggota->getNama(),
            $this->buku->getJudul(),
            $this->tanggalJatuhTempo->isoFormat('D MMMM Y')
        );
    }
}
