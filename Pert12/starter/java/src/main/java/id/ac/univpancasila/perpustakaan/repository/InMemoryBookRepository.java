package id.ac.univpancasila.perpustakaan.repository;

import id.ac.univpancasila.perpustakaan.model.Buku;

import java.util.ArrayList;
import java.util.Collections;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Optional;

/**
 * Implementasi in-memory — untuk latihan dan PENGUJIAN.
 */
public class InMemoryBookRepository implements BookRepository {

    private final Map<String, Buku> data = new LinkedHashMap<>();

    // TODO 1: implementasikan seluruh method dari BookRepository
    @Override
    public void simpan(Buku buku) {
        if (buku == null) {
            throw new IllegalArgumentException("Buku tidak boleh null.");
        }
        data.put(buku.getNomorInduk(), buku);
    }

    @Override
    public Optional<Buku> cariByIsbn(String isbn) {
        if (isbn == null) {
            return Optional.empty();
        }
        return Optional.ofNullable(data.get(isbn));
    }

    @Override
    public List<Buku> cariSemua() {
        return Collections.unmodifiableList(new ArrayList<>(data.values()));
    }

    @Override
    public void kurangiStok(Buku buku) {
        if (buku == null) {
            throw new IllegalArgumentException("Buku tidak boleh null.");
        }
        buku.kurangiStok();
    }
}
