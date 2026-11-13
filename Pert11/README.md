# Analisis Before & After — Praktikum Sesi 11 (Collections, Generics, dan Unit Test Pertama)

Dokumen ini membandingkan kode **sebelum** (kerangka `starter` dengan komentar `TODO`) dan **sesudah** (TODO sudah dikerjakan) pada folder `java` dan `php`.

## 1. Ringkasan

| Area | Berkas | TODO | Status awal | Yang dikerjakan |
|---|---|---|---|---|
| Java | `Repository.java` | 4 | Method kosong / mengembalikan nilai palsu | Implementasi `simpan`, `cariById`, `cariSemua`, `hapus` |
| Java | `RepositoryTest.java` | 6 | Semua uji berisi `fail("TODO ...")` | 5 uji dilengkapi + 6 uji tambahan (total 11) |
| Java | 5 berkas `exception/*.java` | 9 komentar | **Kode sudah lengkap** dari sesi 10, hanya komentar TODO yang tertinggal | Komentar TODO dirapikan menjadi keterangan |
| Java | `pom.xml` | – | Belum ada JUnit | Tambah `junit-jupiter` (scope test) dan `maven-surefire-plugin` |
| PHP | `KoleksiBuku.php` | 6 | Method kosong / mengembalikan nilai palsu | Implementasi `simpan`, `cariByIsbn`, `filter`, `map`, `jumlahkan`, `kelompokkan` |
| PHP | `KoleksiBukuTest.php` | 6 | Semua uji berisi `$this->fail('TODO ...')` | 5 uji dilengkapi + 7 uji tambahan (total 12) |
| PHP | 4 berkas `src/Exception/*.php` | 5 komentar | **Kode sudah lengkap**, hanya komentar TODO yang tertinggal | Komentar TODO dirapikan |
| PHP | `composer.json` | – | `autoload-dev` kosong | Pemetaan `Perpustakaan\Tests\` → `tests/` |

### Perubahan lokasi berkas

Sesuai instruksi modul (Maven dan PHPUnit hanya menemukan uji di lokasi standar), berkas starter dipindahkan:

| Sebelum | Sesudah |
|---|---|
| `java/Repository.java` | `java/src/main/java/id/ac/univpancasila/perpustakaan/Repository.java` |
| `java/RepositoryTest.java` | `java/src/test/java/id/ac/univpancasila/perpustakaan/RepositoryTest.java` |
| `php/KoleksiBuku.php` | `php/src/Model/KoleksiBuku.php` |
| `php/KoleksiBukuTest.php` | `php/tests/KoleksiBukuTest.php` |

---

## 2. Java

### 2.1 `Repository.java` — kelas generik `Repository<T>`

Kerangka kelas sudah menyediakan `Map<String, T> data` (bertipe `LinkedHashMap`, sehingga urutan sisip terjaga) dan `Function<T, String> pengambilKunci`. Dengan fungsi pengambil kunci itu, `Repository` tidak perlu tahu apa pun tentang `T`.

#### TODO 1 — `simpan(T item)`

**Before**

```java
/** TODO 1: simpan item, dengan kunci diambil lewat pengambilKunci. */
public void simpan(T item) {
}
```

**After**

```java
/** Simpan item, dengan kunci diambil lewat pengambilKunci. Kunci yang sama menimpa item lama. */
public void simpan(T item) {
    data.put(pengambilKunci.apply(item), item);
}
```


**Analisis:** sebelumnya method kosong, sehingga `simpan()` tidak menyimpan apa pun dan semua pencarian gagal. Sekarang kunci dihitung lewat `pengambilKunci.apply(item)` lalu dipakai pada `Map.put`. Karena kunci unik, menyimpan dengan kunci yang sama **menimpa** item lama (perilaku ini diuji pada `simpanKunciSamaMenimpa`). Pencarian menjadi O(1) lewat hash, bukan perulangan linear — inilah jawaban pertanyaan demo nomor 1.

#### TODO 2 — `cariById(String id)`

**Before**

```java
/**
 * TODO 2: kembalikan Optional yang berisi item bila ada, kosong bila tidak.
 *         JANGAN mengembalikan null.
 *         Petunjuk: Optional.ofNullable(...)
 */
