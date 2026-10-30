package id.ac.univpancasila.perpustakaan.service;

import id.ac.univpancasila.perpustakaan.model.Anggota;
import id.ac.univpancasila.perpustakaan.model.Buku;
import id.ac.univpancasila.perpustakaan.model.Peminjaman;

import java.time.LocalDate;
import java.time.temporal.ChronoUnit;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

/**
 * Lapisan layanan sistem perpustakaan.
 */
public class PerpustakaanService {

    private final List<Buku> daftarKoleksi = new ArrayList<>();
    private final List<Anggota> daftarAnggota = new ArrayList<>();
    private final List<Peminjaman> daftarPeminjaman = new ArrayList<>();
    private int counterPeminjaman = 1;

    public void tambahKoleksi(Buku buku) {
        if (buku != null && cariKoleksi(buku.getNomorInduk()) == null) {
            daftarKoleksi.add(buku);
        }
    }

    public Buku cariKoleksi(String nomorInduk) {
        for (Buku b : daftarKoleksi) {
            if (b.getNomorInduk().equalsIgnoreCase(nomorInduk)) {
                return b;
            }
        }
        return null;
    }

    public List<Buku> getDaftarKoleksi() {
        return Collections.unmodifiableList(daftarKoleksi);
    }

    public void daftarkanAnggota(Anggota anggota) {
        if (anggota != null && cariAnggota(anggota.getNomorAnggota()) == null) {
            daftarAnggota.add(anggota);
        }
    }

    public Anggota cariAnggota(String nomorAnggota) {
        for (Anggota a : daftarAnggota) {
            if (a.getNomorAnggota().equalsIgnoreCase(nomorAnggota)) {
                return a;
            }
        }
        return null;
    }

    public List<Anggota> getDaftarAnggota() {
        return Collections.unmodifiableList(daftarAnggota);
    }

    public Peminjaman pinjam(String nomorAnggota, String nomorInduk, LocalDate tanggalPinjam, int lamaHari) {
        Anggota anggota = cariAnggota(nomorAnggota);
        if (anggota == null) {
            throw new IllegalArgumentException("Anggota tidak ditemukan: " + nomorAnggota);
        }

        Buku buku = cariKoleksi(nomorInduk);
        if (buku == null) {
            throw new IllegalArgumentException("Buku tidak ditemukan: " + nomorInduk);
        }

        if (!buku.tersedia()) {
            throw new IllegalStateException("Stok buku sedang habis: " + buku.getJudul());
        }

        // Batas maksimal 3 pinjaman aktif
        long pinjamanAktif = daftarPeminjaman.stream()
                .filter(p -> p.getAnggota().getNomorAnggota().equals(nomorAnggota) && !p.sudahKembali())
                .count();
        if (pinjamanAktif >= 3) {
            throw new IllegalStateException("Anggota " + anggota.getNama() + " sudah mencapai batas maksimal 3 peminjaman.");
        }

        buku.kurangiStok();
        String idPeminjaman = String.format("PJ-%03d", counterPeminjaman++);
        Peminjaman peminjaman = new Peminjaman(idPeminjaman, anggota, buku, tanggalPinjam, lamaHari);
        daftarPeminjaman.add(peminjaman);
        return peminjaman;
    }

    public long kembalikan(String idPeminjaman, LocalDate tanggalKembali) {
        Peminjaman peminjaman = daftarPeminjaman.stream()
                .filter(p -> p.getIdPeminjaman().equalsIgnoreCase(idPeminjaman))
                .findFirst()
                .orElseThrow(() -> new IllegalArgumentException("Data peminjaman tidak ditemukan: " + idPeminjaman));

        if (peminjaman.sudahKembali()) {
            throw new IllegalStateException("Peminjaman ini sudah dikembalikan sebelumnya.");
        }

        peminjaman.setTanggalKembali(tanggalKembali);
        peminjaman.getBuku().tambahStok();

        return hitungDenda(peminjaman);
    }

    public long hitungDenda(Peminjaman peminjaman) {
        if (peminjaman.getTanggalKembali() == null) {
            return 0;
        }

        long terlambatHari = ChronoUnit.DAYS.between(peminjaman.getTanggalJatuhTempo(), peminjaman.getTanggalKembali());
        if (terlambatHari <= 0) {
            return 0;
        }

        String jenis = peminjaman.getAnggota().getJenisKeanggotaan().toLowerCase();
        long tarifPerHari;
        if (jenis.contains("mahasiswa")) {
            tarifPerHari = 1000;
        } else if (jenis.contains("dosen")) {
            tarifPerHari = 500;
        } else {
            tarifPerHari = 0; // Anggota kehormatan bebas denda
        }

        return terlambatHari * tarifPerHari;
    }

    public List<Peminjaman> getDaftarPeminjaman() {
        return Collections.unmodifiableList(daftarPeminjaman);
    }
}
