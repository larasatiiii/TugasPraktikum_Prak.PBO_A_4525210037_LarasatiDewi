<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(): JsonResponse
    {
        $buku = Buku::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $buku,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:20|unique:buku,isbn',
            'nama_penulis' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'jumlah_eksamplar' => 'required|integer|min:0',
        ]);

        $buku = Buku::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $buku = Buku::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $buku,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'isbn' => 'required|string|max:20|unique:buku,isbn,' . $buku->id,
            'nama_penulis' => 'required|string|max:255',
            'jumlah_eksamplar' => 'required|integer|min:0',
        ]);

        $buku->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Data buku berhasil diperbarui',
            'data' => $buku,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return response()->json([
            'status' => true,
            'message' => 'Buku berhasil dihapus',
        ]);
    }
}
