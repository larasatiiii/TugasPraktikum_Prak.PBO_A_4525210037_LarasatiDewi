package id.ac.univpancasila.perpustakaan.denda;

// TODO 3 (Langkah 3): bukti Open-Closed Principle — tambah DendaAlumni tanpa menyunting LibraryService
public class DendaAlumni implements AturanDenda {

    private static final double TARIF_PER_HARI = 750.0;

    @Override
    public double hitung(int hariTerlambat) {
        return Math.max(0, hariTerlambat) * TARIF_PER_HARI;
    }

    @Override
    public String nama() {
        return "Alumni (Rp750/hari)";
    }
}
