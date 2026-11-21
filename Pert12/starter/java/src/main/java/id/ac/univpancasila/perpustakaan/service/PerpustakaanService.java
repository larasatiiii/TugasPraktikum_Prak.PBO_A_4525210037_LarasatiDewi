package id.ac.univpancasila.perpustakaan.service;

import java.util.Scanner;

import id.ac.univpancasila.perpustakaan.exception.AnggotaTidakDitemukanException;
import id.ac.univpancasila.perpustakaan.exception.BatasPinjamTerlampauiException;
import id.ac.univpancasila.perpustakaan.exception.BukuSedangDipinjamException;
import id.ac.univpancasila.perpustakaan.exception.BukuTidakDitemukanException;
import id.ac.univpancasila.perpustakaan.exception.PerpustakaanException;

public class PerpustakaanService {
    public static void tambahAnggota(LibraryService libService, Scanner scanner) {
        System.out.println("\n--- Form Tambah Anggota ---");
        System.out.print("Masukkan ID Anggota: ");
        String idAnggota = scanner.nextLine().trim();

        if (idAnggota.isEmpty()) {
            System.out.println("[Gagal] ID Anggota tidak boleh kosong!");
            return;
        }

        libService.daftarkanAnggota(idAnggota);
        System.out.println(">> BERHASIL: Anggota dengan ID '" + idAnggota + "' telah terdaftar.");
    }

   public static void tambahBuku(LibraryService libService, Scanner scanner) {
        System.out.println("\n--- Form Tambah Buku ---");
        System.out.print("Masukkan ISBN Buku: ");
        String isbn = scanner.nextLine().trim();

        if (isbn.isEmpty()) {
            System.out.println("[Gagal] ISBN tidak boleh kosong!");
            return;
        }

        System.out.print("Masukkan Judul Buku: ");
        String judul = scanner.nextLine().trim();

        System.out.print("Masukkan Penulis Buku: ");
        String penulis = scanner.nextLine().trim();
        if (penulis.isEmpty()) {
            penulis = "Tidak Diketahui";
        }

        System.out.print("Masukkan Jumlah Eksemplar: ");
        int eksemplar = 0;
        try {
            eksemplar = Integer.parseInt(scanner.nextLine().trim());
            if (eksemplar <= 0) {
                System.out.println("[Gagal] Jumlah eksemplar harus lebih dari 0!");
                return;
            }
        } catch (NumberFormatException e) {
            System.out.println("[Error Input] Jumlah eksemplar harus berupa angka!");
            return;
        }

        libService.tambahBuku(isbn, judul,penulis, eksemplar);
        System.out.println(">> BERHASIL: Buku '" + judul + "' (" + eksemplar + " eksemplar) ditambahkan.");
    }

    public  static void pinjamBuku(LibraryService libService, Scanner scanner){
        System.out.println("=== SISTEM PERPUSTAKAAN (PERT 10) ===");

        while (true) {
            try {
                System.out.println("\n --- Form Peminjaman Buku ---");
                System.out.print("Masukan ID Anggota (Ketik 'exit' untuk keluar): ");
                String idAngota = scanner.nextLine().trim();
                
                if (idAngota.equalsIgnoreCase("exit")){
                    break;
                }

                if (idAngota.isEmpty()){
                    System.out.println("[Peringatan] ID Anggota tidak boleh kosong!");
                }

                System.out.print("Masukan ISBN Buku: ");
                String isbn = scanner.nextLine().trim();

                libService.pinjam(idAngota, isbn);
                System.out.println("Peminjaman Berhasil!");
            } catch (PerpustakaanException e) {
                System.out.println("[Error Domain] " + e.getMessage());

                if (e instanceof BukuTidakDitemukanException ex){
                    System.out.println("ISBN bermasalah: " + ex.getIsbn());
                }else if(e instanceof AnggotaTidakDitemukanException ex){
                    System.out.println("Id Anggota bermasalah: " + ex.getIdAnggota());
                } else if(e instanceof BukuSedangDipinjamException ex){
                    System.out.println("  -> Judul Buku: " + ex.getJudul() + " (ISBN: " + ex.getIsbn() + ")");
                }else if(e instanceof BatasPinjamTerlampauiException ex) {
                    System.out.println("  -> Batas Maksimum: " + ex.getBatas() + " buku");
                }
            }catch(Exception e){
                System.out.println("[Error Sistem] Terjadi kesalahan input " + e.getMessage());
            }
        }
    }

    public static void tampilkanLaporanFungsional(LibraryService libService) {
        System.out.println("\n=== LAPORAN FUNGSIONAL (HASIL SESI 11) ===");

        // 1. Menampilkan daftar buku yang tersedia (stok > 0)
        System.out.println("\n1. Daftar Buku Tersedia:");
        if (libService.getBukuTersedia().isEmpty()) {
            System.out.println("   (Tidak ada buku yang tersedia saat ini)");
        } else {
            libService.getBukuTersedia().forEach(buku -> 
                System.out.println("   - " + buku.toString())
            );
        }

        // 2. Menampilkan gabungan semua judul buku
        System.out.println("\n2. Gabungan Semua Judul Buku:");
        String gabunganJudul = libService.getSemuaJudulDigabung();
        if (gabunganJudul.isEmpty()) {
            System.out.println("   (Belum ada judul buku di perpustakaan)");
        } else {
            System.out.println("   " + gabunganJudul);
        }

        // 3. Menampilkan jumlah judul buku per penulis
        System.out.println("\n3. Jumlah Buku per Penulis:");
        if (libService.getJumlahBukuPerPenulis().isEmpty()) {
            System.out.println("   (Data penulis kosong)");
        } else {
            libService.getJumlahBukuPerPenulis().forEach((penulis, jumlah) -> 
                System.out.println("   - " + penulis + " : " + jumlah + " judul buku")
            );
        }
        System.out.println("==========================================");
    }
}
