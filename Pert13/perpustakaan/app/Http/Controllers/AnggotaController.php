<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(): JsonResponse
    {
        $anggota = Anggota::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $anggota,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_anggota' => 'required|string|max:20|unique:anggota,id_anggota',
            'nama_anggota' => 'required|string|max:255',
        ]);

        $anggota = Anggota::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Anggota berhasil ditambahkan',
            'data' => $anggota,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $anggota = Anggota::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $anggota,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $anggota = Anggota::findOrFail($id);

        $validated = $request->validate([
            'id_anggota' => 'required|string|max:20|unique:anggota,id_anggota,' . $anggota->id,
            'nama_anggota' => 'required|string|max:255',
        ]);

        $anggota->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Data anggota berhasil diperbarui',
            'data' => $anggota,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();

        return response()->json([
            'status' => true,
            'message' => 'Anggota berhasil dihapus',
        ]);
    }
}
