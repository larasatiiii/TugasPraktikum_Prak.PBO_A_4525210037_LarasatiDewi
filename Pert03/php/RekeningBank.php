<?php
declare(strict_types=1);

/**
 * Sesi 3 — rekening bank (PHP).
 * Default parameter dan named constructor menggantikan constructor overloading.
 */
class RekeningBank
{
    // TODO 1: konstanta bernama
    public const BUNGA_TAHUNAN = 0.025;
    public const BIAYA_ADMIN = 5000.0;
    public const BATAS_PENARIKAN = 5000000.0;

    // TODO 2: penghitung jumlah rekening    private static int $jumlahRekening = 0;
    private static int $jumlahRekening = 0;


    private float $saldo;

    /**
     * TODO 3 dan 4: validasi data dan tambah penghitung.
     */
    public function __construct(
        private readonly string $nomor,
        private readonly string $pemilik,
        float $saldoAwal = 0.0,
    ) {
        if (trim($this->nomor) === '') {
            throw new InvalidArgumentException('Nomor rekening tidak boleh kosong.');
        }

        if (trim($this->pemilik) === '') {
            throw new InvalidArgumentException('Nama pemilik tidak boleh kosong.');
        }

        if (!is_finite($saldoAwal) || $saldoAwal < 0) {
            throw new InvalidArgumentException('Saldo awal tidak boleh negatif atau tidak valid.');
        }

        $this->saldo = $saldoAwal;
        self::$jumlahRekening++;
    }

    /**
     * TODO 5: named constructor rekening pelajar.
     */
    public static function rekeningPelajar(string $nomor, string $pemilik): static
    {
        return new static($nomor, $pemilik, 0.0);
    }

    /**
     * TODO 6: setor uang.
     */
    public function setor(float $jumlah): void
    {
        if (!is_finite($jumlah) || $jumlah <= 0) {
            throw new InvalidArgumentException('Jumlah setoran harus lebih besar dari 0.');
        }

        $this->saldo += $jumlah;
    }

    /**
     * TODO 7: tarik uang.
     */
    public function tarik(float $jumlah): void
    {
        if (!is_finite($jumlah) || $jumlah <= 0) {
            throw new InvalidArgumentException('Jumlah penarikan harus lebih besar dari 0.');
        }

        if ($jumlah > self::BATAS_PENARIKAN) {
            throw new InvalidArgumentException('Penarikan melebihi batas Rp5.000.000 per transaksi.');
        }

        if ($jumlah > $this->saldo) {
            throw new InvalidArgumentException('Saldo tidak mencukupi untuk penarikan.');
        }

        $this->saldo -= $jumlah;
    }

    /**
     * TODO 8: potong biaya administrasi tanpa saldo negatif.
     */
    public function potongBiayaAdmin(): void
    {
        if ($this->saldo >= self::BIAYA_ADMIN) {
            $this->saldo -= self::BIAYA_ADMIN;
        } else {
            $this->saldo = 0.0;
        }
    }

    /**
     * TODO 9: jumlah rekening yang berhasil dibuat.
     */
    public static function getJumlahRekening(): int
    {
        return self::$jumlahRekening;
    }

    /**
     * TODO 10: hitung bunga setahun.
     */
    public static function bungaSetahun(float $pokok): float
    {
        if (!is_finite($pokok) || $pokok < 0) {
            throw new InvalidArgumentException('Pokok bunga tidak boleh negatif atau tidak valid.');
        }

        return $pokok * self::BUNGA_TAHUNAN;
    }

    public function getSaldo(): float
    {
        return $this->saldo;
    }

    public function getNomor(): string
    {
        return $this->nomor;
    }

    public function __toString(): string
    {
        return sprintf(
            'Rekening[%s] %-14s Rp%s',
            $this->nomor,
            $this->pemilik,
            number_format($this->saldo, 2, ',', '.')
        );
    }
}
