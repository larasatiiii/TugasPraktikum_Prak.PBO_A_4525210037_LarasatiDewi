package id.ac.univpancasila.perpustakaan.denda;

/**
 * Sesi 12 — STRATEGY PATTERN.
 *
 * Interface untuk menghitung denda keterlambatan berdasarkan jenis anggota.
 */
public interface AturanDenda {

    /** Hitung denda untuk sejumlah hari keterlambatan. Hari negatif dianggap nol. */
    double hitung(int hariTerlambat);

    /** Nama aturan, untuk ditampilkan pada laporan. */
    String nama();
}
