package id.ac.univpancasila.perpustakaan.exception;

/**
 * EXCEPTION YANG MEMBAWA DATA — bukan sekadar teks.
 *
 * Perhatikan atribut isbn di bawah. Itulah yang membedakan exception ini
 * dari exception biasa: pemanggil bisa MENGAMBIL data penyebabnya kembali,
 * bukan harus mengurai kalimat pesan.
 *
 * Pada sesi 13 dan 14, data inilah yang menjadi isi badan respons HTTP 404.
 */
public class BukuTidakDitemukanException extends PerpustakaanException {
    // Atribut privat final untuk isbn.
    private final String isbn;

    public BukuTidakDitemukanException(String isbn) {
        // Pesan menyebut ISBN-nya, misalnya: "Buku dengan ISBN 978-1 tidak ditemukan"
        super("Buku dengan ISBN " + isbn + " tidak ditemukan");
        // Simpan isbn ke atribut.
        this.isbn = isbn;
    }

    // Getter agar pemanggil bisa mengambil kembali ISBN penyebabnya.
    public String getIsbn() {
        return this.isbn;
    }
}
