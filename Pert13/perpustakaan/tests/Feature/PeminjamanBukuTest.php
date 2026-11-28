<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\PeminjamanBuku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanBukuTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_dapat_meminjam_buku_dan_stok_berkurang(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create([
            'jumlah_eksamplar' => 5,
        ]);

        $response = $this->postJson('/api/pinjam-buku/create', [
            'id_anggota' => $anggota->id,
            'id_buku' => $buku->id,
            'tanggal_pinjam' => '2026-11-26 00:00:00',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Peminjaman buku berhasil dicatat',
            ]);

        $this->assertDatabaseHas('peminjaman_buku', [
            'id_anggota' => $anggota->id,
            'id_buku' => $buku->id,
            'tanggal_pinjam' => '2026-11-26 00:00:00',
        ]);

        // Memastikan stok berkurang dari 5 menjadi 4
        $this->assertDatabaseHas('buku', [
            'id' => $buku->id,
            'jumlah_eksamplar' => 4,
        ]);
    }

    public function test_gagal_meminjam_jika_stok_buku_kosong(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create([
            'jumlah_eksamplar' => 0,
        ]);

        $response = $this->postJson('/api/pinjam-buku/create', [
            'id_anggota' => $anggota->id,
            'id_buku' => $buku->id,
            'tanggal_pinjam' => '2026-11-26 00:00:00',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'Stok buku sedang kosong',
            ]);

        $this->assertDatabaseCount('peminjaman_buku', 0);
    }

    public function test_mengembalikan_buku_menambah_stok_kembali(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create([
            'jumlah_eksamplar' => 3,
        ]);

        $peminjaman = PeminjamanBuku::create([
            'id_anggota' => $anggota->id,
            'id_buku' => $buku->id,
            'tanggal_pinjam' => '2026-11-20',
            'tanggal_pengembalian' => null,
        ]);

        $response = $this->patchJson("/api/pinjam-buku/update/{$peminjaman->id}", [
            'tanggal_pengembalian' => '2026-11-26 00:00:00',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Data peminjaman berhasil diperbarui',
            ]);

        // Memastikan stok bertambah kembali dari 3 menjadi 4
        $this->assertDatabaseHas('buku', [
            'id' => $buku->id,
            'jumlah_eksamplar' => 4,
        ]);
    }

    public function test_validasi_gagal_jika_anggota_atau_buku_tidak_ditemukan(): void
    {
        $response = $this->postJson('/api/pinjam-buku/create', [
            'id_anggota' => 999,
            'id_buku' => 999,
            'tanggal_pinjam' => '2026-11-26 00:00:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_anggota', 'id_buku']);
    }
}
