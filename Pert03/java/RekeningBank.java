
package Pert03.java;

/**
 * Sesi 3 — constructor berdelegasi, anggota statis, dan konstanta.
 *
 * Invariant:
 * 1. Saldo tidak pernah negatif.
 * 2. Nomor rekening tidak berubah setelah objek dibuat.
 * 3. Setoran dan penarikan selalu bernilai positif.
 */
public class RekeningBank {

    // TODO 1: konstanta bernama
    public static final double BUNGA_TAHUNAN = 0.025;
    public static final double BIAYA_ADMIN = 5000;
    public static final double BATAS_PENARIKAN = 5000000;

    // TODO 2: penghitung jumlah rekening
    private static int jumlahRekening = 0;

    private final String nomor;
    private final String pemilik;
    private double saldo;

    /**
     * TODO 3: constructor ringkas berdelegasi
     */
    public RekeningBank(String nomor, String pemilik) {
        this(nomor, pemilik, 0);
    }

    /**
     * TODO 4 dan TODO 5:
     * Validasi data dan hitung rekening yang berhasil dibuat.
     */
    public RekeningBank(String nomor, String pemilik, double saldoAwal) {
        if (nomor == null || nomor.trim().isEmpty()) {
            throw new IllegalArgumentException(
                "Nomor rekening tidak boleh kosong."
            );
        }

        if (pemilik == null || pemilik.trim().isEmpty()) {
            throw new IllegalArgumentException(
                "Nama pemilik tidak boleh kosong."
            );
        }

        if (!Double.isFinite(saldoAwal) || saldoAwal < 0) {
            throw new IllegalArgumentException(
                "Saldo awal tidak boleh negatif atau tidak valid."
            );
        }

        this.nomor = nomor;
        this.pemilik = pemilik;
        this.saldo = saldoAwal;

        jumlahRekening++;
    }

    /**
     * TODO 6: setor uang
     */
    public void setor(double jumlah) {
        if (!Double.isFinite(jumlah) || jumlah <= 0) {
            throw new IllegalArgumentException(
                "Jumlah setoran harus lebih besar dari 0."
            );
        }

        saldo += jumlah;
    }

    /**
     * TODO 7: tarik uang
     */
    public void tarik(double jumlah) {
        if (!Double.isFinite(jumlah) || jumlah <= 0) {
            throw new IllegalArgumentException(
                "Jumlah penarikan harus lebih besar dari 0."
            );
        }

        if (jumlah > BATAS_PENARIKAN) {
            throw new IllegalArgumentException(
                "Penarikan melebihi batas Rp5.000.000 per transaksi."
            );
        }

        if (jumlah > saldo) {
            throw new IllegalArgumentException(
                "Saldo tidak mencukupi untuk penarikan."
            );
        }

        saldo -= jumlah;
    }

    /**
     * TODO 8: potong biaya administrasi tanpa membuat saldo negatif
     */
    public void potongBiayaAdmin() {
        if (saldo >= BIAYA_ADMIN) {
            saldo -= BIAYA_ADMIN;
        } else {
            saldo = 0;
        }
    }

    /**
     * TODO 9: jumlah rekening yang berhasil dibuat
     */
    public static int getJumlahRekening() {
        return jumlahRekening;
    }

    /**
     * TODO 10: hitung bunga setahun
     */
    public static double bungaSetahun(double pokok) {
        if (!Double.isFinite(pokok) || pokok < 0) {
            throw new IllegalArgumentException(
                "Pokok bunga tidak boleh negatif atau tidak valid."
            );
        }

        return pokok * BUNGA_TAHUNAN;
    }

    public double getSaldo() {
        return saldo;
    }

    public String getNomor() {
        return nomor;
    }

    @Override
    public String toString() {
        return String.format(
            "Rekening[%s] %-14s Rp%,.2f",
            nomor, pemilik, saldo
        );
    }
}