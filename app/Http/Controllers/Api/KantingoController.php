<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KantingoController extends Controller
{
    // 1. Ambil Semua Daftar Menu & Kategori
    public function getMenus()
    {
        $categories = DB::table('categories')->get();
        $menus = DB::table('menus')->where('is_available', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data menu',
            'data' => [
                'categories' => $categories,
                'menus' => $menus
            ]
        ], 200);
    }

    // 2. Ambil Daftar Meja
    public function getTables()
    {
        $tables = DB::table('tables')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data meja',
            'data' => $tables
        ], 200);
    }

    // 3. Proses Pesanan Baru (Checkout dari Frontend / Flutter)
    public function storeOrder(Request $request)
    {
        // Validasi data yang dikirim dari Flutter
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array', // Berisi array menu_id dan quantity
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            $orderItemsData = [];

            // Hitung total harga dan siapkan data item pesanan
            foreach ($request->items as $item) {
                $menu = DB::table('menus')->where('id', $item['menu_id'])->first();
                
                if (!$menu || !$menu->is_available) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu tidak tersedia atau sudah habis.'
                    ], 400);
                }

                $subtotal = $menu->price * $item['quantity'];
                $totalPrice += $subtotal;

                $orderItemsData[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Simpan ke tabel orders (Pesanan Utama)
            $orderId = DB::table('orders')->insertGetId([
                'table_id' => $request->table_id,
                'customer_name' => $request->customer_name,
                'total_price' => $totalPrice,
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Masukkan order_id ke setiap item pesanan, lalu simpan ke database
            foreach ($orderItemsData as &$orderItem) {
                $orderItem['order_id'] = $orderId;
            }
            unset($orderItem);

            DB::table('order_items')->insert($orderItemsData);

            // Ubah status meja menjadi 'occupied' (terisi)
            DB::table('tables')->where('id', $request->table_id)->update([
                'status' => 'occupied',
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => [
                    'order_id' => $orderId,
                    'total_price' => $totalPrice,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }
}