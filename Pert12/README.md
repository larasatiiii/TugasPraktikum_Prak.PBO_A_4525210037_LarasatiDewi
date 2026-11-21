# Before vs After — Refactoring Sesi 12 (SOLID, Repository, Strategy)

Dokumen ini membandingkan kode **sebelum** dan **sesudah** refactoring, beserta alasan setiap perbaikan.

## Ringkasan perubahan

| Aspek | Before | After |
|---|---|---|
| Kontrak penyimpanan | `BookRepository` kosong, hanya `TODO` | 4 method: `simpan`, `cariByIsbn`, `cariSemua`, `kurangiStok` |
| Implementasi penyimpanan | `InMemoryBookRepository` hanya punya field `data` | Seluruh method terisi, dengan penjagaan `null` dan salinan tak-berubah |
| Aturan denda | Hanya interface `AturanDenda` + daftar `TODO` | 4 implementasi (`Mahasiswa`, `Dosen`, `Bebas`, `Alumni`) + `AturanDendaFactory` |
| `LibraryService` | Membuat penyimpanannya sendiri (`new Repository<>(...)` / `new KoleksiBuku()`) | Menerima `BookRepository` dan `AturanDenda` lewat constructor |
| Perakitan objek | Tersebar di dalam `LibraryService` | Terpusat di `Main.java` dan `main.php` |
| Struktur PHP | Semua interface dan kelas dalam satu berkas `repository.php`, namespace `Perpustakaan` | Dipecah satu kelas per berkas: `Repository/`, `Denda/`, `Service/` (PSR-4) |
| Pengujian | Tidak ada | Uji dengan `InMemoryBookRepository` dan **repository palsu** yang selalu gagal |

---

# Bagian 1 — Java

## 1.1 `BookRepository` — kontrak penyimpanan

**Before** (`java/BookRepository.java`): interface tanpa method.

```java
public interface BookRepository {

    // TODO 1: rancang tanda tangan method yang DIBUTUHKAN LibraryService.
    //         Jangan menambahkan method yang tidak dipakai — itu pelanggaran
    //         Interface Segregation Principle.
    //
    // Saran minimal:
    //   void simpan(Buku buku);
    //   Optional<Buku> cariByIsbn(String isbn);
    //   List<Buku> cariSemua();
    //   void kurangiStok(Buku buku);
}
```

**After** (`repository/BookRepository.java`):

```java
public interface BookRepository {

    void simpan(Buku buku);

    Optional<Buku> cariByIsbn(String isbn);

    List<Buku> cariSemua();

    void kurangiStok(Buku buku);
}
```

**Yang diperbaiki**

- Kontrak sekarang ada, jadi `LibraryService` bisa bergantung pada abstraksi (DIP).
- Hanya empat method yang benar-benar dipakai `LibraryService`, tidak ada method tambahan (ISP).
- `cariByIsbn` mengembalikan `Optional<Buku>`, bukan `null`, sehingga pemanggil dipaksa menangani kasus "tidak ditemukan".

## 1.2 `InMemoryBookRepository` — implementasi untuk uji

**Before** (`java/InMemoryBookRepository.java`): hanya field, belum ada method.

```java
public class InMemoryBookRepository implements BookRepository {

    private final Map<String, Buku> data = new LinkedHashMap<>();

    // TODO 1: implementasikan seluruh method dari BookRepository.
    //         Perhatikan: cariSemua() harus mengembalikan salinan tak-berubah.
}
```

**After** (`repository/InMemoryBookRepository.java`):

```java
public class InMemoryBookRepository implements BookRepository {

    private final Map<String, Buku> data = new LinkedHashMap<>();

    @Override
    public void simpan(Buku buku) {
        if (buku == null) {
            throw new IllegalArgumentException("Buku tidak boleh null.");
        }
        data.put(buku.getNomorInduk(), buku);
    }

    @Override
    public Optional<Buku> cariByIsbn(String isbn) {
        if (isbn == null) {
            return Optional.empty();
        }
        return Optional.ofNullable(data.get(isbn));
    }

    @Override
    public List<Buku> cariSemua() {
        return Collections.unmodifiableList(new ArrayList<>(data.values()));
    }

    @Override
    public void kurangiStok(Buku buku) {
        if (buku == null) {
            throw new IllegalArgumentException("Buku tidak boleh null.");
        }
        buku.kurangiStok();
    }
}
```

**Yang diperbaiki**

