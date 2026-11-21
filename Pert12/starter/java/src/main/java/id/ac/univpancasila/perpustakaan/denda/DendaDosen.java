package id.ac.univpancasila.perpustakaan.denda;

// TODO 1: implementasi AturanDenda untuk Dosen (Rp500/hari)
public class DendaDosen implements AturanDenda {

    private static final double TARIF_PER_HARI = 500.0;

    @Override
    public double hitung(int hariTerlambat) {
        return Math.max(0, hariTerlambat) * TARIF_PER_HARI;
    }

    @Override
    public String nama() {
        return "Dosen (Rp500/hari)";
    }
}
