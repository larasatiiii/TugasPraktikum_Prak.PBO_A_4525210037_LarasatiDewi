package id.ac.univpancasila.perpustakaan;

import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.function.Function;

/**
 * Sesi 11 — kelas generik Anda sendiri.
 *
 * <T> adalah TYPE PARAMETER: kelas ini bekerja untuk tipe apa pun, tetapi
 * setelah dipakai sebagai Repository<Buku>, kompilator memastikan hanya Buku
 * yang bisa masuk dan keluar.
 *
 * Bandingkan tanda tangannya dengan JpaRepository<Buku, Long> yang akan Anda
 * temui di Spring Data JPA pada sesi 14. Gagasannya persis sama.
 */
public class Repository<T> {

    private final Map<String, T> data = new LinkedHashMap<>();

    /**
     * Fungsi untuk mengambil kunci dari sebuah item.
     * Dengan cara ini Repository tidak perlu tahu apa pun tentang T.
     */
    private final Function<T, String> pengambilKunci;

    public Repository(Function<T, String> pengambilKunci) {
        this.pengambilKunci = pengambilKunci;
    }

    /** Simpan item, dengan kunci diambil lewat pengambilKunci. Kunci yang sama menimpa item lama. */
    public void simpan(T item) {
        data.put(pengambilKunci.apply(item), item);
    }

    /**
     * Kembalikan Optional yang berisi item bila ada, kosong bila tidak.
     * Tidak pernah mengembalikan null.
     */
    public Optional<T> cariById(String id) {
        return Optional.ofNullable(data.get(id));
    }

    /**
     * Kembalikan SALINAN TAK-BERUBAH dari seluruh isi.
     * Pelajaran sesi 7 tetap berlaku: koleksi internal tidak boleh bocor.
     */
    public List<T> cariSemua() {
        return List.copyOf(data.values());
    }

    /** Hapus item; kembalikan true bila ada yang terhapus. */
    public boolean hapus(String id) {
        return data.remove(id) != null;
    }

    public int jumlah() { return data.size(); }
}