public Optional<T> cariById(String id) {
    return Optional.empty();   // ganti
}
```

**After**

```java
/**
 * Kembalikan Optional yang berisi item bila ada, kosong bila tidak.
 * Tidak pernah mengembalikan null.
 */
public Optional<T> cariById(String id) {
    return Optional.ofNullable(data.get(id));
}
```


**Analisis:** versi awal selalu mengembalikan `Optional.empty()` (nilai palsu). `Optional.ofNullable(data.get(id))` membungkus hasil `Map.get` yang bisa `null`: item ada → `Optional` berisi, tidak ada → `Optional` kosong. Pemanggil dipaksa menangani kasus "tidak ada" secara eksplisit, sehingga risiko `NullPointerException` hilang (pertanyaan demo nomor 3).

#### TODO 3 — `cariSemua()`

**Before**

```java
/**
 * TODO 3: kembalikan SALINAN TAK-BERUBAH dari seluruh isi.
 *         Pelajaran sesi 7 tetap berlaku: koleksi internal tidak boleh bocor.
 *         Petunjuk: List.copyOf(...)
 */
public List<T> cariSemua() {
    return List.of();   // ganti
}
```

**After**

```java
/**
 * Kembalikan SALINAN TAK-BERUBAH dari seluruh isi.
 * Pelajaran sesi 7 tetap berlaku: koleksi internal tidak boleh bocor.
 */
