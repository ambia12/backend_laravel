# 📋 Project Guidelines — Kantingo

> Panduan teknis dan standar pengembangan untuk project **Kantingo** — Aplikasi Kantin Digital berbasis Laravel (Backend) dan Flutter (Frontend).

---

## 1. Tech Stack

### 1.1 Backend — Laravel (PHP)

| Komponen | Teknologi | Versi |
|---|---|---|
| **Framework** | Laravel | 11.x |
| **Bahasa** | PHP | 8.2+ |
| **Database** | MySQL / MariaDB | 8.0+ / 10.6+ |
| **API Format** | RESTful JSON API | — |
| **Authentication** | Laravel Sanctum | — |
| **Package Manager** | Composer | 2.x |

**Struktur Direktori Backend (`backend_laravel/`):**

```
backend_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # API Controllers
│   │   ├── Middleware/      # Custom Middleware
│   │   └── Requests/        # Form Request Validation
│   ├── Models/              # Eloquent Models
│   └── Services/            # Business Logic Layer
├── database/
│   ├── migrations/          # Database Migrations
│   └── seeders/             # Data Seeders
├── routes/
│   └── api.php              # API Route Definitions
├── config/                  # Configuration Files
├── tests/                   # PHPUnit Tests
└── docs/                    # Backend Documentation
```

### 1.2 Frontend — Flutter (Dart)

| Komponen | Teknologi | Versi |
|---|---|---|
| **Framework** | Flutter | 3.x |
| **Bahasa** | Dart | 3.x |
| **State Management** | Provider / Riverpod | Latest |
| **HTTP Client** | Dio / http | Latest |
| **Local Storage** | SharedPreferences | Latest |
| **Navigation** | Go Router / Navigator 2.0 | Latest |

**Struktur Direktori Frontend (`frontend_flutter/`):**

```
frontend_flutter/
├── lib/
│   ├── main.dart            # Entry Point
│   ├── models/              # Data Models
│   ├── screens/             # UI Screens/Pages
│   ├── widgets/             # Reusable Widgets
│   ├── services/            # API Service Layer
│   ├── providers/           # State Management
│   ├── utils/               # Helper/Utility Functions
│   └── constants/           # App Constants (Colors, Strings, etc.)
├── assets/                  # Images, Fonts, etc.
├── test/                    # Widget & Unit Tests
└── pubspec.yaml             # Dependencies
```

### 1.3 Arsitektur Komunikasi

```
┌──────────────────┐         HTTP/REST          ┌──────────────────┐
│                  │  ◄─────────────────────►   │                  │
│  Flutter App     │      JSON Request/         │  Laravel API     │
│  (Frontend)      │      Response              │  (Backend)       │
│                  │                            │                  │
└──────────────────┘                            └──────────┬───────┘
                                                           │
                                                           ▼
                                                ┌──────────────────┐
                                                │                  │
                                                │  MySQL Database  │
                                                │                  │
                                                └──────────────────┘
```

---

## 2. Struktur Branch Git

### 2.1 Branch Utama

| Branch | Tujuan | Siapa yang Merge |
|---|---|---|
| `main` | **Production-ready** code. Hanya berisi kode yang sudah stabil dan siap deploy. | Lead / setelah review tim |
| `dev` | **Development** branch. Tempat integrasi semua fitur sebelum ke production. | Semua anggota (via PR) |

### 2.2 Branch Fitur

| Pola | Tujuan | Contoh |
|---|---|---|
| `feature/<nama-fitur>` | Pengembangan fitur baru | `feature/auth-login` |
| `fix/<nama-bug>` | Perbaikan bug | `fix/cart-total-calculation` |
| `hotfix/<nama-issue>` | Perbaikan mendesak di production | `hotfix/payment-crash` |

### 2.3 Alur Kerja (Git Workflow)

```
1. Mulai dari branch `dev`
   └── git checkout dev
   └── git pull origin dev

2. Buat branch fitur baru
   └── git checkout -b feature/nama-fitur

3. Kerjakan fitur, commit secara berkala
   └── git add .
   └── git commit -m "feat(scope): deskripsi"

4. Push branch fitur
   └── git push origin feature/nama-fitur

5. Buat Pull Request ke `dev`
   └── Minta review dari minimal 1 anggota tim

6. Setelah di-approve, merge ke `dev`
   └── Hapus branch fitur setelah merge

7. Release ke `main`
   └── Merge `dev` → `main` saat fitur sudah stabil
```

### 2.4 Visualisasi Branch

```
main ──────────●──────────────────────●──── (release)
               │                      ▲
               ▼                      │
dev  ──────────●────●────●────●───────●──── (development)
                    │    ▲    │       ▲
                    ▼    │    ▼       │
feature/auth ───────●────┘    │       │
                              │       │
feature/menu ─────────────────●───────┘
```

---

## 3. Standar Koding