- Seluruh method dari kontrak terimplementasi. Kode ini tidak akan dikompilasi sebelumnya, karena kelas yang `implements` interface wajib mengisi semua method-nya.
- `cariSemua()` mengembalikan **salinan tak-berubah** (`unmodifiableList(new ArrayList<>(...))`), sehingga koleksi internal tidak bocor ke luar (pelajaran enkapsulasi sesi 7).
- Argumen `null` ditangani secara eksplisit.

## 1.3 `AturanDenda` — Strategy untuk denda

**Before** (`java/AturanDenda.java`): interface, lalu daftar pekerjaan yang belum dibuat.

```java
public interface AturanDenda {
    double hitung(int hariTerlambat);
    String nama();
}

/*
 * TODO 1: buat tiga implementasi dalam berkas terpisah:
 *   DendaMahasiswa   -> Rp1.000 per hari
 *   DendaDosen       -> Rp500 per hari
 *   DendaBebas       -> selalu 0   (anggota kehormatan)
 *
 * TODO 2: buat AturanDendaFactory dengan method statis:
 *   public static AturanDenda untuk(String jenisAnggota)
 *
 * TODO 3 (Langkah 3): buktikan Open-Closed dengan menambahkan DendaAlumni
 *   TANPA menyunting satu baris pun pada LibraryService.
 */
```

**After** (`denda/`): interface tetap sama, dan semua `TODO` di atas menjadi berkas nyata.

| Berkas baru | Isi |
|---|---|
| `DendaMahasiswa.java` | Rp1.000 / hari |
| `DendaDosen.java` | Rp500 / hari |
| `DendaBebas.java` | Selalu `0.0` (anggota kehormatan) |
| `DendaAlumni.java` | Rp750 / hari (bukti Open-Closed) |
| `AturanDendaFactory.java` | Memilih implementasi berdasarkan jenis anggota |

```java
public class DendaMahasiswa implements AturanDenda {

    private static final double TARIF_PER_HARI = 1000.0;

    @Override
    public double hitung(int hariTerlambat) {
        return Math.max(0, hariTerlambat) * TARIF_PER_HARI;
    }

    @Override
    public String nama() {
        return "Mahasiswa (Rp1.000/hari)";
    }
}
```

```java
public final class AturanDendaFactory {

    private AturanDendaFactory() {
    }

    public static AturanDenda untuk(String jenisAnggota) {
        if (jenisAnggota == null) {
            throw new IllegalArgumentException("Jenis anggota tidak boleh null.");
        }
        switch (jenisAnggota.trim().toLowerCase()) {
            case "mahasiswa":
                return new DendaMahasiswa();
            case "dosen":
                return new DendaDosen();
            case "kehormatan":
            case "bebas":
                return new DendaBebas();
            case "alumni":
                return new DendaAlumni();
            default:
                throw new IllegalArgumentException("Jenis anggota tidak dikenal: " + jenisAnggota);
        }
    }
}
```

**Yang diperbaiki**

- **OCP:** jenis anggota baru cukup menambah satu kelas dan satu `case` di factory. `LibraryService` tidak disentuh.
- Keputusan "objek jenis mana yang dibuat" terpusat di satu tempat.
- Hari negatif dianggap nol (`Math.max(0, ...)`), jenis `null` dan jenis tak dikenal ditolak dengan `IllegalArgumentException`.

## 1.4 `LibraryService` — dari membuat sendiri menjadi menerima dari luar

**Before** (direkonstruksi dari `REFACTORING.md`, Temuan 1; lihat catatan di atas):

```java
// LibraryService menciptakan penyimpanan konkretnya sendiri
private final Repository<Buku> repo = new Repository<>(Buku::getNomorInduk);
```

**After** (`service/LibraryService.java`):

```java
public class LibraryService {

    private final BookRepository repo;
    private final AturanDenda aturanDenda;
    private final Map<String, Integer> pinjaman = new HashMap<>();

    public LibraryService(BookRepository repo, AturanDenda aturanDenda) {
        if (repo == null) {
            throw new IllegalArgumentException("BookRepository tidak boleh null.");
        }
        if (aturanDenda == null) {
            throw new IllegalArgumentException("AturanDenda tidak boleh null.");
        }
        this.repo = repo;
        this.aturanDenda = aturanDenda;
    }

    public double hitungDenda(int hariTerlambat) {
        return aturanDenda.hitung(hariTerlambat);
    }

    public String getNamaAturanDenda() {
        return aturanDenda.nama();
    }
    ...
}
```

