# Laporan Praktikum PBO --- Sistem Perpustakaan Universitas Pancasila

## 1. Identitas dan Deskripsi Proyek

Proyek ini merupakan implementasi aplikasi **Sistem Perpustakaan
Universitas Pancasila** berbasis Command Line Interface (CLI). Program
dibuat dalam dua versi bahasa pemrograman, yaitu **Java menggunakan
Apache Maven** dan **PHP menggunakan Composer**.

Fokus praktikum adalah penerapan konsep Pemrograman Berorientasi Objek
(PBO), pengorganisasian kode menggunakan *package* dan *namespace*,
serta pengelolaan dependensi dan proses build menggunakan Maven dan
Composer.

Aplikasi mensimulasikan pengelolaan koleksi buku, data anggota,
transaksi peminjaman dan pengembalian, serta perhitungan denda
keterlambatan.

## 2. Tujuan Praktikum

1.  Menerapkan konsep class, object, encapsulation, dan relasi
    antarobjek.
2.  Memisahkan kode berdasarkan tanggung jawab ke dalam lapisan model,
    service, dan utilitas.
3.  Memahami pemetaan package Java terhadap struktur direktori sumber.
4.  Memahami mekanisme autoload PSR-4 pada PHP menggunakan Composer.
5.  Mengelola dependensi eksternal menggunakan Maven dan Composer.
6.  Mengetahui dampak ketidaksesuaian package/namespace dengan lokasi
    berkas.

## 3. Teknologi dan Dependensi

  --------------------------------------------------------------------------
  Teknologi                              Kegunaan
  -------------------------------------- -----------------------------------
  Java (JDK 25)                          Implementasi aplikasi versi Java

  Apache Maven                           Build, kompilasi, dan pengelolaan
                                         dependensi Java

  `com.github.freva:ascii-table:1.8.0`   Menampilkan tabel ASCII di terminal
                                         Java

  PHP 8.2 atau 8.4                       Implementasi aplikasi versi PHP

  Composer 2.x                           Pengelolaan dependensi dan autoload
                                         PHP

  `nesbot/carbon`                        Manipulasi tanggal dan perhitungan
                                         selisih hari pada PHP
  --------------------------------------------------------------------------

## 4. Struktur Proyek

``` text
done/
├── README.md
├── percobaan.md
├── dependency-tree.txt
├── java/
│   ├── pom.xml
│   └── src/main/java/id/ac/univpancasila/perpustakaan/
│       ├── App.java
│       ├── model/
│       │   ├── Anggota.java
│       │   ├── Buku.java
│       │   └── Peminjaman.java
│       ├── service/
│       │   └── PerpustakaanService.java
│       └── util/
│           └── Format.java
└── php/
    ├── composer.json
    ├── composer.lock
    ├── main.php
    └── src/
        ├── Model/
        │   ├── Anggota.php
        │   ├── Bab.php
        │   ├── Buku.php
        │   └── Peminjaman.php
        ├── Service/
        │   └── PerpustakaanService.php
        └── Util/
            └── Format.php
```

Folder `target/` pada proyek Java dan `vendor/` pada proyek PHP
merupakan hasil build/instalasi dependensi sehingga dapat dibuat kembali
dan tidak perlu disertakan ke repositori.

## 5. Perancangan dan Tanggung Jawab Kelas

### 5.1 Model

-   **Anggota** menyimpan nomor anggota, nama, dan jenis keanggotaan
    (misalnya Mahasiswa atau Dosen).
-   **Buku** menyimpan nomor induk, judul, penulis, jumlah eksemplar,
    dan daftar bab.
-   **Bab** merepresentasikan bagian dari sebuah buku. Pada implementasi
    Java, `Bab` menjadi kelas bersarang di dalam `Buku`; pada PHP, `Bab`
    didefinisikan dalam berkas tersendiri.
-   **Peminjaman** menyimpan identitas transaksi, anggota peminjam, buku
    yang dipinjam, tanggal pinjam, tanggal jatuh tempo, dan tanggal
    pengembalian.

### 5.2 Service

`PerpustakaanService` menangani aturan bisnis aplikasi, antara lain:

