/**
 * Sesi 2 — enkapsulasi yang menjaga invariant.
 *
 * Deskripsi masalah:
 *   "Sistem akademik mencatat mahasiswa dengan NIM, nama, dan tiga komponen
 *    nilai: tugas, UTS, dan UAS. NIM tidak pernah berubah setelah mahasiswa
 *    terdaftar. Setiap komponen nilai berada dalam rentang 0 sampai 100.
 *    Nilai akhir dihitung 30% tugas, 30% UTS, 40% UAS."
 *
 * Tuliskan lebih dulu daftar invariant-nya di analisis.md,
 * baru lengkapi TODO di bawah ini.
 */
public class Mahasiswa {

  // Konstanta bobot nilai
    public static final double BOBOT_TUGAS = 0.30;
    public static final double BOBOT_UTS = 0.30;
    public static final double BOBOT_UAS = 0.40;

    private static final double NILAI_MIN = 0;
    private static final double NILAI_MAX = 100;

    // Atribut mahasiswa
    private final String nim;
    private final String nama;
    private final double nilaiTugas;
    private final double nilaiUts;
    private final double nilaiUas;

    // Konstruktor
    public Mahasiswa(String nim, String nama,
                     double nilaiTugas, double nilaiUts,
                     double nilaiUas) {

        // Validasi NIM
        if (nim == null || nim.trim().isEmpty()) {
            throw new IllegalArgumentException(
                "NIM tidak boleh kosong atau null."
            );
        }

        // Validasi nama
        if (nama == null || nama.trim().isEmpty()) {
            throw new IllegalArgumentException(
                "Nama mahasiswa tidak boleh kosong atau null."
            );
        }

        // Validasi seluruh komponen nilai
        pastikanNilaiSah("Tugas", nilaiTugas);
        pastikanNilaiSah("UTS", nilaiUts);
        pastikanNilaiSah("UAS", nilaiUas);

        // Menyimpan data mahasiswa
        this.nim = nim;
        this.nama = nama;
        this.nilaiTugas = nilaiTugas;
        this.nilaiUts = nilaiUts;
        this.nilaiUas = nilaiUas;
    }

    // Memvalidasi nilai agar berada pada rentang 0-100
    private static void pastikanNilaiSah(
            String namaKomponen, double nilai) {

        if (!Double.isFinite(nilai)
                || nilai < NILAI_MIN
                || nilai > NILAI_MAX) {

            throw new IllegalArgumentException(
                "Nilai " + namaKomponen
                + " harus berada pada rentang 0 sampai 100."
            );
        }
    }

    // Menghitung nilai akhir
    public double nilaiAkhir() {
        return (nilaiTugas * BOBOT_TUGAS)
             + (nilaiUts * BOBOT_UTS)
             + (nilaiUas * BOBOT_UAS);
    }

    // Menentukan huruf mutu berdasarkan nilai akhir
    public String hurufMutu() {
        double nilai = nilaiAkhir();

        if (nilai >= 80) {
            return "A";
        } else if (nilai >= 70) {
            return "B";
        } else if (nilai >= 60) {
            return "C";
        } else if (nilai >= 50) {
            return "D";
        } else {
            return "E";
        }
    }

    // Getter
    public String getNim() {
        return nim;
    }

    public String getNama() {
        return nama;
    }

    public double getNilaiAkhir() {
        return nilaiAkhir();
    }

    // Menampilkan data mahasiswa
    @Override
    public String toString() {
        return String.format(
            "%-10s %-18s akhir=%6.2f  mutu=%s",
            nim, nama, nilaiAkhir(), hurufMutu()
        );
    }
}
