<?php
declare(strict_types=1);

/**
 * Sesi 2 — enkapsulasi yang menjaga invariant (PHP).
 */
class Mahasiswa
{
    // Konstanta bobot nilai
   public const BOBOT_TUGAS = 0.30;
    public const BOBOT_UTS   = 0.30;
    public const BOBOT_UAS   = 0.40;

    private const NILAI_MIN = 0.0;
    private const NILAI_MAX = 100.0;

    /**
     * TODO 1:
     * NIM dan nama menggunakan readonly agar tidak berubah.
     * Nilai juga dibuat readonly karena tidak ada fitur untuk
     * mengubah nilai setelah objek dibuat.
     */
    public function __construct(
        private readonly string $nim,
        private readonly string $nama,
        private readonly float $nilaiTugas,
        private readonly float $nilaiUts,
        private readonly float $nilaiUas,
    ) {
        // TODO 2: validasi NIM
        if (trim($this->nim) === '') {
            throw new InvalidArgumentException(
                'NIM tidak boleh kosong.'
            );
        }

        // Validasi nama
        if (trim($this->nama) === '') {
            throw new InvalidArgumentException(
                'Nama mahasiswa tidak boleh kosong.'
            );
        }

        // TODO 3: validasi setiap komponen nilai
        self::pastikanNilaiSah('Tugas', $this->nilaiTugas);
        self::pastikanNilaiSah('UTS', $this->nilaiUts);
        self::pastikanNilaiSah('UAS', $this->nilaiUas);
    }

    /**
     * TODO 4: validasi satu komponen nilai.
     */
    private static function pastikanNilaiSah(
        string $namaKomponen,
        float $nilai
    ): void {
        if (
            !is_finite($nilai) ||
            $nilai < self::NILAI_MIN ||
            $nilai > self::NILAI_MAX
        ) {
            throw new InvalidArgumentException(
                "Nilai {$namaKomponen} harus berada pada rentang 0 sampai 100."
            );
        }
    }

    /**
     * TODO 5: menghitung nilai akhir berdasarkan bobot.
     */
    public function nilaiAkhir(): float
    {
        return ($this->nilaiTugas * self::BOBOT_TUGAS)
             + ($this->nilaiUts * self::BOBOT_UTS)
             + ($this->nilaiUas * self::BOBOT_UAS);
    }

    /**
     * TODO 6: menentukan huruf mutu.
     */
    public function hurufMutu(): string
    {
        $nilai = $this->nilaiAkhir();

        return match (true) {
            $nilai >= 80 => 'A',
            $nilai >= 70 => 'B',
            $nilai >= 60 => 'C',
            $nilai >= 50 => 'D',
            default      => 'E',
        };
    }

    /**
     * TODO 7: getter tanpa setter NIM.
     */
    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getNilaiAkhir(): float
    {
        return $this->nilaiAkhir();
    }

    /**
     * Menampilkan informasi mahasiswa.
     */
    public function __toString(): string
    {
        return sprintf(
            '%-10s %-18s akhir=%6.2f  mutu=%s',
            $this->nim,
            $this->nama,
            $this->nilaiAkhir(),
            $this->hurufMutu()
        );
    }
}