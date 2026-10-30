# Laporan Percobaan Sesi 9: Package, Namespace, dan Build Tool

Dokumen ini memuat catatan hasil eksperimen pembuktian kegagalan pemetaan namespace/package, langkah perbaikannya, serta analisis pengelolaan repositori Git untuk proyek berbasis Maven dan Composer.

---

## 1. Percobaan PHP: Merusak Pemetaan Autoload PSR-4

### Tindakan yang Dilakukan
Pada berkas `src/Model/Buku.php`, baris deklarasi namespace diubah dari:
```php
namespace Perpustakaan\Model;
```
Menjadi:
```php
namespace Perpustakaan\Domain;
```
**tanpa memindahkan berkas fisik tersebut** dari foldernya (`src/Model/Buku.php`).

Program kemudian dijalankan melalui terminal:
```bash
php main.php
```

### Pesan Kesalahan Asli (Terminal Output)
```text
PHP Fatal error:  Uncaught Error: Class "Perpustakaan\Model\Buku" not found in D:\FilePerkuliahan\AsprakPBO-A\modul praktikum\modul\09-package-namespace-dan-build-tool\done\php\main.php:23
Stack trace:
#0 {main}
  thrown in D:\FilePerkuliahan\AsprakPBO-A\modul praktikum\modul\09-package-namespace-dan-build-tool\done\php\main.php on line 23
```

### Analisis Penyebab
1. Berdasarkan konfigurasi PSR-4 pada `composer.json` (`"Perpustakaan\\": "src/"`), ketika kode di `main.php` memanggil `new Perpustakaan\Model\Buku()`, autoloader Composer secara deterministik memetakan prefix namespace tersebut ke jalur berkas:
   $$\text{Perpustakaan\Model\Buku} \longrightarrow \texttt{src/Model/Buku.php}$$
2. Berkas `src/Model/Buku.php` memang ditemukan dan dimuat oleh PHP runtime, namun isi kelas di dalamnya dideklarasikan berada di bawah namespace `Perpustakaan\Domain`.
3. Akibatnya, kelas dengan FQCN (*Fully Qualified Class Name*) `Perpustakaan\Model\Buku` tetap tidak terdefinisi dalam runtime symbol table PHP, memicu `Fatal Error: Class not found`.
4. Jika pemanggil di `main.php` diubah menjadi `new Perpustakaan\Domain\Buku()`, Composer akan mencari `src/Domain/Buku.php` yang pada kenyataannya tidak ada di disk.

### Langkah Perbaikan
Terdapat dua opsi perbaikan yang valid:
- **Opsi A (Jika kelas memang merupakan bagian dari domain):**  
  Pindahkan berkas fisik ke folder yang sesuai dengan namespace-nya, yaitu `src/Domain/Buku.php`, lalu sesuaikan impor pemanggil: `use Perpustakaan\Domain\Buku;`.
- **Opsi B (Menyelaraskan kembali deklarasi namespace):**  
  Kembalikan baris deklarasi namespace di `src/Model/Buku.php` menjadi:
  ```php
  namespace Perpustakaan\Model;
  ```
  Setelah dikembalikan, jalankan kembali `php main.php` dan seluruh kelas berhasil di-*load* tanpa error.

---

## 2. Percobaan Java: Merusak Pemetaan Package

### Tindakan yang Dilakukan
Pada berkas `src/main/java/id/ac/univpancasila/perpustakaan/model/Buku.java`, baris deklarasi package diubah dari:
```java
package id.ac.univpancasila.perpustakaan.model;
```
Menjadi:
```java
package id.ac.univpancasila.perpustakaan.domain;
```
**tanpa memindahkan berkas fisik tersebut** dari foldernya (`.../perpustakaan/model/Buku.java`).

Proyek kemudian dikompilasi menggunakan Maven:
```bash
mvn compile
```

