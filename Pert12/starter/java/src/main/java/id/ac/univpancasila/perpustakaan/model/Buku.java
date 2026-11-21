package id.ac.univpancasila.perpustakaan.model;

import java.util.ArrayList;
import java.util.List;


public class Buku {

    private final String nomorInduk;
    private final String judul;
    private final String penulis;
    private int jumlahEksemplar;
    private final List<Bab> bab = new ArrayList<>();

    public Buku(String nomorInduk, String judul, String penulis, int jumlahEksemplar) {
        if (jumlahEksemplar < 0){
            throw new IllegalAccessError("Jumlah Eksemplar tidak boleh negatif!");
        }

        this.nomorInduk = nomorInduk;
        this.judul = judul;
        this.penulis = penulis;
        this.jumlahEksemplar = jumlahEksemplar;
    }


    public void tambahBab(int nomor, String judulBab) {
        Bab objekBab = new Bab(nomor, judulBab);
        this.bab.add(objekBab);
    }

    public List<Bab> getBab() {
        return List.copyOf(this.bab);
    }

    public boolean tersedia() { return jumlahEksemplar > 0; }

    public void kurangiStok() {
       if (jumlahEksemplar <= 0) {
            throw new IllegalStateException("Gagal mengurangi stok: Stok eksemplar sudah 0.");
        }
        this.jumlahEksemplar--;
    }

    public void tambahStok() { jumlahEksemplar++; }

    public String getNomorInduk()  { return nomorInduk; }
    public String getJudul()       { return judul; }
    public int getJumlahEksemplar() { return jumlahEksemplar; }
    public String getPenulis(){return penulis;}

    @Override
    public String toString() {
        return String.format("%-32s %-16s [%d eks, %d bab]",
                judul, penulis, jumlahEksemplar, bab.size());
    }


    public static final class Bab {
        private final int nomor;
        private final String judul;

        private   Bab(int nomor, String judul) {
            this.nomor = nomor;
            this.judul = judul;
        }

        public int getNomor()    { return nomor; }
        public String getJudul() { return judul; }

        @Override public String toString() { return "Bab " + nomor + ": " + judul; }
    }
}
