# 🤖 Agents — Aturan Kolaborasi AI untuk Project Kantingo

> Dokumen ini mengatur bagaimana tim dan AI assistant berkolaborasi dalam pengembangan project **Kantingo**.

---

## 1. Aturan Kolaborasi AI

### 1.1 Peran AI dalam Pengembangan

- AI assistant digunakan sebagai **alat bantu** (co-pilot), bukan pengganti developer.
- Semua output yang dihasilkan AI **wajib di-review** oleh anggota tim sebelum di-merge.
- Developer bertanggung jawab penuh atas kode yang di-commit, meskipun dibantu AI.

### 1.2 Penggunaan AI yang Diperbolehkan

| ✅ Diperbolehkan | ❌ Tidak Diperbolehkan |
|---|---|
| Generate boilerplate code | Commit langsung tanpa review |
| Bantu debugging dan refactoring | Menggunakan AI untuk bypass code review |
| Menulis unit test | Copy-paste output AI tanpa memahami kodenya |
| Dokumentasi dan komentar kode | Mengandalkan AI untuk keputusan arsitektur kritis |
| Brainstorming solusi teknis | Mengabaikan best practice demi kecepatan |

### 1.3 Etika Penggunaan AI

- Selalu **verifikasi** output AI terhadap spesifikasi project.
- Jangan gunakan AI untuk menghasilkan kode yang melanggar lisensi atau hak cipta.
- Cantumkan keterangan jika sebagian besar kode dihasilkan dengan bantuan AI (dalam commit message atau PR description).

---

## 2. Prinsip Pengembangan Kode yang Bersih (Clean Code)

### 2.1 Penamaan (Naming Conventions)

- **Variabel & Fungsi**: Gunakan `camelCase` (Dart/Flutter) dan `snake_case` (PHP/Laravel).
- **Class**: Gunakan `PascalCase` di kedua stack.
- **Konstanta**: Gunakan `UPPER_SNAKE_CASE`.
- Nama harus **deskriptif** dan mencerminkan tujuannya.

```dart
// ✅ Baik
final String menuItemName = 'Nasi Goreng';
double calculateTotalPrice(List<CartItem> items) { ... }

// ❌ Buruk
final String n = 'Nasi Goreng';
double calc(List<CartItem> x) { ... }
```

```php
// ✅ Baik
$menu_items = MenuItem::where('is_available', true)->get();
public function calculate_total_price(): float { ... }

// ❌ Buruk
$m = MenuItem::where('is_available', true)->get();
public function calc(): float { ... }
```

### 2.2 Struktur Fungsi

- Setiap fungsi harus melakukan **satu hal saja** (Single Responsibility).
- Maksimal **30 baris** per fungsi. Jika lebih, refactor menjadi fungsi-fungsi kecil.
- Hindari nested conditions lebih dari **3 level**.

### 2.3 Komentar & Dokumentasi

- Tulis komentar untuk **menjelaskan "mengapa"**, bukan "apa".
- Gunakan **docblock** pada setiap class dan public method.
- Hapus komentar yang sudah tidak relevan (dead comments).

### 2.4 Error Handling

- Selalu gunakan **try-catch** untuk operasi yang bisa gagal (API call, database query).
- Jangan pernah menelan exception secara diam-diam (silent catch).
- Log error dengan informasi yang cukup untuk debugging.

### 2.5 DRY (Don't Repeat Yourself)

- Identifikasi kode yang duplikat dan ekstrak menjadi fungsi/class reusable.
- Gunakan **mixin**, **trait**, atau **helper** untuk logika yang dipakai di banyak tempat.

---

## 3. Git Safety Net — Aturan Keamanan Git

### 3.1 ⛔ Larangan Keras

> **DILARANG menggunakan `git push --force` atau `git push -f` pada branch shared!**

Branch shared yang dilindungi:

| Branch | Status | Keterangan |
|---|---|---|
| `main` | 🔒 **Protected** | Branch production, hanya menerima merge dari `dev` |
| `dev` | 🔒 **Protected** | Branch development utama, hanya menerima merge dari `feature/*` |

```bash
# ❌ DILARANG — Bisa menghapus commit anggota tim lain!
git push --force origin main
git push -f origin dev
git push --force-with-lease origin main

# ✅ DIPERBOLEHKAN — Force push hanya di branch pribadi
git push --force origin feature/nama-fitur
```

### 3.2 Aturan Branching

```
main (production)
 └── dev (development)
      ├── feature/auth-login
      ├── feature/menu-management
      ├── feature/cart-checkout
      └── feature/order-history
```

- Selalu buat branch baru dari `dev` untuk setiap fitur.
- Format nama branch: `feature/<nama-fitur>` (gunakan kebab-case).
- Setelah selesai, buat **Pull Request** ke `dev` untuk di-review.
- Branch `main` hanya di-update melalui merge dari `dev` saat release.

### 3.3 Aturan Commit Message

Gunakan format **Conventional Commits**:

```
<type>(<scope>): <deskripsi singkat>

[body opsional]

[footer opsional]
```

**Tipe commit yang digunakan:**

| Type | Keterangan | Contoh |
|---|---|---|
| `feat` | Fitur baru | `feat(menu): tambah fitur pencarian menu` |
| `fix` | Perbaikan bug | `fix(cart): perbaiki kalkulasi total harga` |
| `docs` | Perubahan dokumentasi | `docs(readme): update panduan instalasi` |
| `style` | Formatting (tanpa perubahan logika) | `style(auth): rapikan indentasi kode login` |
| `refactor` | Refactoring kode | `refactor(order): pisahkan logic order service` |
| `test` | Menambah atau memperbaiki test | `test(menu): tambah unit test CRUD menu` |
| `chore` | Maintenance (config, deps, dll) | `chore(deps): update Flutter dependencies` |

### 3.4 Sebelum Push, Wajib:

1. ✅ `git pull origin dev` — Pastikan branch up-to-date.
2. ✅ `git diff` — Review perubahan sebelum commit.
3. ✅ Jalankan test (jika ada) — Pastikan tidak ada yang broken.
4. ✅ Resolve conflict secara lokal — Jangan push kode yang conflict.

### 3.5 Jika Terjadi Kesalahan

- **Commit salah?** Gunakan `git revert <hash>` (buat commit baru yang membatalkan).
- **Belum di-push?** Gunakan `git reset --soft HEAD~1` untuk undo commit terakhir.
- **Jangan pernah** menggunakan `git reset --hard` di branch shared.

---

## 4. Checklist Sebelum Pull Request

- [ ] Kode sudah di-review secara mandiri (self-review)
- [ ] Tidak ada `console.log`, `print()`, atau `dd()` yang tertinggal
- [ ] Semua fungsi memiliki dokumentasi/docblock
- [ ] Nama variabel dan fungsi sudah deskriptif
- [ ] Tidak ada kode yang duplikat
- [ ] Sudah test secara lokal dan berjalan normal
- [ ] Commit message mengikuti format Conventional Commits
- [ ] Branch sudah up-to-date dengan `dev`

---

*Dokumen ini berlaku untuk seluruh anggota tim Kantingo. Terakhir diperbarui: Oktober 2026.*

