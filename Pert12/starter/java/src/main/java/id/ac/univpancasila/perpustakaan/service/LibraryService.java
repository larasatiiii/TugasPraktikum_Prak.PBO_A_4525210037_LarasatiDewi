package id.ac.univpancasila.perpustakaan.service;

import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.stream.Collectors;

import id.ac.univpancasila.perpustakaan.denda.AturanDenda;
import id.ac.univpancasila.perpustakaan.exception.AnggotaTidakDitemukanException;
import id.ac.univpancasila.perpustakaan.exception.BatasPinjamTerlampauiException;
import id.ac.univpancasila.perpustakaan.exception.BukuSedangDipinjamException;
import id.ac.univpancasila.perpustakaan.exception.BukuTidakDitemukanException;
import id.ac.univpancasila.perpustakaan.model.Buku;
import id.ac.univpancasila.perpustakaan.repository.BookRepository;

public class LibraryService {

    public static final int BATAS_PINJAM = 3;

    private final BookRepository repo;
    private final AturanDenda aturanDenda;
    private final Map<String, Integer> pinjaman = new HashMap<>();

    // Langkah 2 & 4: terima BookRepository dan AturanDenda lewat constructor (tanpa new di dalam service)
    public LibraryService(BookRepository repo, AturanDenda aturanDenda) {
        if (repo == null) {
            throw new IllegalArgumentException("BookRepository tidak boleh null.");
        }
        if (aturanDenda == null) {
            throw new IllegalArgumentException("AturanDenda tidak boleh null.");
        }
        this.repo = repo;
        this.aturanDenda = aturanDenda;
    }

    public void tambahBuku(String isbn, String judulBuku, String penulis, int eksemplar) {
        Optional<Buku> bukuOpt = repo.cariByIsbn(isbn);

        if (bukuOpt.isPresent()) {
            Buku bukuLama = bukuOpt.get();
            for (int i = 0; i < eksemplar; i++) {
                bukuLama.tambahStok();
            }
        } else {
            Buku bukuBaru = new Buku(isbn, judulBuku, penulis, eksemplar);
            repo.simpan(bukuBaru);
        }
    }

    public void daftarkanAnggota(String idAnggota) {
        pinjaman.putIfAbsent(idAnggota, 0);
    }

    public boolean pinjam(String idAnggota, String isbn) {
        if (!pinjaman.containsKey(idAnggota)) {
            throw new AnggotaTidakDitemukanException(idAnggota);
        }
        Buku bukuDiPinjam = repo.cariByIsbn(isbn)
                .orElseThrow(() -> new BukuTidakDitemukanException(isbn));

        if (!bukuDiPinjam.tersedia()) {
            throw new BukuSedangDipinjamException(isbn, bukuDiPinjam.getJudul());
        }

        if (pinjaman.get(idAnggota) >= BATAS_PINJAM) {
            throw new BatasPinjamTerlampauiException(idAnggota, BATAS_PINJAM);
        }

        repo.kurangiStok(bukuDiPinjam);
        pinjaman.merge(idAnggota, 1, Integer::sum);
        return true;
    }

    public double hitungDenda(int hariTerlambat) {
        return aturanDenda.hitung(hariTerlambat);
    }

    public String getNamaAturanDenda() {
        return aturanDenda.nama();
    }

    public int sisaEksemplar(String isbn) {
        Optional<Buku> bukuOpt = repo.cariByIsbn(isbn);
        return bukuOpt.map(Buku::getJumlahEksemplar).orElse(0);
    }

    public String judulDari(String isbn) {
        Optional<Buku> bukuOpt = repo.cariByIsbn(isbn);
        return bukuOpt.map(Buku::getJudul).orElse(null);
    }

    public Map<String, String> getSemuaJudul() {
        Map<String, String> semuaJudul = new HashMap<>();
        for (Buku buku : repo.cariSemua()) {
            semuaJudul.put(buku.getNomorInduk(), buku.getJudul());
        }
        return semuaJudul;
    }

    public List<Buku> getBukuTersedia() {
        return repo.cariSemua().stream()
                .filter(Buku::tersedia)
                .collect(Collectors.toList());
    }

    public String getSemuaJudulDigabung() {
        return repo.cariSemua().stream()
                .map(Buku::getJudul)
                .collect(Collectors.joining(", "));
    }

    public Map<String, Long> getJumlahBukuPerPenulis() {
        return repo.cariSemua().stream()
                .collect(Collectors.groupingBy(
                        Buku::getPenulis,
                        Collectors.counting()
                ));
    }
}
