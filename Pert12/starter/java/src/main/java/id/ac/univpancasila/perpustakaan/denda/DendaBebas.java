package id.ac.univpancasila.perpustakaan.denda;

// TODO 1: implementasi AturanDenda untuk Anggota Kehormatan (selalu 0)
public class DendaBebas implements AturanDenda {

    @Override
    public double hitung(int hariTerlambat) {
        return 0.0;
    }

    @Override
    public String nama() {
        return "Kehormatan (Bebas Denda)";
    }
}
