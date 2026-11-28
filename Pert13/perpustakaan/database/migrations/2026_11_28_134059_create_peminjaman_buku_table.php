<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anggota')->constrained('anggota')->cascadeOnDelete();
            $table->foreignId('id_buku')->constrained('buku')->cascadeOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_buku');
    }
};
