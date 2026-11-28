<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\PeminjamanBuku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanBukuController extends Controller
{
    public function index(): JsonResponse
    {
        $peminjaman = PeminjamanBuku::with(['anggota', 'buku'])->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $peminjaman,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_anggota' => 'required|exists:anggota,id',
            'id_buku' => 'required|exists:buku,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        $buku = Buku::findOrFail($validated['id_buku']);

        if ($buku->jumlah_eksamplar <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Stok buku sedang kosong',
            ], 422);
        }

        $peminjaman = DB::transaction(function () use ($validated, $buku) {
            $buku->decrement('jumlah_eksamplar');

            return PeminjamanBuku::create($validated);
        });

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman buku berhasil dicatat',
            'data' => $peminjaman->load(['anggota', 'buku']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $peminjaman = PeminjamanBuku::with(['anggota', 'buku'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $peminjaman,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $peminjaman = PeminjamanBuku::findOrFail($id);

        $validated = $request->validate([
            'id_anggota' => 'sometimes|required|exists:anggota,id',
            'id_buku' => 'sometimes|required|exists:buku,id',
            'tanggal_pinjam' => 'sometimes|required|date',
            'tanggal_pengembalian' => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        DB::transaction(function () use ($peminjaman, $validated) {
            // Kembalikan stok buku otomatis jika buku baru saja dikembalikan
            $baruDikembalikan = $peminjaman->tanggal_pengembalian === null
                && !empty($validated['tanggal_pengembalian']);

            $peminjaman->update($validated);

            if ($baruDikembalikan) {
                $peminjaman->buku()->increment('jumlah_eksamplar');
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman berhasil diperbarui',
            'data' => $peminjaman->load(['anggota', 'buku']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $peminjaman = PeminjamanBuku::findOrFail($id);

        DB::transaction(function () use ($peminjaman) {
            // Jika transaksi dihapus saat buku belum dikembalikan, kembalikan stoknya
            if ($peminjaman->tanggal_pengembalian === null) {
                $peminjaman->buku()->increment('jumlah_eksamplar');
            }

            $peminjaman->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman berhasil dihapus',
        ]);
    }
}