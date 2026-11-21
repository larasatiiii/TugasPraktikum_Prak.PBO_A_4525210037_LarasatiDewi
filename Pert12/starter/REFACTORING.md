# REFACTORING.md

---

## Bagian 1 — Temuan pelanggaran SOLID

### Temuan 1

| | |
|---|---|
| Prinsip yang dilanggar | **Dependency Inversion Principle (DIP)** |
| Berkas dan baris | `LibraryService.java` (sebelum refactoring, baris 20–21) dan `LibraryService.php` (sebelum refactoring, baris 19–22) |
| Mengapa ini pelanggaran | `LibraryService` membuat langsung objek penyimpanan konkret di dalam kelas (`new Repository<>(Buku::getNomorInduk)` di Java dan `new KoleksiBuku()` di PHP), bukan bergantung pada abstraksi. |
| Akibatnya bila dibiarkan | Aturan bisnis terikat erat ke satu implementasi penyimpanan. Tidak bisa mengganti penyimpanan ke database atau menguji `LibraryService` dengan repository palsu/tiruan tanpa mengubah kode `LibraryService`. |

### Temuan 2

| | |
|---|---|
| Prinsip yang dilanggar | **Open-Closed Principle (OCP)** |
| Berkas dan baris | `LibraryService.java` / `LibraryService.php` (sebelum memakai `AturanDenda`) |
| Mengapa ini pelanggaran | Penentuan aturan atau tarif denda per jenis anggota (mahasiswa, dosen, kehormatan) jika ditulis sebagai rantai `if/else` di dalam `LibraryService` mengharuskan kita menyunting `LibraryService` setiap kali ada jenis anggota baru. |
| Akibatnya bila dibiarkan | Setiap penambahan jenis anggota baru berisiko merusak logika peminjaman/denda yang sudah berjalan di `LibraryService`. |

### Temuan 3

| | |
|---|---|
| Prinsip yang dilanggar | **Single Responsibility Principle (SRP)** |
| Berkas dan baris | `PerpustakaanService.java` / `PerpustakaanService.php` & `LibraryService` |
| Mengapa ini pelanggaran | `LibraryService` sebelumnya mengurus struktur penyimpanan internal (`HashMap`/`Repository` konkret) sekaligus aturan bisnis peminjaman, sementara `PerpustakaanService` mengurus pembacaan input `Scanner` sekaligus pemformatan output konsol. |
| Akibatnya bila dibiarkan | Perubahan format tampilan atau cara penyimpanan memaksa perubahan pada kelas yang seharusnya fokus pada satu tanggung jawab saja. |

---

## Bagian 2 — Tabel sebelum dan sesudah

| Aspek | Sebelum | Sesudah | Alasan perubahan |
|---|---|---|---|
| Penyimpanan data | `LibraryService` membuat `new Repository<>(...)` / `new KoleksiBuku()` sendiri | `LibraryService` menerima interface `BookRepository` lewat constructor; implementasinya di `InMemoryBookRepository` | Mematuhi DIP dan memisahkan penyimpanan dari aturan bisnis |
| Aturan denda | Belum terabstraksi / berisiko memakai rantai `if` di service | Interface `AturanDenda` dengan `DendaMahasiswa`, `DendaDosen`, `DendaBebas`, `DendaAlumni`, dan `AturanDendaFactory` | Mematuhi OCP sehingga jenis anggota baru cukup menambah kelas baru |
| Perakitan objek | Tersebar di dalam `LibraryService` | Terpusat di `Main.java` (Java) dan `main.php` (PHP) lewat constructor injection manual | Hanya titik masuk program yang mengetahui kelas konkret |
| Kemampuan diuji | Sulit menguji skenario kegagalan penyimpanan tanpa mengubah `LibraryService` | Dapat diuji dengan `InMemoryBookRepository` dan repository palsu (`FailingBookRepository`) di `LibraryServiceTest` | Membuktikan aturan bisnis terpisah dari detail penyimpanan |

---

## Bagian 3 — Bukti Open-Closed

Setelah menambahkan aturan denda keempat (`DendaAlumni`):

- Berkas **baru** yang ditambahkan: `DendaAlumni.java` (Java) dan `DendaAlumni.php` (PHP)
- Berkas **lama** yang isinya berubah: `AturanDendaFactory.java` (Java) dan `AturanDendaFactory.php` (PHP) — masing-masing hanya menambah satu cabang `case "alumni"` / `'alumni'`
- Apakah `LibraryService` ikut disunting? **Tidak sama sekali.**

---

## Bagian 4 — Bukti Dependency Inversion

Bila penyimpanan diganti dari memori (`InMemoryBookRepository`) ke basis data:

- Berkas **baru** yang perlu ditulis: 1 berkas implementasi baru, misalnya `DatabaseBookRepository.java` / `DatabaseBookRepository.php` yang mengimplementasikan `BookRepository`
- Berkas **lama** yang isinya berubah: 1 baris perakitan di `Main.java` / `main.php` (mengganti `new InMemoryBookRepository()` menjadi `new DatabaseBookRepository(...)`); `LibraryService` tidak berubah sama sekali.

---

## Bagian 5 — Yang sengaja TIDAK diberi pattern

| | |
|---|---|
| Bagian kode | Pembuatan objek `Buku` di dalam `LibraryService.tambahBuku(...)` serta model `Buku`, `Anggota`, dan `Jurusan` |
| Pattern yang sempat dipertimbangkan | Factory / Builder Pattern untuk konstruksi `Buku` |
| Alasan tidak dipakai | Constructor `Buku(isbn, judul, penulis, eksemplar)` sudah sederhana, hanya memiliki empat parameter yang jelas, dan tidak memiliki varian subkelas yang berbeda cara pembuatannya. Menambahkan Factory hanya menambah lapisan kode tanpa manfaat nyata. |
