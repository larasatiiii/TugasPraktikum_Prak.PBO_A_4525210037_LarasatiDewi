package id.ac.univpancasila.perpustakaan.repository;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import java.util.List;
import java.util.Optional;
import id.ac.univpancasila.perpustakaan.service.LibraryService;
import id.ac.univpancasila.perpustakaan.denda.DendaMahasiswa;
import id.ac.univpancasila.perpustakaan.exception.BukuTidakDitemukanException;
import id.ac.univpancasila.perpustakaan.exception.AnggotaTidakDitemukanException;

import static org.junit.jupiter.api.Assertions.*;

import java.util.Optional;

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

        // TODO 1: pastikan cariById("978-1") berisi nilai,
        //         dan judulnya "PBO".
        //         Petunjuk: assertTrue(...isPresent()) dan assertEquals(...)
        Optional<Buku> hasilCari = repo.cariById("978-1");

        assertTrue(hasilCari.isPresent(), "Buku seharusnya ditemukan");
        assertEquals("PBO", hasilCari.get().judul(), "Judul buku tidak sesuai");
    }

    @Test
    @DisplayName("Mencari id yang tidak ada mengembalikan Optional kosong")
    void cariYangTidakAda() {
        Optional<Buku> hasilCari = repo.cariById("ISBN-KOSONG");

        assertTrue(hasilCari.isEmpty(), "Mencari ID yang tidak ada harus mengembalikan Optional kosong");
    }

    @Test
    @DisplayName("Item yang dihapus tidak dapat ditemukan lagi")
    void hapusItem() {
        // TODO 3
        repo.simpan(new Buku("978-2", "Struktur Data"));
        boolean isTerhapus = repo.hapus("978-2");


        assertTrue(isTerhapus, "Proses hapus harus mengembalikan nilai true");
        assertTrue(repo.cariById("978-2").isEmpty(), "Buku yang sudah dihapus seharusnya tidak ditemukan lagi");
    }

    @Test
    @DisplayName("Repository baru berisi nol item")
    void repositoryKosong() {
        // TODO 4
        assertEquals(0, repo.jumlah(), "Jumlah item pada repository baru harus 0");
        assertTrue(repo.cariSemua().isEmpty(), "List yang dikembalikan harus kosong");
    }

    @Test
    @DisplayName("Koleksi yang dikembalikan cariSemua tidak dapat diubah dari luar")
    void koleksiTidakBocor() {
        repo.simpan(new Buku("978-1", "PBO"));

        // TODO 5: pastikan memanggil .add(...) pada hasil cariSemua()
        //         melemparkan UnsupportedOperationException.
        //         Petunjuk: assertThrows(UnsupportedOperationException.class, () -> ...)
        List<Buku> hasilList = repo.cariSemua();
        
        assertThrows(UnsupportedOperationException.class, () -> {
            hasilList.add(new Buku("978-9", "Buku Hack"));
        }, "Memodifikasi list salinan harus melempar UnsupportedOperationException");
    }

    // TODO 6: tambahkan minimal TIGA pengujian lagi dari daftar skenario
    //         yang Anda susun pada sesi 10, termasuk satu yang memastikan
    //         exception yang benar dilemparkan.
    @Test
    @DisplayName("Menyimpan item dengan kunci yang sama akan menimpa data lama")
    void simpanKunciSamaMenimpaData() {
        repo.simpan(new Buku("978-3", "Buku Versi Lama"));
        repo.simpan(new Buku("978-3", "Buku Versi Revisi")); // ISBN sama

        assertEquals(1, repo.jumlah(), "Jumlah item seharusnya tetap 1");
        assertEquals("Buku Versi Revisi", repo.cariById("978-3").get().judul(), "Data lama seharusnya tertimpa");
    }

    @Test
    @DisplayName("Menghapus item dengan ID yang tidak ada akan mengembalikan false")
    void hapusItemTidakAdaMengembalikanFalse() {
        boolean isTerhapus = repo.hapus("ISBN-TIDAK-ADA");
        
        assertFalse(isTerhapus, "Menghapus ID yang tidak ada harus mengembalikan false");
    }

    @Test
    @DisplayName("Exception: Meminjam dengan ISBN yang tidak terdaftar melempar BukuTidakDitemukanException")
    void exceptionPinjamBukuTidakAda() {
        LibraryService service = new LibraryService(new InMemoryBookRepository(), new DendaMahasiswa());
        service.daftarkanAnggota("ANG-001");
        
        // Memastikan method pinjam() melempar exception saat ISBN salah
        assertThrows(BukuTidakDitemukanException.class, () -> {
            service.pinjam("ANG-001", "ISBN-NGASAL");
        }, "Harus melempar BukuTidakDitemukanException jika ISBN tidak ada");
    }
    
    @Test
    @DisplayName("Exception: Meminjam dengan ID Anggota yang tidak terdaftar melempar AnggotaTidakDitemukanException")
    void exceptionPinjamAnggotaTidakAda() {
        LibraryService service = new LibraryService(new InMemoryBookRepository(), new DendaMahasiswa());
        
        assertThrows(AnggotaTidakDitemukanException.class, () -> {
            service.pinjam("ANG-NGASAL", "978-1");
        }, "Harus melempar AnggotaTidakDitemukanException jika Anggota tidak terdaftar");
    }
}