public List<T> cariSemua() {
    return List.copyOf(data.values());
}
```


**Analisis:** awalnya selalu mengembalikan daftar kosong. `List.copyOf(data.values())` membuat **salinan tak-berubah**: pemanggil tidak bisa memodifikasi isi internal repository (`add`/`remove` akan melempar `UnsupportedOperationException`). Ini menerapkan prinsip enkapsulasi dari sesi 7 dan diuji oleh `koleksiTidakBocor`.

#### TODO 4 — `hapus(String id)`

**Before**

```java
/** TODO 4: hapus item; kembalikan true bila ada yang terhapus. */
public boolean hapus(String id) {
    return false;   // ganti
}
```

**After**

```java
/** Hapus item; kembalikan true bila ada yang terhapus. */
public boolean hapus(String id) {
    return data.remove(id) != null;
}
```


**Analisis:** awalnya selalu `false`, jadi item tidak pernah benar-benar terhapus. `Map.remove(id)` mengembalikan nilai lama atau `null` bila kunci tidak ada, sehingga `!= null` langsung menjadi jawaban "apakah ada yang terhapus" tanpa perlu `containsKey` terpisah.

> **Checkpoint modul:** `Repository<Buku>` menolak objek selain `Buku` pada tahap **kompilasi**. Ini dijamin oleh type parameter `<T>` — `repo.simpan(new Anggota(...))` pada `Repository<Buku>` tidak akan dikompilasi (pertanyaan demo nomor 2).

### 2.2 `RepositoryTest.java` — JUnit 5

Setiap uji berjalan setelah `@BeforeEach siapkan()` membuat `Repository` baru, sehingga uji tidak saling memengaruhi.

#### TODO 1 — `simpanLaluTemukan`

**Before**

```java
@Test
@DisplayName("Item yang disimpan dapat ditemukan kembali")
void simpanLaluTemukan() {
    repo.simpan(new Buku("978-1", "PBO"));

    // TODO 1: pastikan cariById("978-1") berisi nilai,
    //         dan judulnya "PBO".
    //         Petunjuk: assertTrue(...isPresent()) dan assertEquals(...)
    fail("TODO 1 belum dikerjakan");
}
```

**After**

```java
@Test
@DisplayName("Item yang disimpan dapat ditemukan kembali")
void simpanLaluTemukan() {
    repo.simpan(new Buku("978-1", "PBO"));

    Optional<Buku> hasil = repo.cariById("978-1");

    assertTrue(hasil.isPresent());
    assertEquals("PBO", hasil.get().judul());
}
```


**Analisis:** sebelumnya uji pasti gagal karena `fail(...)`. Kini dibuktikan dua hal: nilainya **ada** (`isPresent`) dan **isinya benar** (`judul` = "PBO"). Memeriksa isi, bukan sekadar "tidak null", menghindari uji yang terlalu longgar.

#### TODO 2 — `cariYangTidakAda`

**Before**

```java
@Test
@DisplayName("Mencari id yang tidak ada mengembalikan Optional kosong")
void cariYangTidakAda() {
    // TODO 2
    fail("TODO 2 belum dikerjakan");
}
```

**After**

```java
@Test
@DisplayName("Mencari id yang tidak ada mengembalikan Optional kosong")
void cariYangTidakAda() {
    assertTrue(repo.cariById("999").isEmpty());
}
```


#### TODO 3 — `hapusItem`

**Before**

```java
@Test
@DisplayName("Item yang dihapus tidak dapat ditemukan lagi")
void hapusItem() {
    // TODO 3
    fail("TODO 3 belum dikerjakan");
}
```

**After**

```java
@Test
@DisplayName("Item yang dihapus tidak dapat ditemukan lagi")
void hapusItem() {
    repo.simpan(new Buku("978-1", "PBO"));

    assertTrue(repo.hapus("978-1"));
    assertTrue(repo.cariById("978-1").isEmpty());
    assertEquals(0, repo.jumlah());
}
```


**Analisis:** tiga pemeriksaan sekaligus — `hapus` mengembalikan `true`, item tidak lagi ditemukan, dan jumlah kembali 0.

#### TODO 4 — `repositoryKosong`

**Before**

```java
@Test
@DisplayName("Repository baru berisi nol item")
void repositoryKosong() {
    // TODO 4
    fail("TODO 4 belum dikerjakan");
}
```

**After**

```java
@Test
@DisplayName("Repository baru berisi nol item")
void repositoryKosong() {
    assertEquals(0, repo.jumlah());
    assertTrue(repo.cariSemua().isEmpty());
}
```


#### TODO 5 — `koleksiTidakBocor`

**Before**

```java
@Test
@DisplayName("Koleksi yang dikembalikan cariSemua tidak dapat diubah dari luar")
void koleksiTidakBocor() {
    repo.simpan(new Buku("978-1", "PBO"));

    // TODO 5: pastikan memanggil .add(...) pada hasil cariSemua()
    //         melemparkan UnsupportedOperationException.
    //         Petunjuk: assertThrows(UnsupportedOperationException.class, () -> ...)
    fail("TODO 5 belum dikerjakan");
}
```

**After**

```java
@Test
@DisplayName("Koleksi yang dikembalikan cariSemua tidak dapat diubah dari luar")
void koleksiTidakBocor() {
    repo.simpan(new Buku("978-1", "PBO"));

    List<Buku> salinan = repo.cariSemua();

    assertThrows(UnsupportedOperationException.class,
            () -> salinan.add(new Buku("978-2", "Basis Data")));
}
```


**Analisis:** `assertThrows` membuktikan bahwa menambah item ke hasil `cariSemua()` ditolak. Uji ini hanya lulus jika TODO 3 memakai `List.copyOf`; bila memakai `new ArrayList<>(...)`, uji ini akan gagal — itulah gunanya uji.

#### TODO 6 — minimal tiga uji tambahan (dikerjakan: 6)

**Before**

```java
// TODO 6: tambahkan minimal TIGA pengujian lagi dari daftar skenario
//         yang Anda susun pada sesi 10, termasuk satu yang memastikan
//         exception yang benar dilemparkan.
```

**After** — salah satunya, uji exception untuk skenario "ISBN tidak terdaftar":


```java
@Test
@DisplayName("pinjam() melempar BukuTidakDitemukanException untuk ISBN yang tidak terdaftar")
void pinjamIsbnTidakTerdaftar() {
    LibraryService layanan = new LibraryService();
    layanan.daftarkanAnggota("A1");

    BukuTidakDitemukanException e = assertThrows(
            BukuTidakDitemukanException.class,
            () -> layanan.pinjam("A1", "000"));

    assertEquals("000", e.getIsbn());
}
```


Keenam uji tambahan:

| Uji | Yang dibuktikan |
|---|---|
| `simpanKunciSamaMenimpa` | Kunci sama menimpa item lama (jumlah tetap 1) |
| `hapusYangTidakAda` | `hapus` id yang tidak ada mengembalikan `false` |
| `pinjamBerhasilMengurangiStok` | Peminjaman sah mengurangi stok |
| `pinjamIsbnTidakTerdaftar` | Melempar `BukuTidakDitemukanException` dan `getIsbn()` benar |
| `pinjamMelewatiBatas` | Melempar `BatasPinjamTerlampauiException` pada pinjaman ke-4, `getBatas()` = 3 |
| `pinjamBukuHabis` | Melempar `BukuSedangDipinjamException` dan `getJudul()` benar |

Total 5 + 6 = **11 uji** (checkpoint modul: minimal 8).

### 2.3 `exception/*.java` — komentar TODO

Berbeda dengan berkas lain, kelima exception ini **sudah berisi kode yang benar** dari sesi 10 (atribut `final`, `super(...)` dengan pesan, getter). Yang tertinggal hanya komentar `TODO` yang menyesatkan karena seolah-olah belum dikerjakan. Contoh `BukuTidakDitemukanException`:

**Before**

```java
// TODO 1: deklarasikan atribut privat final untuk isbn.
private final String isbn;

public BukuTidakDitemukanException(String isbn) {
    // TODO 2: panggil super(...) dengan pesan yang menyebut ISBN-nya,
    // misalnya: "Buku dengan ISBN 978-1 tidak ditemukan"
    super("Buku dengan ISBN " + isbn + " tidak ditemukan");
    // TODO 3: simpan isbn ke atribut.
    this.isbn = isbn;
}

// TODO 4: sediakan getter getIsbn().
public String getIsbn() {
    return this.isbn;
}
```

**After**

```java
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
```


Pada `PerpustakaanException`, TODO 1 meminta keputusan *checked* atau *unchecked*. Induknya sudah `extends RuntimeException`; yang ditambahkan adalah alasannya:

**Before**

```java
 * TODO 1: putuskan — turunkan dari RuntimeException (unchecked) atau dari
 * Exception (checked)? Tuliskan alasannya di skenario-uji.md.
 * Petunjuk: kalau kegagalannya menandakan permintaan yang tidak sah
 * dan bukan keadaan luar yang wajar, unchecked biasanya lebih tepat.
 */
