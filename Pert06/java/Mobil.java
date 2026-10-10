package Pert06.java;

/**
 * Satu kelas boleh mewarisi SATU class, tetapi mengimplementasikan BANYAK interface.
 * Tuliskan di keputusan.md: mengapa Java membuat aturan seperti itu?
 */
public class Mobil extends Kendaraan implements Movable, Fuelable {

    private final double kapasitasTangki;
    private double isiTangki = 0;

    public Mobil(String merek, int tahun, double kapasitasTangki) {
        super(merek, tahun);
        this.kapasitasTangki = kapasitasTangki;
    }

    @Override public int jumlahRoda() { return 4; }

    // TODO 1: lengkapi kontrak Movable.
    @Override public void bergerak() {
        // cetak sesuatu seperti "Toyota Avanza melaju di jalan raya"
          System.out.println(merek + "melaju di jalan raya");
    }

    @Override public double kecepatanMaksimum() { return 100; }   // ganti

    // TODO 2: lengkapi kontrak Fuelable.
    //         isiBahanBakar harus menolak jumlah <= 0 dan
    //         tidak boleh mengisi melebihi kapasitas tangki.
    @Override public void isiBahanBakar(double jumlah) {

        if(jumlah <= 0){
            throw new IllegalArgumentException("Jumlah bahan bakar harus lebih dari 0");
        }

        if(isiTangki + jumlah >kapasitasTangki){
            throw new IllegalArgumentException("Pengisian melebihi kapasitas tangki");
        }

        isiTangki += jumlah;
    }

    @Override public double kapasitasTangki() { return kapasitasTangki; }

    @Override public TipeBahanBakar tipeBahanBakar() { return TipeBahanBakar.BENSIN; }

    public double getIsiTangki() { return isiTangki; }
}