# Sistem Perpustakaan — Exception Handling dan Pemrograman Defensif

Tugas Pertemuan 10, mata kuliah Pemrograman Berorientasi Objek (PBO), Universitas Pancasila.
Proyek ini mengimplementasikan sistem peminjaman buku sederhana dalam dua bahasa, **Java** dan **PHP**, dengan fokus pada:

- hierarki exception domain (`PerpustakaanException` sebagai induk),
- exception yang **membawa data** (ISBN, ID anggota, judul, batas pinjam), bukan sekadar teks pesan,
- refactoring dari *nested if* (`LibraryServiceKotor`) menjadi *guard clause* (`LibraryService`),
- penanganan kegagalan dengan `try-catch`, serta `try-with-resources` (Java) dan `try-finally` (PHP) untuk menulis berkas laporan.

---

## Struktur Proyek

```
perpustakaan/
├── java/
│   ├── pom.xml
│   └── src/main/java/id/ac/univpancasila/perpustakaan/
│       ├── Main.java
│       ├── exception/
│       │   ├── PerpustakaanException.java
│       │   ├── AnggotaTidakDitemukanException.java
│       │   ├── BatasPinjamTerlampauiException.java
│       │   ├── BukuSedangDipinjamException.java
│       │   └── BukuTidakDitemukanException.java
│       └── service/
│           ├── LibraryService.java          (versi guard clause)
│           ├── LibraryServiceKotor.java     (bahan refactoring)
│           └── PerpustakaanService.java     (menu interaktif)
└── php/
    ├── composer.json
    ├── Main.php                              (demo, di luar src)
    └── src/
        ├── Exception/                        (5 kelas, sama seperti Java)
        └── Service/
            ├── LibraryService.php
            └── LibraryServiceKotor.php
```

---

## Hierarki Exception

```
RuntimeException
└── PerpustakaanException (abstract)
    ├── BukuTidakDitemukanException        → membawa isbn
    ├── AnggotaTidakDitemukanException     → membawa idAnggota
    ├── BukuSedangDipinjamException        → membawa isbn dan judul
    └── BatasPinjamTerlampauiException     → membawa idAnggota dan batas
```

Semua exception diturunkan dari `RuntimeException` (*unchecked*), karena kegagalan ini menandakan permintaan yang tidak sah, bukan keadaan luar yang wajar. Dengan satu induk bersama, pemanggil bisa menangkap seluruh kegagalan domain dengan satu blok `catch (PerpustakaanException e)`.

### Aturan peminjaman (urutan guard clause)

`pinjam(idAnggota, isbn)` memeriksa berurutan, dan langsung melempar exception pada kegagalan pertama:

| No | Kondisi                           | Exception                          |
|----|-----------------------------------|------------------------------------|
| 1  | Anggota belum terdaftar           | `AnggotaTidakDitemukanException`   |
| 2  | ISBN tidak ada di katalog         | `BukuTidakDitemukanException`      |
| 3  | Stok buku habis                   | `BukuSedangDipinjamException`      |
| 4  | Anggota sudah meminjam 3 buku     | `BatasPinjamTerlampauiException`   |

Jika semua lolos, stok berkurang satu dan jumlah pinjaman anggota bertambah satu. Batas pinjam maksimum adalah `BATAS_PINJAM = 3`.

---

## Menjalankan Versi Java

### Prasyarat

- JDK **17** atau lebih baru (kode memakai pattern matching `instanceof`, yang butuh Java 16+)
- Maven 3.8+

Cek dengan `java -version` dan `mvn -version`.

### Langkah

```bash
cd java
mvn compile exec:java
```

Atau buat JAR lalu jalankan:

```bash
cd java
mvn package
java -jar target/perpustakaan-1.0-SNAPSHOT.jar
```

### Cara pakai

Program menampilkan menu interaktif:

```
=== SISTEM PERPUSTAKAAN ===
1. Tambah Buku
2. Tambah Anggota
3. Pinjam Buku
0. Keluar
```

Urutan yang disarankan untuk mencoba:

1. Menu **1**: tambah buku (ISBN `111`, judul `Laskar Pelangi`, 1 eksemplar).
2. Menu **2**: daftarkan anggota (`A-001`).
3. Menu **3**: pinjam buku dengan `A-001` dan ISBN `111`. Hasilnya berhasil.
4. Ulangi langkah 3: akan muncul `BukuSedangDipinjamException` karena stok habis.
5. Coba ID anggota atau ISBN yang belum ada untuk melihat exception lainnya.
6. Menu **0**: keluar. Laporan otomatis ditulis ke `laporan_buku.txt`.

---

## Menjalankan Versi PHP

### Prasyarat

- PHP **8.1** atau lebih baru
- [Composer](https://getcomposer.org)

Cek dengan `php -v` dan `composer -V`.

> **Catatan versi:** kode ini memakai `readonly` dan *constructor property promotion* yang butuh PHP 8.1+.
> Konstanta bertipe (`public const int BATAS_PINJAM`) baru didukung di PHP **8.3**.
> Jika memakai PHP 8.1 atau 8.2, tulis `public const BATAS_PINJAM = 3;` (tanpa `int`) di `LibraryService.php` dan `LibraryServiceKotor.php`, dan pastikan `composer.json` berisi `"php": ">=8.1"`.

### Langkah

```bash
cd php
composer install
php Main.php
```

`composer install` **wajib** dijalankan sekali agar folder `vendor/` dan `vendor/autoload.php` terbentuk. Tanpa itu, akan muncul error `Failed opening required '.../vendor/autoload.php'`.

Alternatif: `composer start` (menjalankan `php Main.php` lewat script Composer).

### Keluaran yang diharapkan

`Main.php` menjalankan tujuh percobaan peminjaman:

| Anggota | ISBN | Hasil                                       |
|---------|------|---------------------------------------------|
| A-001   | 111  | Berhasil                                    |
| A-002   | 111  | Gagal, buku sedang dipinjam (stok habis)    |
| A-002   | 999  | Gagal, buku tidak ditemukan                 |
| X-999   | 222  | Gagal, anggota tidak ditemukan              |
| A-001   | 222  | Berhasil                                    |
| A-001   | 222  | Berhasil                                    |
| A-001   | 222  | Gagal, batas pinjam tercapai                |

Setelah itu laporan ditulis ke `laporan_buku.txt` di folder `php/`.

---

## Berkas Laporan

Kedua versi menghasilkan `laporan_buku.txt` dengan format:

```
ISBN         | JUDUL BUKU                          | STOK     | STATUS
111          | Laskar Pelangi                      | 0        | Tidak Tersedia
222          | Bumi Manusia                        | 2        | Tersedia
```

- Java memakai `try-with-resources` sehingga berkas otomatis tertutup.
- PHP memakai blok `finally` untuk memastikan `fclose()` selalu dipanggil, bahkan saat terjadi error di tengah penulisan.

---

## Pemecahan Masalah

| Gejala | Penyebab dan solusi |
|--------|---------------------|
| `Failed opening required '.../vendor/autoload.php'` | Jalankan `composer install` di folder `php`. |
| `composer` tidak dikenali | Install Composer, lalu buka ulang terminal. |
| Parse error di `const int` | PHP di bawah 8.3. Hapus kata `int`, lihat catatan versi di atas. |
| Composer meminta ekstensi `openssl` atau `mbstring` | Aktifkan di `php.ini` (hapus tanda `;` di depan `extension=...`). |
| Java: `package ...exception does not exist` | Folder `exception/` beserta 5 berkasnya belum ada di proyek. |
| Java: `invalid target release` atau error pada `instanceof` | JDK di bawah 17. Install JDK 17+ dan atur `JAVA_HOME`. |
| `mvn` tidak dikenali | Install Maven dan tambahkan ke Path. |

---

## Catatan Pengembangan

- `LibraryServiceKotor` sengaja dipertahankan sebagai pembanding: `pinjam()` bersarang empat tingkat dan hanya mengembalikan `false`, sehingga pemanggil tidak tahu alasan kegagalan. `LibraryService` memperbaikinya dengan guard clause dan exception yang informatif.
- Di `PerpustakaanService.pinjamBuku` (Java), jika ID anggota kosong program hanya mencetak peringatan lalu tetap meminta ISBN. Tambahkan `continue;` setelah peringatan tersebut jika ingin perulangan diulang dari awal.
- Data disimpan di memori (`HashMap` / array), sehingga hilang saat program berhenti.