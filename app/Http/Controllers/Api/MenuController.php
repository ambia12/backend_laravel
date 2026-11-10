<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu dengan filter kategori dan pencarian.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Menu::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('available_only', false)) {
            $query->where('is_available', true);
        }

        $menus = $query->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar menu berhasil diambil.',
            'data' => $menus,
        ]);
    }

    /**
     * Menambahkan item menu baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'nullable|boolean',
        ]);

        $menu = Menu::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
            'is_available' => $validated['is_available'] ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu baru berhasil ditambahkan.',
            'data' => $menu->load('category'),
        ], 201);
    }

    /**
     * Menampilkan detail satu item menu.
     */
    public function show(int $id): JsonResponse
    {
        $menu = Menu::with('category')->find($id);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail menu berhasil diambil.',
            'data' => $menu,
        ]);
    }

    /**
     * Memperbarui informasi item menu.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'sometimes|required|boolean',
        ]);

        $menu->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil diperbarui.',
            'data' => $menu->load('category'),
        ]);
    }

    /**
     * Menghapus item menu dari database.
     */
    public function destroy(int $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil dihapus.',
            'data' => null,
        ]);
    }

    /**
     * Mengubah status ketersediaan stok menu (ready / habis).
     */
    public function updateStock(Request $request, int $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $menu->update(['is_available' => $validated['is_available']]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status ketersediaan stok menu berhasil diubah.',
            'data' => $menu,
        ]);
    }
}
