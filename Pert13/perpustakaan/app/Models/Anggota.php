<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Model
{
    use HasFactory;
    protected $table = 'anggota';

    protected $fillable = [
        'id_anggota',
        'nama_anggota',
    ];

    // Relasi: Satu anggota bisa melakukan banyak peminjaman buku
    public function peminjaman(): HasMany
    {
        return $this->hasMany(PeminjamanBuku::class, 'id_anggota');
    }
}