-   Menambahkan koleksi buku dan mencegah nomor induk buku duplikat.
-   Mendaftarkan anggota dan mencegah nomor anggota duplikat.
-   Mencari buku dan anggota berdasarkan nomor identitas.
-   Memproses peminjaman dan pengembalian.
-   Mengurangi stok ketika buku dipinjam dan menambah stok ketika buku
    dikembalikan.
-   Membatasi jumlah peminjaman aktif maksimal tiga transaksi untuk
    setiap anggota.
-   Menghitung denda berdasarkan keterlambatan dan jenis keanggotaan.

### 5.3 Utilitas

Kelas `Format` menyediakan fungsi pemformatan tanggal dan nominal rupiah
agar informasi yang ditampilkan pada terminal lebih mudah dibaca.

## 6. Aturan Proses Peminjaman dan Denda

Alur peminjaman yang diterapkan:

1.  Sistem mencari anggota berdasarkan nomor anggota.
2.  Sistem mencari buku berdasarkan nomor induk.
3.  Sistem memastikan buku tersedia.
4.  Sistem memeriksa bahwa anggota belum mencapai batas tiga peminjaman
    aktif.
5.  Stok buku dikurangi satu.
6.  Sistem membuat transaksi dengan ID berurutan, misalnya `PJ-001`.
7.  Tanggal jatuh tempo ditentukan berdasarkan tanggal pinjam ditambah
    lama peminjaman.

Ketika pengembalian diproses, sistem memeriksa transaksi dan memastikan
buku belum dikembalikan. Setelah itu tanggal pengembalian disimpan, stok
buku ditambah satu, dan denda dihitung.

Tarif denda yang diterapkan:

  Jenis anggota             Denda per hari keterlambatan
  ----------------------- ------------------------------
  Mahasiswa                                      Rp1.000
  Dosen                                            Rp500
  Jenis anggota lainnya                              Rp0

Rumus perhitungan:

**Denda = jumlah hari keterlambatan × tarif denda per hari**

Jika buku dikembalikan pada atau sebelum tanggal jatuh tempo, denda
adalah Rp0.

## 7. Skenario Simulasi Program

Program utama pada kedua bahasa menjalankan skenario data contoh yang
sama:

-   Menambahkan tiga koleksi buku.
-   Menambahkan bab pada setiap buku.
-   Mendaftarkan dua anggota Mahasiswa dan satu anggota Dosen.
-   Memproses dua transaksi peminjaman dengan tanggal pinjam 20 Oktober
    2026 dan lama peminjaman 14 hari.
-   Mengembalikan transaksi pertama lima hari setelah tanggal jatuh
    tempo.
-   Menampilkan daftar koleksi buku, daftar transaksi, status
    pengembalian, serta rincian denda.

Untuk transaksi anggota Mahasiswa yang terlambat lima hari, denda yang
diharapkan adalah **5 × Rp1.000 = Rp5.000**. Transaksi kedua tetap
berstatus masih dipinjam dalam simulasi.

## 8. Implementasi Java dengan Maven

### Prasyarat

-   JDK 25 atau versi yang sesuai dengan konfigurasi
    `maven.compiler.release` pada `pom.xml`.
-   Apache Maven 3.9 atau lebih baru.

Periksa instalasi:

``` bash
java -version
mvn -version
```

### Langkah menjalankan

Dari direktori utama proyek:

``` bash
cd java
mvn clean compile
```

Jika kompilasi berhasil, jalankan aplikasi:

``` bash
mvn exec:java -Dexec.mainClass=id.ac.univpancasila.perpustakaan.App
```

Untuk melihat dependensi Maven:

``` bash
mvn dependency:tree
```

Konfigurasi Maven terdapat pada `java/pom.xml`. File tersebut menentukan
identitas proyek, versi Java, dependensi `ascii-table`, dan plugin untuk
menjalankan kelas utama.

## 9. Implementasi PHP dengan Composer

### Prasyarat

-   PHP 8.2 atau 8.4 sesuai batas versi pada `composer.json`.
-   Composer 2.x.

Periksa instalasi:

``` bash
php -v
composer --version
```

### Langkah menjalankan

Dari direktori utama proyek:

``` bash
cd php
composer install
php main.php
```

Composer membaca konfigurasi `composer.json` dan versi dependensi yang
dikunci dalam `composer.lock`, kemudian membangun autoloader di
`vendor/autoload.php`.

