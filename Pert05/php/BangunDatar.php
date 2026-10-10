<?php
declare(strict_types=1);

abstract class BangunDatar
{
    private string $nama;

    public function __construct(string $nama)
    {
        $this->nama = $nama;
    }

    abstract public function luas(): float;
    abstract public function keliling(): float;

    public function getNama(): string { return $this->nama; }

    public function __toString(): string
    {
        return sprintf('%-12s luas=%10.2f  keliling=%10.2f',
            $this->nama, $this->luas(), $this->keliling());
    }
}

class Lingkaran extends BangunDatar
{
    private float $jariJari;

    public function __construct(float $jariJari)
    {
        parent::__construct('Lingkaran');

        // TODO 1: tolak jari-jari <= 0.
        if ($jariJari <= 0) {
            throw new InvalidArgumentException("Jari-jari harus lebih besar dari 0.");
        }

        $this->jariJari = $jariJari;
    }

    // TODO 2: lengkapi. Gunakan M_PI, bukan 3.14. child 1
    public function luas(): float
    {
        return M_PI * ($this->jariJari ** 2);
    }

    public function keliling(): float
    {
        return 2 * M_PI * $this->jariJari;
    }

    public function getJariJari(): float { return $this->jariJari; }
}

class Persegi extends BangunDatar
{
    private float $sisi;

    public function __construct(float $sisi)
    {
        parent::__construct('Persegi');

        // TODO 1: tolak sisi <= 0.
        if ($sisi <= 0) {
            throw new InvalidArgumentException("Panjang sisi harus lebih besar dari 0.");
        }

        $this->sisi = $sisi;
    }

    // TODO 2: lengkapi. child 2
    public function luas(): float
    {
        return $this->sisi * $this->sisi;
    }

    public function keliling(): float
    {
        return 4 * $this->sisi;
    }

    public function getSisi(): float { return $this->sisi; }
}

// TODO Langkah 2: buat kelas Segitiga (tiga sisi, rumus Heron).
//                 Tolak konstruksi bila ketiga sisi tidak membentuk segitiga.
class Segitiga extends BangunDatar
{
    private float $a;
    private float $b;
    private float $c;

    public function __construct(float $a, float $b, float $c)
    {
        parent::__construct('Segitiga');

        if ($a <= 0 || $b <= 0 || $c <= 0) {
            throw new InvalidArgumentException("Setiap sisi segitiga harus bernilai lebih besar dari 0.");
        }

        // Syarat segitiga valid: jumlah dua sisi harus selalu lebih panjang dari sisi ketiga
        if ($a + $b <= $c || $a + $c <= $b || $b + $c <= $a) {
            throw new InvalidArgumentException("Kombinasi sisi tidak memenuhi syarat segitiga.");
        }

        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    // child 3
    public function luas(): float
    {
        // Rumus Heron: s = (a + b + c) / 2
        // Luas = sqrt(s * (s - a) * (s - b) * (s - c))
        $s = $this->keliling() / 2;
        return sqrt($s * ($s - $this->a) * ($s - $this->b) * ($s - $this->c));
    }

    public function keliling(): float
    {
        return $this->a + $this->b + $this->c;
    }

    public function getA(): float { return $this->a; }
    public function getB(): float { return $this->b; }
    public function getC(): float { return $this->c; }
}

// TODO Langkah 4: buat kelas Trapesium.
class Trapesium extends BangunDatar
{
    private float $sisiSejajarA;
    private float $sisiSejajarB;
    private float $sisiMiringC;
    private float $sisiMiringD;
    private float $tinggi;

    public function __construct(
        float $sisiSejajarA,
        float $sisiSejajarB,
        float $sisiMiringC,
        float $sisiMiringD,
        float $tinggi
    ) {
        parent::__construct('Trapesium');

        if ($sisiSejajarA <= 0 || $sisiSejajarB <= 0 || 
            $sisiMiringC <= 0 || $sisiMiringD <= 0 || $tinggi <= 0) {
            throw new InvalidArgumentException("Semua parameter trapesium harus bernilai lebih besar dari 0.");
        }

        $this->sisiSejajarA = $sisiSejajarA;
        $this->sisiSejajarB = $sisiSejajarB;
        $this->sisiMiringC = $sisiMiringC;
        $this->sisiMiringD = $sisiMiringD;
        $this->tinggi = $tinggi;
    }

    // child 4
    public function luas(): float
    {
        return 0.5 * ($this->sisiSejajarA + $this->sisiSejajarB) * $this->tinggi;
    }

    public function keliling(): float
    {
        return $this->sisiSejajarA + $this->sisiSejajarB + $this->sisiMiringC + $this->sisiMiringD;
    }

    public function getSisiSejajarA(): float { return $this->sisiSejajarA; }
    public function getSisiSejajarB(): float { return $this->sisiSejajarB; }
    public function getSisiMiringC(): float  { return $this->sisiMiringC; }
    public function getSisiMiringD(): float  { return $this->sisiMiringD; }
    public function getTinggi(): float       { return $this->tinggi; }
}

// --- Driver Code untuk menguji hasil di browser / terminal ---
header('Content-Type: text/plain');

$daftarBangun = [
    new Lingkaran(7),
    new Persegi(4),
    new Segitiga(3, 4, 5),
    new Trapesium(10, 6, 5, 5, 4),
];

foreach ($daftarBangun as $bangun) {
    echo $bangun . PHP_EOL;
}