### 3.1 Laravel / PHP

#### Penamaan

| Elemen | Konvensi | Contoh |
|---|---|---|
| Controller | PascalCase + `Controller` suffix | `MenuController` |
| Model | PascalCase, singular | `MenuItem` |
| Migration | snake_case, deskriptif | `create_menu_items_table` |
| Method | camelCase | `getAvailableMenus()` |
| Variable | snake_case (PHP) / camelCase | `$menu_items` |
| Route | kebab-case, plural | `/api/menu-items` |
| Table | snake_case, plural | `menu_items` |

#### Contoh Controller yang Baik

```php
<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Http\Requests\StoreMenuItemRequest;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu yang tersedia.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $menu_items = MenuItem::where('is_available', true)
            ->orderBy('category')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $menu_items,
        ]);
    }

    /**
     * Menyimpan menu baru.
     *
     * @param StoreMenuItemRequest $request
     * @return JsonResponse
     */
    public function store(StoreMenuItemRequest $request): JsonResponse
    {
        $menu_item = MenuItem::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => $menu_item,
        ], 201);
    }
}
```

#### API Response Format

Semua response API mengikuti format konsisten:

```json
{
    "status": "success",
    "message": "Deskripsi opsional",
    "data": { }
}
```

Untuk error:

```json
{
    "status": "error",
    "message": "Deskripsi error",
    "errors": {
        "field_name": ["Pesan validasi"]
    }
}
```

### 3.2 Flutter / Dart

#### Penamaan

| Elemen | Konvensi | Contoh |
|---|---|---|
| Class | PascalCase | `MenuItemCard` |
| File | snake_case | `menu_item_card.dart` |
| Variable | camelCase | `menuItemList` |
| Constant | lowerCamelCase / UPPER_SNAKE_CASE | `defaultPadding` |
| Function | camelCase | `fetchMenuItems()` |
| Enum | PascalCase | `OrderStatus.pending` |

#### Contoh Widget yang Baik

```dart
import 'package:flutter/material.dart';
import '../models/menu_item.dart';

/// Widget card untuk menampilkan satu item menu.
///
/// Menampilkan nama, harga, dan gambar menu item.
/// Menerima callback [onTap] untuk handling interaksi.
class MenuItemCard extends StatelessWidget {
  final MenuItem menuItem;
  final VoidCallback? onTap;

  const MenuItemCard({
    super.key,
    required this.menuItem,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(12.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                menuItem.name,
                style: Theme.of(context).textTheme.titleMedium,
              ),
              const SizedBox(height: 4),
              Text(
                'Rp ${menuItem.price.toStringAsFixed(0)}',
                style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                      color: Colors.green,
                      fontWeight: FontWeight.bold,
                    ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
```

#### Aturan Import

Urutkan import dalam 3 grup (pisahkan dengan baris kosong):

```dart
// 1. Dart SDK
import 'dart:async';
import 'dart:convert';

// 2. Package eksternal
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

// 3. File internal project
import '../models/menu_item.dart';
import '../services/api_service.dart';
```

### 3.3 Aturan Umum Kedua Stack

| Aturan | Keterangan |
|---|---|
| **Indentasi** | 2 spasi (Dart) / 4 spasi (PHP) |
| **Panjang baris** | Maksimal 120 karakter |
| **Trailing comma** | Wajib di Dart untuk formatting otomatis |
| **Semicolon** | Wajib di PHP, tidak dipakai di Dart |
| **String quote** | Single quote (`'`) di Dart, single quote (`'`) di PHP |
| **Null safety** | Wajib di Dart (gunakan `?`, `!`, `??` dengan benar) |

---

## 4. Environment & Setup

### 4.1 Backend Setup

```bash
# Clone repository
cd backend_laravel

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Jalankan server
php artisan serve
```

### 4.2 Frontend Setup

```bash
# Clone repository
cd frontend_flutter

# Install dependencies
flutter pub get

# Jalankan aplikasi
flutter run
```

### 4.3 Environment Variables

> ⚠️ **PENTING**: File `.env` TIDAK boleh di-commit ke repository. Pastikan sudah masuk di `.gitignore`.

---

## 5. Code Review Checklist

Sebelum approve Pull Request, pastikan:

- [ ] Kode mengikuti standar penamaan yang telah ditentukan
- [ ] Tidak ada hardcoded values (gunakan constants/env)
- [ ] Error handling sudah diterapkan dengan benar
- [ ] Tidak ada kode yang tidak terpakai (dead code)
- [ ] Fungsi memiliki docblock/dokumentasi
- [ ] Tidak ada credentials atau secret yang terekspos
- [ ] Response API mengikuti format standar
- [ ] Widget Flutter menggunakan `const` constructor jika memungkinkan

---

*Dokumen ini adalah panduan wajib untuk seluruh anggota tim Kantingo. Terakhir diperbarui: Oktober 2026.*

