package id.ac.univpancasila.perpustakaan.model;

import java.time.LocalDate;

/**
 * Entitas Transaksi Peminjaman buku oleh Anggota.
 */
public class Peminjaman {

    private final String idPeminjaman;
    private final Anggota anggota;
    private final Buku buku;
    private final LocalDate tanggalPinjam;
    private final LocalDate tanggalJatuhTempo;
    private LocalDate tanggalKembali;

    public Peminjaman(String idPeminjaman, Anggota anggota, Buku buku, LocalDate tanggalPinjam, int lamaHari) {
        if (idPeminjaman == null || idPeminjaman.isBlank()) {
            throw new IllegalArgumentException("ID peminjaman tidak boleh kosong.");
        }
        if (anggota == null) {
            throw new IllegalArgumentException("Anggota tidak boleh null.");
        }
        if (buku == null) {
            throw new IllegalArgumentException("Buku tidak boleh null.");
        }
        this.idPeminjaman = idPeminjaman;
        this.anggota = anggota;
        this.buku = buku;
        this.tanggalPinjam = tanggalPinjam;
        this.tanggalJatuhTempo = tanggalPinjam.plusDays(lamaHari);
        this.tanggalKembali = null;
    }

    public String getIdPeminjaman() {
        return idPeminjaman;
    }

    public Anggota getAnggota() {
        return anggota;
    }

    public Buku getBuku() {
        return buku;
    }

    public LocalDate getTanggalPinjam() {
        return tanggalPinjam;
    }

    public LocalDate getTanggalJatuhTempo() {
        return tanggalJatuhTempo;
    }

    public LocalDate getTanggalKembali() {
        return tanggalKembali;
    }

    public boolean sudahKembali() {
        return tanggalKembali != null;
    }

    public void setTanggalKembali(LocalDate tanggalKembali) {
        this.tanggalKembali = tanggalKembali;
    }

    @Override
    public String toString() {
        return String.format("%s: %s meminjam '%s' (Jatuh tempo: %s)",
                idPeminjaman, anggota.getNama(), buku.getJudul(), tanggalJatuhTempo);
    }
}
