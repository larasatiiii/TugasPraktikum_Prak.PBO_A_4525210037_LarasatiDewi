package id.ac.univpancasila.perpustakaan.repository;

import id.ac.univpancasila.perpustakaan.model.Buku;

import java.util.List;
import java.util.Optional;

/**
 * Sesi 12 — REPOSITORY PATTERN.
 *
 * Interface ini menyembunyikan CARA data disimpan dari logika bisnis
 * yang memakainya. LibraryService cukup tahu kontraknya.
 */
public interface BookRepository {

    // TODO 1: rancang tanda tangan method yang dibutuhkan LibraryService
    void simpan(Buku buku);

    Optional<Buku> cariByIsbn(String isbn);

    List<Buku> cariSemua();

    void kurangiStok(Buku buku);
}