```

**After**

```java
 * KEPUTUSAN: diturunkan dari RuntimeException (unchecked). Kegagalan domain
 * di sini menandakan permintaan yang tidak sah (ISBN salah, batas terlampaui),
 * bukan keadaan luar yang wajar, sehingga pemanggil tidak dipaksa menulis
 * try/catch di setiap tempat.
 */
```


### 2.4 `pom.xml`

**Before:** hanya `ascii-table` di `<dependencies>` dan `exec-maven-plugin` di `<plugins>`; `mvn test` tidak bisa menemukan JUnit.

**After:**


```xml
<!--
  Sesi 11 — JUnit 5. scope=test: hanya tersedia saat pengujian dan
  TIDAK ikut masuk ke berkas .jar hasil produksi.
-->
<dependency>
  <groupId>org.junit.jupiter</groupId>
  <artifactId>junit-jupiter</artifactId>
  <version>5.11.3</version>
  <scope>test</scope>
</dependency>
```

```xml
<!-- Sesi 11 — menjalankan pengujian lewat: mvn test -->
<plugin>
  <groupId>org.apache.maven.plugins</groupId>
  <artifactId>maven-surefire-plugin</artifactId>
  <version>3.5.2</version>
</plugin>
```


`scope=test` membuat JUnit hanya tersedia saat pengujian dan tidak ikut masuk ke `.jar` produksi.

---

## 3. PHP

PHP tidak punya generics. `KoleksiBuku` memakai pendekatan *typed collection*: tipe parameter `simpan(Buku $b)` menolak objek selain `Buku` (dengan `declare(strict_types=1)` akan melempar `TypeError`).

### 3.1 `KoleksiBuku.php`

#### TODO 1 — `simpan(Buku $b)`

**Before**

```php
/** TODO 1: simpan buku dengan kunci ISBN-nya. */
public function simpan(Buku $b): void
{
}
```

**After**

```php
/** Simpan buku dengan kunci ISBN-nya. ISBN yang sama menimpa buku lama. */
public function simpan(Buku $b): void
{
    $this->items[$b->getNomorInduk()] = $b;
}
```


**Analisis:** item disimpan dengan kunci `getNomorInduk()` (ISBN), sehingga `items` menjadi array asosiatif `isbn => Buku` — padanan `Map` di Java.

#### TODO 2 — `cariByIsbn(string $isbn): ?Buku`

**Before**

```php
/** TODO 2: kembalikan Buku atau null. Perhatikan tipe kembaliannya: ?Buku */
public function cariByIsbn(string $isbn): ?Buku
{
    return null;   // ganti
}
```

**After**

```php
/** Kembalikan Buku atau null. Perhatikan tipe kembaliannya: ?Buku */
public function cariByIsbn(string $isbn): ?Buku
{
    return $this->items[$isbn] ?? null;
}
```


**Analisis:** operator null-coalescing `??` mengembalikan elemen bila ada dan `null` bila kunci tidak ada, tanpa memicu *warning* "undefined array key". Tipe kembalian `?Buku` menyatakan secara eksplisit bahwa hasilnya bisa `null` — padanan `Optional` di Java.

#### TODO 3 — `filter(callable $predikat)`

**Before**

```php
/**
 * TODO 3: kembalikan KoleksiBuku baru berisi item yang lolos predikat.
 *         PERHATIAN: array_filter(array, callable) — array DULU.
 *         Jangan lupa array_values agar indeksnya tidak berlubang.
 */
