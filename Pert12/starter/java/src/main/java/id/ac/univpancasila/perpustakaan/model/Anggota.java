package id.ac.univpancasila.perpustakaan.model;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class Anggota extends Jurusan{
    private String kodeAnggota;
    private LocalDateTime anggotaSejak;
    private Mahasiswa anggotaPerpus;

    public Anggota(String kodeJurusan, String namaJurusan, String kodeAnggota){
        super(kodeJurusan, namaJurusan);
        this.kodeAnggota = kodeAnggota;
    }

    public void daftarkan(Mahasiswa m, LocalDateTime masaKeanggotaan){
        if (m == null){
            throw new IllegalAccessError("Mahasiswa tidak boleh kosong");
        }

        if (masaKeanggotaan == null){
            throw new IllegalArgumentException("Masa keanggotaan tidak boleh kosong");
        }

        this.anggotaPerpus = m;
        this.anggotaSejak = masaKeanggotaan;
    }

    public String checkMasaKeanggotaan(){
        if (this.anggotaSejak == null){
            return "Keanggotaan belum terdaftar";
        }

        LocalDateTime dateNow = LocalDateTime.now();
        String status;

        if (dateNow.isAfter(this.anggotaSejak.plusYears((3))) ){
           status = "Keanggotaan tidak valid (lebih dari 3 tahun)";
        } else {
            status = "Keanggotaan valid";
        }

        return status;
    }

    public void getKeanggotaan(){
        String namaMhs = (anggotaPerpus != null) ? anggotaPerpus.getNama() : "-";
        String tglSejak = (anggotaSejak != null) ? formatDateMasaAnggota(anggotaSejak) : "-";

        System.out.println("Nama Anggota: " + namaMhs);
        System.out.println("Jurusan: " + super.getNama());
        System.out.println("Kode Anggota: " + this.kodeAnggota);
        System.out.println("Anggota Perpustakaan Sejak: "+ tglSejak);
    }

    private String formatDateMasaAnggota(LocalDateTime date){
        DateTimeFormatter formatMasaAnggota = DateTimeFormatter.ofPattern("dd-MM-yyy");
        String formatedDate = formatMasaAnggota.format(date);

        return formatedDate;
    }
}
