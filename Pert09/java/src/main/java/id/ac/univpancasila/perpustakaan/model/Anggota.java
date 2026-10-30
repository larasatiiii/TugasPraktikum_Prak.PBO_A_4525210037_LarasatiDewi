package id.ac.univpancasila.perpustakaan.model;

/**
 * Entitas Anggota perpustakaan.
 */
public class Anggota {

    private final String nomorAnggota;
    private final String nama;
    private final String jenisKeanggotaan;

    public Anggota(String nomorAnggota, String nama, String jenisKeanggotaan) {
        if (nomorAnggota == null || nomorAnggota.isBlank()) {
            throw new IllegalArgumentException("Nomor anggota tidak boleh kosong.");
        }
        if (nama == null || nama.isBlank()) {
            throw new IllegalArgumentException("Nama tidak boleh kosong.");
        }
        this.nomorAnggota = nomorAnggota;
        this.nama = nama;
        this.jenisKeanggotaan = jenisKeanggotaan;
    }

    public String getNomorAnggota() {
        return nomorAnggota;
    }

    public String getNama() {
        return nama;
    }

    public String getJenisKeanggotaan() {
        return jenisKeanggotaan;
    }

    @Override
    public String toString() {
        return String.format("[%s] %s (%s)", nomorAnggota, nama, jenisKeanggotaan);
    }
}