### Pesan Kesalahan Asli Kompilator (Javac Output)
```text
[ERROR] COMPILATION ERROR : 
[INFO] -------------------------------------------------------------
[ERROR] /D:/FilePerkuliahan/AsprakPBO-A/modul praktikum/modul/09-package-namespace-dan-build-tool/done/java/src/main/java/id/ac/univpancasila/perpustakaan/App.java:[5,46] cannot access id.ac.univpancasila.perpustakaan.model.Buku
  bad source file: D:\FilePerkuliahan\AsprakPBO-A\modul praktikum\modul\09-package-namespace-dan-build-tool\done\java\src\main\java\id\ac\univpancasila\perpustakaan\model\Buku.java
    file does not contain class id.ac.univpancasila.perpustakaan.model.Buku
    Please remove or make sure it appears in the correct subdirectory of the sourcepath.
[ERROR] /D:/FilePerkuliahan/AsprakPBO-A/modul praktikum/modul/09-package-namespace-dan-build-tool/done/java/src/main/java/id/ac/univpancasila/perpustakaan/service/PerpustakaanService.java:[18,24] cannot find symbol
  symbol:   class Buku
  location: class id.ac.univpancasila.perpustakaan.service.PerpustakaanService
```

### Analisis Penyebab
1. Dalam spesifikasi bahasa Java (*Java Language Specification* / JLS), hubungan antara struktur folder dan baris `package` adalah **aturan sintaksis dan kompilasi mutlak**, bukan sekadar konvensi.
2. Ketika berkas `App.java` mengimpor `id.ac.univpancasila.perpustakaan.model.Buku`, kompilator `javac` mencari berkas pada jalur `id/ac/univpancasila/perpustakaan/model/Buku.java`.
3. `javac` menemukan berkas tersebut, namun membaca deklarasi package di dalamnya adalah `id.ac.univpancasila.perpustakaan.domain`.
4. Kompilator langsung menolak dengan diagnosa:
   `bad source file: ... file does not contain class id.ac.univpancasila.perpustakaan.model.Buku. Please remove or make sure it appears in the correct subdirectory of the sourcepath.`
5. Kegagalan ini merembet ke seluruh kelas konsumen (`App.java` dan `PerpustakaanService.java`) yang memicu rentetan error `cannot find symbol: class Buku`.

### Langkah Perbaikan
- Kembalikan deklarasi baris pertama pada `Buku.java` agar sesuai dengan jalur direktori fisiknya:
  ```java
  package id.ac.univpancasila.perpustakaan.model;
  ```
- Lakukan kompilasi ulang:
  ```bash
  mvn clean compile
  ```
- Hasil: `BUILD SUCCESS` (seluruh 6 berkas sumber terkompilasi ke dalam `target/classes`).

---

## 3. Analisis Pengelolaan Repositori Git

### Mengapa `target/` dan `vendor/` DILARANG Di-commit?
1. **Artefak Hasil Build / Kompilasi (*Derived Artifacts*):**  
   Folder `target/` pada Maven dan `vendor/` pada Composer adalah luaran yang dapat digenerasi ulang kapan saja dengan perintah `mvn compile` atau `composer install`. Menyimpan hasil build ke Git melanggar prinsip *Single Source of Truth*.
2. **Kerapuhan Lintas Platform (*Platform Incompatibility*):**  
   Beberapa dependensi atau paket pihak ketiga mengompilasi biner C/C++ atau membuat symlink yang spesifik pada OS tertentu (misal Windows vs Linux/macOS). Mengikutsertakan direktori ini ke Git dapat merusak proyek saat di-*clone* pada OS yang berbeda.
3. **Ukuran dan Performa Repositori:**  
   Folder `vendor/` dan `target/` memuat ribuan hingga puluhan ribu berkas kecil. Hal ini menyebabkan ukuran riwayat `.git` membengkak drastis dan memperlambat operasi `git clone`, `git fetch`, dan `git status`.

### Mengapa `composer.lock` WAJIB Di-commit?
1. **Jaminan Determinisme Versi (*Reproducible Builds*):**  
   Berkas `composer.json` biasanya menggunakan operator versi semantik terbuka seperti `^8.2` atau `~2.0`. Jika hanya `composer.json` yang di-commit, developer A yang menjalankan `composer install` hari ini bisa mendapatkan paket versi `2.1.0`, sedangkan developer B yang menjalankan perintah yang sama sebulan kemudian bisa memperoleh versi `2.1.8` yang mungkin mengandung bug regresi.
2. **Penguncian Commit Hash & Tree Hash:**  
   `composer.lock` merekam versi eksak, commit hash Git, dan hash integritas dari paket utama maupun dependensi transitifnya. Dengan men-commit `composer.lock`, seluruh anggota tim, mesin CI/CD, dan server produksi dijamin menjalankan bytecode yang 100% identik.
