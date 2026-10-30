package id.ac.univpancasila.perpustakaan;

import com.github.freva.asciitable.AsciiTable;
import id.ac.univpancasila.perpustakaan.model.Anggota;
import id.ac.univpancasila.perpustakaan.model.Buku;
import id.ac.univpancasila.perpustakaan.model.Peminjaman;
import id.ac.univpancasila.perpustakaan.service.PerpustakaanService;
import id.ac.univpancasila.perpustakaan.util.Format;

import java.time.LocalDate;
import java.util.List;

/**
 * Titik masuk utama aplikasi Sistem Perpustakaan (Java / Maven).
 */
public class App {

    public static void main(String[] args) {
        System.out.println("=======================================================================");
        System.out.println("    SISTEM PERPUSTAKAAN UNIVERSITAS PANCASILA (MAVEN MODUL 9)");
        System.out.println("=======================================================================\n");

        PerpustakaanService service = new PerpustakaanService();

        // 1. Tambah Koleksi Buku beserta Bab (Komposisi)
        Buku b1 = new Buku("B-001", "Pemrograman Berorientasi Objek", "Dr. A. W. Pribadi", 4);
        b1.tambahBab(1, "Konsep Objek dan Kelas");
        b1.tambahBab(2, "Enkapsulasi & Immutability");
        b1.tambahBab(3, "Package & Build Tools");

        Buku b2 = new Buku("B-002", "Struktur Data & Algoritma", "Ir. Budi Raharjo", 2);
        b2.tambahBab(1, "Array & Linked List");
        b2.tambahBab(2, "Tree & Graph");

        Buku b3 = new Buku("B-003", "Rekayasa Perangkat Lunak Modern", "Prof. R. Pressman", 1);
        b3.tambahBab(1, "Agile & Scrum Process");
        b3.tambahBab(2, "Clean Architecture & Design Pattern");

        service.tambahKoleksi(b1);
        service.tambahKoleksi(b2);
        service.tambahKoleksi(b3);

        // 2. Daftarkan Anggota
        Anggota a1 = new Anggota("A-2024001", "Siti Aisyah", "Mahasiswa");
        Anggota a2 = new Anggota("A-2024002", "Bambang Pamungkas", "Mahasiswa");
        Anggota a3 = new Anggota("D-1985010", "Dr. Hendra Gunawan", "Dosen");

        service.daftarkanAnggota(a1);
        service.daftarkanAnggota(a2);
        service.daftarkanAnggota(a3);

        // 3. Simulasi Transaksi Peminjaman
        LocalDate tglSekarang = LocalDate.of(2026, 10, 20);
        Peminjaman p1 = service.pinjam("A-2024001", "B-001", tglSekarang, 14);
        Peminjaman p2 = service.pinjam("D-1985010", "B-003", tglSekarang, 14);

        // Simulasi pengembalian terlambat (p1 dikembalikan 5 hari setelah jatuh tempo)
        LocalDate tglKembaliP1 = p1.getTanggalJatuhTempo().plusDays(5);
        long dendaP1 = service.kembalikan(p1.getIdPeminjaman(), tglKembaliP1);

        // 4. Tampilkan Tabel Koleksi Buku menggunakan AsciiTable (Dependensi Eksternal)
        System.out.println("[1] DAFTAR KOLEKSI BUKU PERPUSTAKAAN:");
        String[] headerBuku = {"Kode", "Judul Buku", "Penulis", "Stok", "Jumlah Bab"};
        List<Buku> koleksi = service.getDaftarKoleksi();
        String[][] dataBuku = new String[koleksi.size()][5];
        for (int i = 0; i < koleksi.size(); i++) {
            Buku b = koleksi.get(i);
            dataBuku[i][0] = b.getNomorInduk();
            dataBuku[i][1] = b.getJudul();
            dataBuku[i][2] = b.getPenulis();
            dataBuku[i][3] = String.valueOf(b.getJumlahEksemplar());
            dataBuku[i][4] = b.getBab().size() + " bab";
        }
        System.out.println(AsciiTable.getTable(headerBuku, dataBuku));
        System.out.println();

        // 5. Tampilkan Tabel Transaksi Peminjaman menggunakan AsciiTable
        System.out.println("[2] DAFTAR TRANSAKSI PEMINJAMAN:");
        String[] headerPinjam = {"ID", "Peminjam", "Peran", "Judul Buku", "Tgl Pinjam", "Jatuh Tempo", "Status / Tgl Kembali"};
        List<Peminjaman> peminjamanList = service.getDaftarPeminjaman();
        String[][] dataPinjam = new String[peminjamanList.size()][7];
        for (int i = 0; i < peminjamanList.size(); i++) {
            Peminjaman p = peminjamanList.get(i);
            dataPinjam[i][0] = p.getIdPeminjaman();
            dataPinjam[i][1] = p.getAnggota().getNama();
            dataPinjam[i][2] = p.getAnggota().getJenisKeanggotaan();
            dataPinjam[i][3] = p.getBuku().getJudul();
            dataPinjam[i][4] = Format.formatTanggal(p.getTanggalPinjam());
            dataPinjam[i][5] = Format.formatTanggal(p.getTanggalJatuhTempo());
            dataPinjam[i][6] = p.sudahKembali() 
                    ? "Kembali (" + Format.formatTanggal(p.getTanggalKembali()) + ")" 
                    : "MASIH DIPINJAM";
        }
        System.out.println(AsciiTable.getTable(headerPinjam, dataPinjam));
        System.out.println();

        // 6. Rincian Denda
        System.out.println("[3] SIMULASI PERHITUNGAN DENDA KETERLAMBATAN:");
        System.out.println("  ID Transaksi      : " + p1.getIdPeminjaman());
        System.out.println("  Peminjam          : " + p1.getAnggota().getNama() + " (" + p1.getAnggota().getJenisKeanggotaan() + ")");
        System.out.println("  Tgl Jatuh Tempo   : " + Format.formatTanggal(p1.getTanggalJatuhTempo()));
        System.out.println("  Tgl Dikembalikan  : " + Format.formatTanggal(p1.getTanggalKembali()));
        System.out.println("  Total Keterlambatan : 5 hari");
        System.out.println("  Total Denda       : " + Format.formatRupiah(dendaP1));
        System.out.println("\nProgram selesai dieksekusi dengan sukses.");
    }
}