public function filter(callable $predikat): self
{
    return new self();   // ganti
}
```

**After**

```php
/**
 * Kembalikan KoleksiBuku baru berisi item yang lolos predikat.
 * PERHATIAN: array_filter(array, callable) — array DULU.
 * array_values dipakai agar indeksnya tidak berlubang.
 */
public function filter(callable $predikat): self
{
    return new self(array_values(array_filter($this->items, $predikat)));
}
```


**Analisis:** `array_filter` mempertahankan kunci asli, sehingga hasilnya berindeks berlubang (misalnya `[0, 2]`) dan `json_encode` akan menghasilkan **objek**, bukan larik. `array_values` merapikan indeks. Hasilnya dibungkus `new self(...)` agar tetap bertipe `KoleksiBuku` dan bisa dirantai.

#### TODO 4 — `map(callable $f)`

**Before**

```php
/**
 * TODO 4: kembalikan larik hasil pemetaan.
 *         PERHATIAN: array_map(callable, array) — callable DULU.
 *         Urutan argumennya BERBEDA dari array_filter. Ini sumber
 *         kesalahan yang sangat sering terjadi.
 *
 * @return mixed[]
 */
public function map(callable $f): array
{
    return [];   // ganti
}
```

**After**

```php
/**
 * Kembalikan larik hasil pemetaan.
 * PERHATIAN: array_map(callable, array) — callable DULU.
 * Urutan argumennya BERBEDA dari array_filter. Ini sumber
 * kesalahan yang sangat sering terjadi.
 *
 * @return mixed[]
 */
public function map(callable $f): array
{
    return array_map($f, array_values($this->items));
}
```


**Analisis:** perhatikan urutan argumen: `array_map(callable, array)` — callable **dulu**, kebalikan dari `array_filter(array, callable)`. Ini jawaban pertanyaan demo nomor 7. `array_values` memastikan hasilnya larik berindeks 0..n-1, bukan berkunci ISBN.

#### TODO 5 — `jumlahkan(callable $f)`

**Before**

```php
/** TODO 5: jumlahkan hasil $f atas seluruh item. Petunjuk: array_reduce */
public function jumlahkan(callable $f): float
{
    return 0.0;   // ganti
}
```

**After**

```php
/** Jumlahkan hasil $f atas seluruh item. */
public function jumlahkan(callable $f): float
{
    return array_reduce(
        $this->items,
        fn (float $total, Buku $b): float => $total + $f($b),
        0.0
    );
}
```


**Analisis:** `array_reduce` melipat seluruh item menjadi satu angka. Nilai awal `0.0` memastikan koleksi kosong menghasilkan `0.0` (bukan `null`), sesuai tipe kembalian `float`.

#### TODO 6 — `kelompokkan(callable $kunci)`

**Before**

```php
/**
 * TODO 6: kelompokkan item berdasarkan kunci hasil $kunci($buku).
 * @return array<string,Buku[]>
 */
public function kelompokkan(callable $kunci): array
{
    return [];   // ganti
}
```

**After**

```php
/**
 * Kelompokkan item berdasarkan kunci hasil $kunci($buku).
 * @return array<string,Buku[]>
 */
