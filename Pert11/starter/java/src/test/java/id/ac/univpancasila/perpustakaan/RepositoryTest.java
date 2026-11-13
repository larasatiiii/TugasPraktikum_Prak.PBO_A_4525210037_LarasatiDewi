package id.ac.univpancasila.perpustakaan;

import java.util.List;
import java.util.Optional;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import id.ac.univpancasila.perpustakaan.exception.BatasPinjamTerlampauiException;
import id.ac.univpancasila.perpustakaan.exception.BukuSedangDipinjamException;
import id.ac.univpancasila.perpustakaan.exception.BukuTidakDitemukanException;
import id.ac.univpancasila.perpustakaan.service.LibraryService;

import static org.junit.jupiter.api.Assertions.*;

/**
 * Sesi 11 — unit test pertama Anda.
 *
 * Letakkan berkas ini di src/test/java/id/ac/univpancasila/perpustakaan/
 * Jalankan dengan: mvn test
 *
 * Konvensi Maven: hanya berkas di src/test/java yang bernama *Test
 * yang akan dijalankan sebagai pengujian.
 */
class RepositoryTest {

    /** Objek sederhana untuk diuji. */
    record Buku(String isbn, String judul) {}

    private Repository<Buku> repo;

    @BeforeEach
    void siapkan() {
        // Dijalankan ulang sebelum SETIAP pengujian, sehingga tiap uji
        // berangkat dari keadaan yang bersih dan tidak saling memengaruhi.
        repo = new Repository<>(Buku::isbn);
    }

    @Test
    @DisplayName("Item yang disimpan dapat ditemukan kembali")
    void simpanLaluTemukan() {
        repo.simpan(new Buku("978-1", "PBO"));

        Optional<Buku> hasil = repo.cariById("978-1");

        assertTrue(hasil.isPresent());
        assertEquals("PBO", hasil.get().judul());
    }

    @Test
    @DisplayName("Mencari id yang tidak ada mengembalikan Optional kosong")
    void cariYangTidakAda() {
        assertTrue(repo.cariById("999").isEmpty());
    }

    @Test
    @DisplayName("Item yang dihapus tidak dapat ditemukan lagi")
    void hapusItem() {
        repo.simpan(new Buku("978-1", "PBO"));

        assertTrue(repo.hapus("978-1"));
        assertTrue(repo.cariById("978-1").isEmpty());
        assertEquals(0, repo.jumlah());
    }

    @Test
    @DisplayName("Repository baru berisi nol item")
    void repositoryKosong() {
        assertEquals(0, repo.jumlah());
        assertTrue(repo.cariSemua().isEmpty());
    }

    @Test
    @DisplayName("Koleksi yang dikembalikan cariSemua tidak dapat diubah dari luar")
    void koleksiTidakBocor() {
        repo.simpan(new Buku("978-1", "PBO"));

        List<Buku> salinan = repo.cariSemua();

        assertThrows(UnsupportedOperationException.class,
                () -> salinan.add(new Buku("978-2", "Basis Data")));
    }

    // --- Pengujian tambahan dari skenario sesi 10 ---

    @Test
    @DisplayName("Menyimpan dengan kunci yang sama menimpa item lama")
    void simpanKunciSamaMenimpa() {
        repo.simpan(new Buku("978-1", "PBO"));
        repo.simpan(new Buku("978-1", "PBO Edisi 2"));

        assertEquals(1, repo.jumlah());
        assertEquals("PBO Edisi 2", repo.cariById("978-1").orElseThrow().judul());
    }

    @Test
    @DisplayName("Menghapus id yang tidak ada mengembalikan false")
    void hapusYangTidakAda() {
        assertFalse(repo.hapus("999"));
    }

    @Test
    @DisplayName("Peminjaman yang sah mengurangi stok satu eksemplar")
    void pinjamBerhasilMengurangiStok() {
        LibraryService layanan = new LibraryService();
        layanan.tambahBuku("978-1", "PBO", 2);
        layanan.daftarkanAnggota("A1");

        assertTrue(layanan.pinjam("A1", "978-1"));
        assertEquals(1, layanan.sisaEksemplar("978-1"));
    }

    @Test
    @DisplayName("pinjam() melempar BukuTidakDitemukanException untuk ISBN yang tidak terdaftar")
    void pinjamIsbnTidakTerdaftar() {
        LibraryService layanan = new LibraryService();
        layanan.daftarkanAnggota("A1");

        BukuTidakDitemukanException e = assertThrows(
                BukuTidakDitemukanException.class,
                () -> layanan.pinjam("A1", "000"));

        assertEquals("000", e.getIsbn());
    }

    @Test
    @DisplayName("pinjam() melempar BatasPinjamTerlampauiException setelah tiga pinjaman")
    void pinjamMelewatiBatas() {
        LibraryService layanan = new LibraryService();
        layanan.tambahBuku("978-1", "PBO", 5);
        layanan.daftarkanAnggota("A1");
        for (int i = 0; i < LibraryService.BATAS_PINJAM; i++) {
            layanan.pinjam("A1", "978-1");
        }

        BatasPinjamTerlampauiException e = assertThrows(
                BatasPinjamTerlampauiException.class,
                () -> layanan.pinjam("A1", "978-1"));

        assertEquals(LibraryService.BATAS_PINJAM, e.getBatas());
    }

    @Test
    @DisplayName("pinjam() melempar BukuSedangDipinjamException bila stok habis")
    void pinjamBukuHabis() {
        LibraryService layanan = new LibraryService();
        layanan.tambahBuku("978-1", "PBO", 1);
        layanan.daftarkanAnggota("A1");
        layanan.daftarkanAnggota("A2");
        layanan.pinjam("A1", "978-1");

        BukuSedangDipinjamException e = assertThrows(
                BukuSedangDipinjamException.class,
                () -> layanan.pinjam("A2", "978-1"));

        assertEquals("PBO", e.getJudul());
    }
}
