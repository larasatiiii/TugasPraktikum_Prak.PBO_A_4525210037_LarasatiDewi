package id.ac.univpancasila.perpustakaan.service;

import id.ac.univpancasila.perpustakaan.denda.AturanDenda;
import id.ac.univpancasila.perpustakaan.denda.AturanDendaFactory;
import id.ac.univpancasila.perpustakaan.denda.DendaAlumni;
import id.ac.univpancasila.perpustakaan.denda.DendaBebas;
import id.ac.univpancasila.perpustakaan.denda.DendaDosen;
import id.ac.univpancasila.perpustakaan.denda.DendaMahasiswa;
import id.ac.univpancasila.perpustakaan.model.Buku;
import id.ac.univpancasila.perpustakaan.repository.BookRepository;
import id.ac.univpancasila.perpustakaan.repository.InMemoryBookRepository;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

import java.util.List;
import java.util.Optional;

import static org.junit.jupiter.api.Assertions.*;

class LibraryServiceTest {

    /**
     * Repository palsu yang selalu melempar exception untuk membuktikan Dependency Inversion.
     */
    private static class FailingBookRepository implements BookRepository {
        @Override
        public void simpan(Buku buku) {
            throw new RuntimeException("Koneksi basis data gagal saat simpan");
        }

        @Override
        public Optional<Buku> cariByIsbn(String isbn) {
            throw new RuntimeException("Koneksi basis data gagal saat cariByIsbn");
        }

        @Override
        public List<Buku> cariSemua() {
            throw new RuntimeException("Koneksi basis data gagal saat cariSemua");
        }

        @Override
        public void kurangiStok(Buku buku) {
            throw new RuntimeException("Koneksi basis data gagal saat kurangiStok");
        }
    }

    @Test
    @DisplayName("LibraryService bekerja dengan InMemoryBookRepository melalui DI")
    void tambahDanPinjamDenganInMemoryRepository() {
        BookRepository repo = new InMemoryBookRepository();
        LibraryService service = new LibraryService(repo, new DendaMahasiswa());

        service.tambahBuku("978-1", "Clean Code", "Robert C. Martin", 2);
        service.daftarkanAnggota("A-001");

        assertTrue(service.pinjam("A-001", "978-1"));
        assertEquals(1, service.sisaEksemplar("978-1"));
    }

    @Test
    @DisplayName("Strategy AturanDenda dan Factory menghitung denda sesuai jenis anggota")
    void hitungDendaSesuaiStrategy() {
        assertEquals(3000.0, new LibraryService(new InMemoryBookRepository(), new DendaMahasiswa()).hitungDenda(3));
        assertEquals(1500.0, new LibraryService(new InMemoryBookRepository(), new DendaDosen()).hitungDenda(3));
        assertEquals(0.0, new LibraryService(new InMemoryBookRepository(), new DendaBebas()).hitungDenda(3));
        assertEquals(2250.0, new LibraryService(new InMemoryBookRepository(), new DendaAlumni()).hitungDenda(3));
        assertEquals(0.0, AturanDendaFactory.untuk("mahasiswa").hitung(-2));

        AturanDenda alumni = AturanDendaFactory.untuk("alumni");
        assertInstanceOf(DendaAlumni.class, alumni);
        assertThrows(IllegalArgumentException.class, () -> AturanDendaFactory.untuk("tamu"));
    }

    @Test
    @DisplayName("LibraryService meneruskan kegagalan repository palsu tanpa bergantung pada penyimpanan nyata")
    void ujiDenganRepositoryPalsuYangSelaluGagal() {
        LibraryService service = new LibraryService(new FailingBookRepository(), new DendaMahasiswa());
        service.daftarkanAnggota("A-001");

        RuntimeException ex = assertThrows(RuntimeException.class, () -> service.pinjam("A-001", "978-1"));
        assertTrue(ex.getMessage().contains("Koneksi basis data gagal"));
    }
}
