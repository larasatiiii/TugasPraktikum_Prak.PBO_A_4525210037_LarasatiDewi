public class PegawaiKontrak extends Pegawai {

    private final int bulanKontrak;

    public PegawaiKontrak(String nip, String nama, double gajiPokok, int bulanKontrak) {
        super(nip, nama, gajiPokok);
        this.bulanKontrak = bulanKontrak;
    }

    // TODO 2: pegawai kontrak TIDAK mendapat tunjangan masa kerja.
    //         Apakah method hitungGaji() perlu di-override di sini?
    //         Tidak perlu di-override, karena implementasi default pada superclass Pegawai
    //         sudah mengembalikan gajiPokok apa adanya tanpa tunjangan.

    @Override
    public String jenis() { return "KONTRAK"; }

    public int getBulanKontrak() { return bulanKontrak; }
}