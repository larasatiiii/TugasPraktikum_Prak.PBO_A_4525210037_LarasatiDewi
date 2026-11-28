<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanBuku extends Model
{
    protected $table = 'peminjaman_buku';

    protected $fillable = [
        'id_anggota',
        'id_buku',
        'tanggal_pinjam',
        'tanggal_pengembalian',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_pengembalian' => 'date',
    ];

    // Relasi ke tabel anggota
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    // Relasi ke tabel buku
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku');
    }
}
