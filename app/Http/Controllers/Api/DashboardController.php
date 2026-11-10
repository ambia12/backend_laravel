<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Menampilkan ringkasan data statistik kantin untuk dashboard pengelola/kasir.
     */
    public function summary(): JsonResponse
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_price');
        $activeOrders = Order::whereIn('order_status', ['pending', 'cooking', 'ready'])->count();

        $occupiedTables = Table::where('status', 'occupied')->count();
        $availableTables = Table::where('status', 'available')->count();

        $totalMenus = Menu::count();
        $availableMenus = Menu::where('is_available', true)->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Ringkasan dashboard kantin berhasil diambil.',
            'data' => [
                'orders' => [
                    'total' => $totalOrders,
                    'active' => $activeOrders,
                    'total_revenue' => (int) $totalRevenue,
                ],
                'tables' => [
                    'occupied' => $occupiedTables,
                    'available' => $availableTables,
                    'total' => $occupiedTables + $availableTables,
                ],
                'menus' => [
                    'total' => $totalMenus,
                    'available' => $availableMenus,
                    'sold_out' => $totalMenus - $availableMenus,
                ],
            ],
        ]);
    }
}
