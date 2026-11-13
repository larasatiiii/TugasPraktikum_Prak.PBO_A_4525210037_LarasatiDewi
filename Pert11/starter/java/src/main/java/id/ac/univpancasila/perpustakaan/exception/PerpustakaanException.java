package id.ac.univpancasila.perpustakaan.exception;

/**
 * Sesi 10 — induk seluruh exception domain perpustakaan.
 *
 * Manfaat punya induk bersama: pemanggil bisa menangkap SELURUH kegagalan
 * domain sekaligus dengan satu blok catch, tanpa ikut menelan kesalahan
 * pemrograman yang lain.
 *
 * KEPUTUSAN: diturunkan dari RuntimeException (unchecked). Kegagalan domain
 * di sini menandakan permintaan yang tidak sah (ISBN salah, batas terlampaui),
 * bukan keadaan luar yang wajar, sehingga pemanggil tidak dipaksa menulis
 * try/catch di setiap tempat.
 */
public abstract class PerpustakaanException extends RuntimeException {
    protected PerpustakaanException(String pesan) {
        super(pesan);
    }

    protected PerpustakaanException(String pesan, Throwable penyebab) {
        super(pesan, penyebab);
    }
}