public function kelompokkan(callable $kunci): array
{
    $hasil = [];
    foreach ($this->items as $b) {
        $hasil[(string) $kunci($b)][] = $b;
    }
    return $hasil;
}
```


**Analisis:** `$hasil[kunci][] = $b` otomatis membuat larik baru pada kunci yang belum ada, lalu menambahkan item. Padanannya `Collectors.groupingBy` di Java. Kunci di-cast ke `string` agar sesuai anotasi `array<string,Buku[]>`.

### 3.2 `KoleksiBukuTest.php` — PHPUnit

#### TODO 1–5

**Before**

```php
public function testItemYangDisimpanDapatDitemukanKembali(): void
{
    // TODO 1
    $this->fail('TODO 1 belum dikerjakan');
}

public function testMencariIsbnYangTidakAdaMengembalikanNull(): void
{
    // TODO 2
    $this->fail('TODO 2 belum dikerjakan');
}

public function testKoleksiBaruBerisiNolItem(): void
{
    // TODO 3: perhatikan bahwa count($this->koleksi) bekerja
    //         karena kelasnya implements Countable.
    $this->fail('TODO 3 belum dikerjakan');
}

public function testFilterHanyaMengembalikanYangLolosPredikat(): void
{
    // TODO 4
    $this->fail('TODO 4 belum dikerjakan');
}

public function testJumlahkanMenghitungTotalDenganBenar(): void
{
    // TODO 5
    $this->fail('TODO 5 belum dikerjakan');
}
```

**After**

```php
public function testItemYangDisimpanDapatDitemukanKembali(): void
{
    $this->koleksi->simpan(new Buku('978-1', 'PBO', 'Budi', 2));

    $hasil = $this->koleksi->cariByIsbn('978-1');

    $this->assertNotNull($hasil);
    $this->assertSame('PBO', $hasil->getJudul());
}

public function testMencariIsbnYangTidakAdaMengembalikanNull(): void
{
    $this->assertNull($this->koleksi->cariByIsbn('999'));
}

public function testKoleksiBaruBerisiNolItem(): void
{
    // count() bekerja karena kelasnya implements Countable.
    $this->assertCount(0, $this->koleksi);
    $this->assertSame(0, count($this->koleksi));
}

public function testFilterHanyaMengembalikanYangLolosPredikat(): void
{
    $this->isiTigaBuku();

    $tersedia = $this->koleksi->filter(fn (Buku $b): bool => $b->tersedia());

    $this->assertCount(2, $tersedia);
    $this->assertSame(
        ['978-1', '978-3'],
        array_map(fn (Buku $b): string => $b->getNomorInduk(), $tersedia->semua())
    );
}

