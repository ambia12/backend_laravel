<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    /**
     * Menampilkan daftar seluruh meja dan statusnya.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Table::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->orderBy('table_number')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar meja berhasil diambil.',
            'data' => $tables,
        ]);
    }

    /**
     * Menambahkan meja kantin baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:50|unique:tables,table_number',
            'status' => 'nullable|in:available,occupied',
        ]);

        $table = Table::create([
            'table_number' => $validated['table_number'],
            'qr_code_token' => 'KTG-' . Str::upper(Str::random(10)),
            'status' => $validated['status'] ?? 'available',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Meja baru berhasil ditambahkan.',
            'data' => $table,
        ], 201);
    }

    /**
     * Menampilkan informasi satu meja.
     */
    public function show(int $id): JsonResponse
    {
        $table = Table::with(['orders' => function ($q) {
            $q->latest()->take(5);
        }])->find($id);

        if (!$table) {
            return response()->json([
                'status' => 'error',
                'message' => 'Meja tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail meja berhasil diambil.',
            'data' => $table,
        ]);
    }

    /**
     * Memperbarui nomor atau status meja.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json([
                'status' => 'error',
                'message' => 'Meja tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validate([
            'table_number' => 'sometimes|required|string|max:50|unique:tables,table_number,' . $id,
            'status' => 'sometimes|required|in:available,occupied',
        ]);

        $table->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data meja berhasil diperbarui.',
            'data' => $table,
        ]);
    }

    /**
     * Menghapus meja dari sistem.
     */
    public function destroy(int $id): JsonResponse
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json([
                'status' => 'error',
                'message' => 'Meja tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $table->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Meja berhasil dihapus.',
            'data' => null,
        ]);
    }

    /**
     * Memverifikasi token QR meja saat pengunjung scan di meja.
     */
    public function scanQr(string $token): JsonResponse
    {
        $table = Table::where('qr_code_token', $token)->first();

        if (!$table) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code meja tidak valid atau tidak terdaftar.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'QR Code meja valid.',
            'data' => $table,
        ]);
    }
}
