<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan dengan filter status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['table', 'items.menu'])->latest();

        if ($request->has('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        $orders = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pesanan berhasil diambil.',
            'data' => $orders,
        ]);
    }

    /**
     * Membuat pesanan baru dari checkout pelanggan di meja.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemInput) {
                $menu = Menu::find($itemInput['menu_id']);

                if (!$menu || !$menu->is_available) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Menu "' . ($menu->name ?? 'ID: ' . $itemInput['menu_id']) . '" sedang tidak tersedia.',
                        'data' => null,
                    ], 400);
                }

                $subtotal = $menu->price * $itemInput['quantity'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $itemInput['quantity'],
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'table_id' => $validated['table_id'],
                'customer_name' => $validated['customer_name'],
                'total_price' => $totalPrice,
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
            ]);

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Ubah status meja menjadi occupied
            Table::where('id', $validated['table_id'])->update(['status' => 'occupied']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $order->load(['table', 'items.menu']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with(['table', 'items.menu'])->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pesanan berhasil diambil.',
            'data' => $order,
        ]);
    }

    /**
     * Memperbarui status pengerjaan pesanan (pending, cooking, ready, completed).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validate([
            'order_status' => 'required|in:pending,cooking,ready,completed',
        ]);

        $order->update(['order_status' => $validated['order_status']]);

        // Jika selesai, meja bisa dikembalikan ke available jika tidak ada pesanan aktif lain
        if ($validated['order_status'] === 'completed') {
            $hasActiveOrders = Order::where('table_id', $order->table_id)
                ->where('id', '!=', $order->id)
                ->whereIn('order_status', ['pending', 'cooking', 'ready'])
                ->exists();

            if (!$hasActiveOrders) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesanan berhasil diperbarui.',
            'data' => $order->fresh(),
        ]);
    }

    /**
     * Membatalkan atau menghapus pesanan.
     */
    public function destroy(int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $order->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan berhasil dihapus.',
            'data' => null,
        ]);
    }

    /**
     * Memproses pelunasan pembayaran pesanan (QRIS / Tunai Kasir).
     */
    public function payOrder(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|in:QRIS,cash,transfer',
        ]);

        $order->update([
            'payment_status' => 'paid',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran pesanan berhasil dikonfirmasi.',
            'data' => [
                'order_id' => $order->id,
                'payment_status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'total_price' => $order->total_price,
            ],
        ]);
    }
}
