<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Meja (Tables)
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number'); // Nomor meja (cth: 1, 2, VIP-1)
            $table->string('qr_code_token')->unique(); // Token unik untuk QR code
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->timestamps();
        });

        // 2. Tabel Kategori Menu (Categories)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cth: Makanan Berat, Minuman
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 3. Tabel Menu Makanan/Minuman (Menus)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name'); // Cth: Mie Setan, Es Teh
            $table->integer('price'); // Harga produk
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true); // Stok ada atau habis
            $table->timestamps();
        });

        // 4. Tabel Pesanan Utama (Orders)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->integer('total_price');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('order_status', ['pending', 'cooking', 'ready', 'completed'])->default('pending');
            $table->timestamps();
        });

        // 5. Tabel Detail Item Pesanan (Order Items)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('price'); // Harga saat dipesan
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tables');
    }
};