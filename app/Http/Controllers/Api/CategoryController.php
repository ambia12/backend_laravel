<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori menu.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('menus')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => $categories,
        ]);
    }

    /**
     * Menyimpan kategori menu baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori baru berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    /**
     * Menampilkan detail satu kategori.
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::with('menus')->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail kategori berhasil dimuat.',
            'data' => $category,
        ]);
    }

    /**
     * Memperbarui nama/slug kategori.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    /**
     * Menghapus kategori menu.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.',
            'data' => null,
        ]);
    }
}
