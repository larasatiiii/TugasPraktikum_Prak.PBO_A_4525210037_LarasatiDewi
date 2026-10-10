<?php
declare(strict_types=1);

/**
 * Langkah 6 — latihan mandiri.
 *
 * Buat hierarki Notifikasi dengan tiga turunan: Email, SMS, WhatsApp.
 * Lalu lengkapi kirimSemua() TANPA satu pun pemeriksaan tipe.
 */

// TODO 1: buat kelas abstrak Notifikasi dengan:
//         - properti readonly $tujuan
//         - method abstract kirim(string $pesan): void
//         - method saluran(): string yang menyebut nama salurannya
abstract class Notifikasi
{
    private string $tujuan;

    public function __construct(string $tujuan)
    {
        $this->tujuan = $tujuan;
    }

    public function getTujuan(): string
    {
        return $this->tujuan;
    }

    abstract public function kirim(string $pesan): void;
    abstract public function saluran(): string;
}

// TODO 2: buat tiga turunan: Email, SMS, WhatsApp.
//         Masing-masing mencetak format pesan yang berbeda.
class Email extends Notifikasi
{
    public function saluran(): string
    {
        return 'Email';
    }

    public function kirim(string $pesan): void
    {
        echo sprintf("[%s] Mengirim email ke <%s>:\n\"%s\"\n\n", $this->saluran(), $this->getTujuan(), $pesan);
    }
}

class SMS extends Notifikasi
{
    public function saluran(): string
    {
        return 'SMS';
    }

    public function kirim(string $pesan): void
    {
        echo sprintf("[%s] SMS terkirim ke nomor %s: %s\n\n", $this->saluran(), $this->getTujuan(), $pesan);
    }
}

class WhatsApp extends Notifikasi
{
    public function saluran(): string
    {
        return 'WhatsApp';
    }

    public function kirim(string $pesan): void
    {
        echo sprintf("[%s] WA chat ke %s -> %s\n\n", $this->saluran(), $this->getTujuan(), $pesan);
    }
}

/**
 * TODO 3: kirim pesan ke seluruh notifikasi dalam daftar.
 *
 * ATURAN: tidak boleh ada instanceof, tidak boleh ada match/switch
 *         atas jenis notifikasi. Kalau Anda merasa membutuhkannya,
 *         berarti hierarki Anda belum benar.
 *
 * @param Notifikasi[] $daftar
 */
function kirimSemua(array $daftar, string $pesan): void
{
    foreach ($daftar as $notifikasi) {
        $notifikasi->kirim($pesan);
    }
}

// Uji setelah TODO 1-3 selesai:
header('Content-Type: text/plain');

kirimSemua([
    new Email('ani@univpancasila.ac.id'),
    new SMS('081234567890'),
    new WhatsApp('081234567890'),
], 'Buku yang Anda pesan sudah tersedia.');