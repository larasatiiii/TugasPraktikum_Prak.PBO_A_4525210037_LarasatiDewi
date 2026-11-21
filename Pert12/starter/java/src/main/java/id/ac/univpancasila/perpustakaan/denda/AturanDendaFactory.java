package id.ac.univpancasila.perpustakaan.denda;

// TODO 2: factory untuk memilih implementasi AturanDenda berdasarkan jenis anggota
public final class AturanDendaFactory {

    private AturanDendaFactory() {
    }

    public static AturanDenda untuk(String jenisAnggota) {
        if (jenisAnggota == null) {
            throw new IllegalArgumentException("Jenis anggota tidak boleh null.");
        }
        switch (jenisAnggota.trim().toLowerCase()) {
            case "mahasiswa":
                return new DendaMahasiswa();
            case "dosen":
                return new DendaDosen();
            case "kehormatan":
            case "bebas":
                return new DendaBebas();
            case "alumni":
                return new DendaAlumni();
            default:
                throw new IllegalArgumentException("Jenis anggota tidak dikenal: " + jenisAnggota);
        }
    }
}
