<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

    protected $fillable = [
        'isbn',
        'nama_penulis',
        'title',
        'jumlah_eksamplar',
    ];

    protected $casts = [
        'jumlah_eksamplar' => 'integer',
    ];

    // Relasi: Satu buku bisa memiliki banyak riwayat peminjaman
    public function peminjaman(): HasMany
    {
        return $this->hasMany(PeminjamanBuku::class, 'id_buku');
    }
}