Jika terdapat kelas baru atau perubahan pemetaan autoload, jalankan:

``` bash
composer dump-autoload
```

Konfigurasi PSR-4 pada proyek ini memetakan namespace `Perpustakaan\` ke
direktori `src/`. Karena itu, namespace kelas harus konsisten dengan
struktur folder dan nama berkasnya.

## 10. Hasil dan Pengujian

Program menampilkan tiga bagian utama pada terminal:

1.  **Daftar koleksi buku**, meliputi kode buku, judul, penulis, stok,
    dan jumlah bab.
2.  **Daftar transaksi peminjaman**, meliputi ID transaksi, peminjam,
    jenis anggota, buku, tanggal pinjam, tanggal jatuh tempo, dan status
    transaksi.
3.  **Rincian denda keterlambatan**, meliputi identitas transaksi,
    tanggal jatuh tempo, tanggal kembali, jumlah hari terlambat, dan
    total denda.

Selain skenario normal tersebut, service juga memiliki validasi untuk
anggota tidak ditemukan, buku tidak ditemukan, stok habis, batas
peminjaman tercapai, transaksi tidak ditemukan, dan transaksi yang sudah
pernah dikembalikan.

## 11. Analisis Percobaan Package dan Namespace

Dokumen `percobaan.md` memuat pengujian ketika deklarasi namespace atau
package sengaja diubah tanpa memindahkan berkas sumber.

### PHP --- PSR-4

Ketika kelas yang berada di `src/Model/Buku.php` diubah namespace-nya
dari `Perpustakaan\Model` menjadi `Perpustakaan\Domain`, pemanggilan
`Perpustakaan\Model\Buku` tidak lagi menemukan kelas yang diharapkan.
Composer memetakan namespace berdasarkan aturan PSR-4 pada
`composer.json`.

**Perbaikan:** selaraskan kembali deklarasi namespace dengan lokasi
berkas, atau pindahkan berkas ke direktori yang sesuai dan sesuaikan
pemanggilnya.

### Java --- Package

Ketika deklarasi package pada `Buku.java` diubah menjadi package
`domain` sementara berkas tetap berada di folder `model`, kompilator
tidak menemukan kelas dengan nama lengkap
`id.ac.univpancasila.perpustakaan.model.Buku`.

**Perbaikan:** kembalikan deklarasi package agar sesuai dengan struktur
direktori atau pindahkan berkas ke direktori package yang benar. Setelah
perbaikan, lakukan kompilasi ulang:

``` bash
mvn clean compile
```

Percobaan ini menunjukkan bahwa konsistensi antara nama
package/namespace, struktur direktori, dan pernyataan import sangat
penting agar kelas dapat ditemukan.

## 12. Pengelolaan Repository Git

-   `java/target/` tidak perlu di-commit karena berisi hasil kompilasi
    dan artefak build Maven.
-   `php/vendor/` tidak perlu di-commit karena dependensi dapat dipasang
    kembali melalui `composer install`.
-   `composer.lock` sebaiknya di-commit agar versi dependensi yang
    dipasang konsisten antarlingkungan.
-   `pom.xml` dan `composer.json` harus disertakan karena merupakan
    konfigurasi utama build dan dependensi masing-masing proyek.

## 13. Kesimpulan

Melalui proyek ini, konsep PBO diterapkan dengan memisahkan entitas data
dan aturan bisnis ke dalam kelas-kelas yang memiliki tanggung jawab
jelas. Implementasi Java menunjukkan pengelolaan package dan dependensi
menggunakan Maven, sedangkan implementasi PHP menunjukkan penggunaan
namespace dan autoload PSR-4 melalui Composer.

Pengujian kesalahan pemetaan package/namespace memperlihatkan bahwa
struktur direktori dan deklarasi kelas harus konsisten. Maven dan
Composer membantu proses build serta pengelolaan dependensi sehingga
proyek lebih terstruktur dan mudah dijalankan kembali.

------------------------------------------------------------------------

**Catatan:** Aplikasi ini merupakan simulasi berbasis CLI dengan data
yang disimpan sementara di memori selama program berjalan. Program belum
menggunakan database maupun antarmuka web.