Bagian `pinjam()` kini hanya berbicara lewat interface `BookRepository`:

```java
Buku bukuDiPinjam = repo.cariByIsbn(isbn)
        .orElseThrow(() -> new BukuTidakDitemukanException(isbn));
...
repo.kurangiStok(bukuDiPinjam);
```

**Yang diperbaiki**

- **DIP:** `LibraryService` tidak lagi menyebut kelas penyimpanan konkret dan tidak ada `new` untuk urusan penyimpanan maupun denda.
- Perhitungan denda didelegasikan ke `AturanDenda`, bukan dihitung di dalam service.
- Dependensi `null` ditolak sejak constructor (gagal cepat).

## 1.5 `Main` — satu-satunya tempat perakitan

**Before:** tidak ada perakitan terpusat; objek dibuat di dalam `LibraryService`.

**After** (`Main.java`):

```java
BookRepository repo = new InMemoryBookRepository();
AturanDenda aturanDenda = AturanDendaFactory.untuk("mahasiswa");
LibraryService libService = new LibraryService(repo, aturanDenda);
```

**Yang diperbaiki**

- `Main` menjadi satu-satunya tempat kelas konkret disebut.
- Mengganti penyimpanan ke basis data berarti **satu berkas baru** (`DatabaseBookRepository`) dan **satu baris** yang berubah di `Main`.

## 1.6 Unit test — membuktikan manfaatnya

**Before:** belum ada uji untuk `LibraryService` dengan dependensi yang disuntikkan.

**After** (`LibraryServiceTest.java`): repository palsu yang selalu gagal.

```java
private static class FailingBookRepository implements BookRepository {
    @Override
    public void simpan(Buku buku) {
        throw new RuntimeException("Koneksi basis data gagal saat simpan");
    }

    @Override
    public Optional<Buku> cariByIsbn(String isbn) {
        throw new RuntimeException("Koneksi basis data gagal saat cariByIsbn");
    }
    // cariSemua() dan kurangiStok() melempar exception yang sama
}

@Test
void ujiDenganRepositoryPalsuYangSelaluGagal() {
    LibraryService service = new LibraryService(new FailingBookRepository(), new DendaMahasiswa());
    service.daftarkanAnggota("A-001");

    RuntimeException ex = assertThrows(RuntimeException.class, () -> service.pinjam("A-001", "978-1"));
    assertTrue(ex.getMessage().contains("Koneksi basis data gagal"));
}
```

Uji lain memeriksa tarif tiap strategi untuk 3 hari terlambat:

```java
assertEquals(3000.0, new LibraryService(new InMemoryBookRepository(), new DendaMahasiswa()).hitungDenda(3));
assertEquals(1500.0, new LibraryService(new InMemoryBookRepository(), new DendaDosen()).hitungDenda(3));
assertEquals(0.0,    new LibraryService(new InMemoryBookRepository(), new DendaBebas()).hitungDenda(3));
assertEquals(2250.0, new LibraryService(new InMemoryBookRepository(), new DendaAlumni()).hitungDenda(3));
```

Uji dengan repository palsu **tidak mungkin ditulis sebelum refactoring**, karena `LibraryService` dulu membuat penyimpanannya sendiri dan tidak ada celah untuk menggantinya.

---

# Bagian 2 — PHP

## 2.1 Dari satu berkas menjadi satu kelas per berkas

**Before:** seluruh kontrak, implementasi, strategi, dan layanan ada dalam satu berkas `php/repository.php` dengan namespace `Perpustakaan`.

```php
namespace Perpustakaan;

interface BookRepository
{
    // TODO 1: rancang tanda tangan method yang DIBUTUHKAN LibraryService.
}

final class InMemoryBookRepository implements BookRepository
{
    /** @var array<string,Buku> */
    private array $data = [];

    // TODO 2: implementasikan seluruh method dari BookRepository.
}

interface AturanDenda
{
    public function hitung(int $hariTerlambat): float;
    public function nama(): string;
}

// TODO 3: buat DendaMahasiswa (1000/hari), DendaDosen (500/hari), dan DendaBebas (selalu 0).
// TODO 4: buat AturanDendaFactory::untuk(string $jenisAnggota): AturanDenda

final class LibraryService
{
    // TODO 5: terima BookRepository DAN AturanDenda lewat constructor.
    // TODO 6: pindahkan method-method aturan bisnis ke sini.
}
```