public function testJumlahkanMenghitungTotalDenganBenar(): void
{
    $this->isiTigaBuku();

    $total = $this->koleksi->jumlahkan(fn (Buku $b): float => $b->getJumlahEksemplar());

    $this->assertSame(3.0, $total);
}
```


**Analisis:**

- `assertSame('PBO', $hasil->getJudul())` memakai perbandingan ketat (nilai **dan** tipe), lebih ketat daripada `assertEquals`.
- `assertCount(0, $this->koleksi)` bisa dipakai pada objek karena `KoleksiBuku implements Countable`.
- Pada uji `filter`, yang dibandingkan adalah daftar ISBN yang benar-benar lolos (`['978-1', '978-3']`), bukan hanya jumlahnya, sehingga uji tidak bisa lulus secara kebetulan.
- Uji `jumlahkan` membandingkan dengan `3.0` (float), sesuai tipe kembalian.

#### TODO 6 — minimal lima skenario (dikerjakan: 7), termasuk uji exception

**Before**

```php
// TODO 6: terjemahkan minimal LIMA skenario dari skenario-uji.md sesi 10
//         menjadi kasus uji di sini, termasuk satu yang memastikan
//         exception yang benar dilemparkan.
//         Petunjuk: $this->expectException(BukuTidakDitemukanException::class);
```

**After** — contoh uji exception:


```php
public function testPinjamIsbnTidakTerdaftarMelemparBukuTidakDitemukan(): void
{
    $layanan = new LibraryService();
    $layanan->daftarkanAnggota('A1');

    $this->expectException(BukuTidakDitemukanException::class);
    $layanan->pinjam('A1', '000');
}
```


Ketujuh uji tambahan:

| Uji | Yang dibuktikan |
|---|---|
| `testSimpanIsbnYangSamaMenimpaBukuLama` | ISBN sama menimpa, jumlah tetap 1 |
| `testFilterMengembalikanIndeksBerurutan` | `array_values` bekerja: indeks `[0, 1]`, bukan `[0, 2]` |
| `testMapMengembalikanDaftarJudul` | `map` menghasilkan daftar judul berurutan |
| `testKelompokkanBerdasarkanPenulis` | Pengelompokan per penulis (Budi: 2, Sari: 1) |
| `testPinjamIsbnTidakTerdaftarMelemparBukuTidakDitemukan` | Exception benar untuk ISBN tak terdaftar |
| `testPinjamMelewatiBatasMelemparBatasPinjamTerlampaui` | Exception benar pada pinjaman ke-4 |
| `testPinjamBukuHabisMelemparBukuSedangDipinjam` | Exception benar saat stok habis |

Total 5 + 7 = **12 uji** (checkpoint modul: minimal 8).

### 3.3 `composer.json`

**Before**

```json
"autoload-dev": {
    "psr-4": {}
}
```

**After**


```json
"autoload-dev": {
    "psr-4": {
        "Perpustakaan\\Tests\\": "tests/"
    }
}
```


Tanpa pemetaan ini, PHPUnit melaporkan *No tests executed* karena namespace `Perpustakaan\Tests\` tidak dikenali. PHPUnit sendiri **belum** ditambahkan ke `require-dev` — jalankan `composer require --dev phpunit/phpunit` agar `composer.lock` ikut diperbarui.

### 3.4 `src/Exception/*.php` — komentar TODO

Sama seperti Java: kode sudah benar (*constructor property promotion* dengan `private readonly`), hanya komentar TODO yang tertinggal.

**Before**

```php
// TODO 4: membawa $isbn DAN $judul.
```

**After**

```php
// Membawa $isbn DAN $judul.
```


---

## 4. Perbandingan Java vs PHP

| Konsep | Java | PHP |
|---|---|---|
| Penyimpanan berkunci | `Map<String, T>` (`LinkedHashMap`) | array asosiatif `isbn => Buku` |
| Jaminan tipe | Generics `<T>`, dicek saat kompilasi | Type hint `Buku $b`, dicek saat runtime |
| Hasil "mungkin tidak ada" | `Optional<T>` | `?Buku` + `??` |
| Salinan aman | `List.copyOf(...)` | Array PHP disalin-saat-ubah (*copy-on-write*), `array_values(...)` |
| Filter | `stream().filter(...)` | `array_filter(array, callable)` |
| Map | `stream().map(...)` | `array_map(callable, array)` |
| Pengelompokan | `Collectors.groupingBy` | `foreach` + `$hasil[$kunci][] = ...` |
| Uji exception | `assertThrows(...)` | `expectException(...)` |
| Setup uji | `@BeforeEach` | `setUp()` |

---

## 5. Cara menjalankan

**Java** (membutuhkan JDK 25 sesuai `maven.compiler.release` di `pom.xml`):

```bash
cd java
mvn test
```

**PHP** (membutuhkan PHP 8.2+):

```bash
cd php
composer require --dev phpunit/phpunit
composer dump-autoload
./vendor/bin/phpunit tests
```

---

## 6. Verifikasi dan batasan

- **Java:** seluruh 11 uji dikompilasi dan dijalankan terhadap kode hasil perbaikan memakai runner sederhana dengan stub JUnit (`Tests run: 11, Failures: 0`). `mvn test` sungguhan **belum** dijalankan di lingkungan pembuatan dokumen ini (tidak ada Maven/JDK 25), jadi jalankan sendiri untuk memastikan.
- **PHP:** kode **belum dijalankan** karena PHP tidak tersedia di lingkungan pembuatan dokumen ini. Kode sudah ditelaah manual, tetapi jalankan `./vendor/bin/phpunit tests` dan periksa hasilnya sebelum didemokan.
- **Skenario sesi 10:** berkas `skenario-uji.md` tidak ikut dalam arsip, sehingga uji tambahan disusun dari empat exception domain yang ada pada `LibraryService`. Sesuaikan dengan daftar skenario Anda sendiri.
- **Demo:** modul mewajibkan Anda dapat menjelaskan setiap baris kode sendiri. Pelajari bagian *Analisis* di atas, lalu coba ubah satu baris dan lihat uji mana yang gagal (Langkah 7 modul).