**After:** setiap tanggung jawab punya berkas dan namespace sendiri, sesuai autoload PSR-4 (`Perpustakaan\` → `src/`).

```
src/
├── Denda/
│   ├── AturanDenda.php            (namespace Perpustakaan\Denda)
│   ├── AturanDendaFactory.php
│   ├── DendaMahasiswa.php
│   ├── DendaDosen.php
│   ├── DendaBebas.php
│   └── DendaAlumni.php
├── Repository/
│   ├── BookRepository.php         (namespace Perpustakaan\Repository)
│   └── InMemoryBookRepository.php
└── Service/
    └── LibraryService.php         (namespace Perpustakaan\Service)
```

**Yang diperbaiki**

- Satu berkas berisi banyak kelas tidak bisa dimuat oleh autoload PSR-4, yang mencari satu kelas per berkas sesuai namespace-nya.
- Tanggung jawab terpisah per folder (SRP): penyimpanan, aturan denda, dan aturan bisnis tidak lagi bercampur.

## 2.2 `BookRepository` dan `InMemoryBookRepository`

**Before:** interface kosong, kelas hanya punya field `$data`.

**After** (`Repository/BookRepository.php`):

```php
interface BookRepository
{
    public function simpan(Buku $buku): void;

    public function cariByIsbn(string $isbn): ?Buku;

    /** @return Buku[] */
    public function cariSemua(): array;

    public function kurangiStok(Buku $buku): void;
}
```

**After** (`Repository/InMemoryBookRepository.php`):

```php
final class InMemoryBookRepository implements BookRepository
{
    /** @var array<string, Buku> */
    private array $data = [];

    public function simpan(Buku $buku): void
    {
        $this->data[$buku->getNomorInduk()] = $buku;
    }

    public function cariByIsbn(string $isbn): ?Buku
    {
        return $this->data[$isbn] ?? null;
    }

    /** @return Buku[] */
    public function cariSemua(): array
    {
        return array_values($this->data);
    }

    public function kurangiStok(Buku $buku): void
    {
        $buku->kurangiStok();
    }
}
```

**Yang diperbaiki**

- Kontrak dan implementasinya lengkap, dengan tipe kembalian `?Buku` (padanan `Optional` di PHP).
- `cariSemua()` memakai `array_values` sehingga indeks selalu berurutan dari 0.

## 2.3 Strategy denda dan factory

**Before:** hanya `interface AturanDenda` dan dua komentar `TODO 3` dan `TODO 4`.

**After** (`Denda/DendaMahasiswa.php` dan `Denda/AturanDendaFactory.php`):

```php
final class DendaMahasiswa implements AturanDenda
{
    private const float TARIF_PER_HARI = 1000.0;

    public function hitung(int $hariTerlambat): float
    {
        return max(0, $hariTerlambat) * self::TARIF_PER_HARI;
    }

    public function nama(): string
    {
        return 'Mahasiswa (Rp1.000/hari)';
    }
}
```

```php
final class AturanDendaFactory
{
    public static function untuk(string $jenisAnggota): AturanDenda
    {
        return match (strtolower(trim($jenisAnggota))) {
            'mahasiswa' => new DendaMahasiswa(),
            'dosen' => new DendaDosen(),
            'kehormatan', 'bebas' => new DendaBebas(),
            'alumni' => new DendaAlumni(),
            default => throw new InvalidArgumentException("Jenis anggota tidak dikenal: {$jenisAnggota}"),
        };
    }
}
```

**Yang diperbaiki**

- `match` menggantikan rantai `if`, dan `default` melempar `InvalidArgumentException` untuk jenis yang tidak dikenal.
- `DendaAlumni` ditambahkan tanpa menyunting `LibraryService` (OCP).

## 2.4 `LibraryService` — constructor injection

**Before** (direkonstruksi dari `REFACTORING.md`, Temuan 1; lihat catatan di atas):

```php
// LibraryService menciptakan penyimpanan konkretnya sendiri
private KoleksiBuku $repo;

public function __construct()
{
    $this->repo = new KoleksiBuku();
}
```

**After** (`Service/LibraryService.php`):

```php
final class LibraryService
{
    public const int BATAS_PINJAM = 3;

    /** @var array<string,int> */
    private array $pinjaman = [];

    public function __construct(
        private readonly BookRepository $repo,
        private readonly AturanDenda $aturanDenda,
    ) {}

    public function pinjam(string $idAnggota, string $isbn): void
    {
        ...
        $bukuDipinjam = $this->repo->cariByIsbn($isbn);

        if ($bukuDipinjam === null) {
            throw new BukuTidakDitemukanException($isbn);
        }
        ...
        $this->repo->kurangiStok($bukuDipinjam);
        $this->pinjaman[$idAnggota]++;
    }

    public function hitungDenda(int $hariTerlambat): float
    {
        return $this->aturanDenda->hitung($hariTerlambat);
    }
}
```

**Yang diperbaiki**

- Dependensi bertipe interface dan ditandai `readonly`, sehingga tidak bisa diganti setelah objek dibuat.
- Tidak ada `new` di dalam kelas untuk penyimpanan maupun aturan denda.

## 2.5 `main.php` — perakitan di titik masuk

**After** (`main.php`):

```php
$repo = new InMemoryBookRepository();
$aturanDenda = AturanDendaFactory::untuk('mahasiswa');
$s = new LibraryService($repo, $aturanDenda);
```

Mengganti penyimpanan ke basis data: satu berkas baru (`DatabaseBookRepository`) dan satu baris di `main.php`.

## 2.6 Unit test — repository palsu

**After** (`tests/Service/LibraryServiceTest.php`):

```php
$failingRepo = new class implements BookRepository {
    public function simpan(Buku $buku): void
    {
        throw new RuntimeException('Koneksi basis data gagal saat simpan');
    }

    public function cariByIsbn(string $isbn): ?Buku
    {
        throw new RuntimeException('Koneksi basis data gagal saat cariByIsbn');
    }
    // cariSemua() dan kurangiStok() melempar exception yang sama
};

$service = new LibraryService($failingRepo, new DendaMahasiswa());
$service->daftarkanAnggota('A-001');

$this->expectException(RuntimeException::class);
$this->expectExceptionMessage('Koneksi basis data gagal');
$service->pinjam('A-001', '978-1');
```

---

# Bagian 3 — Pemetaan `TODO` ke hasil

| `TODO` di kerangka (before) | Selesai di (after) |
|---|---|
| Java `BookRepository` TODO 1 | `repository/BookRepository.java` |
| Java `InMemoryBookRepository` TODO 1 | `repository/InMemoryBookRepository.java` |
| Java `AturanDenda` TODO 1 (tiga implementasi) | `denda/DendaMahasiswa.java`, `DendaDosen.java`, `DendaBebas.java` |
| Java `AturanDenda` TODO 2 (factory) | `denda/AturanDendaFactory.java` |
| Java `AturanDenda` TODO 3 (alumni, bukti OCP) | `denda/DendaAlumni.java` |
| PHP TODO 1 dan 2 (repository) | `Repository/BookRepository.php`, `InMemoryBookRepository.php` |
| PHP TODO 3 (tiga implementasi) | `Denda/DendaMahasiswa.php`, `DendaDosen.php`, `DendaBebas.php` |
| PHP TODO 4 (factory) | `Denda/AturanDendaFactory.php` |
| PHP TODO 5 dan 6 (service) | `Service/LibraryService.php` |

## Pelanggaran SOLID yang diperbaiki

| Prinsip | Masalah (before) | Perbaikan (after) |
|---|---|---|
| **DIP** | `LibraryService` membuat penyimpanan konkret sendiri | Menerima `BookRepository` lewat constructor |
| **OCP** | Aturan denda per jenis anggota tidak bisa ditambah tanpa menyunting service | `AturanDenda` + factory; jenis baru = 1 berkas + 1 baris |
| **SRP** | Penyimpanan, aturan bisnis, dan tampilan bercampur | Dipisah: `repository/`, `denda/`, `service/` |
| **ISP** | (Pencegahan) interface repository berpotensi menumpuk method | Hanya 4 method yang dipakai service |

## Bukti perubahan minimal

| Skenario | Berkas baru | Berkas lama yang berubah |
|---|---|---|
| Menambah aturan denda keempat (alumni) | `DendaAlumni` | `AturanDendaFactory` (satu `case`). `LibraryService` **tidak berubah**. |
| Mengganti penyimpanan ke basis data | `DatabaseBookRepository` | `Main.java` / `main.php` (satu baris). `LibraryService` **tidak berubah**. |

---

