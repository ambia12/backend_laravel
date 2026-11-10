# 🎬 Panduan Video Git — Simulasi Workflow Kantingo

> Instruksi langkah-langkah komplit untuk pembuatan video unlisted ±3 menit.  
> Mencakup: pembuatan branch, commit, dan simulasi rollback.

---

## 🎯 Tujuan Video

Mendemonstrasikan workflow Git project Kantingo:
1. Membuat branch `dev` dari `main`
2. Membuat branch fitur `feature/tambah-fitur-menu`
3. Melakukan commit dengan format Conventional Commits
4. Simulasi rollback menggunakan `git revert` dan `git reset`

---

## Persiapan Sebelum Rekam (±30 detik)

```bash
# Pastikan sudah di folder project
cd tugas-pertemuan-5

# Inisialisasi Git (jika belum)
git init

# Set identitas Git
git config user.name "Nama Anda"
git config user.email "email@example.com"

# Tambahkan semua file dan buat commit awal
git add .
git commit -m "chore: initial project setup Kantingo"
```

> 💡 **Catatan:** Langkah persiapan ini bisa dilakukan sebelum rekaman dimulai.

---

## Bagian 1 — Membuat Branch `dev` (±30 detik)

**Narasi:** *"Pertama, kita buat branch `dev` sebagai branch development utama."*

```bash
# 1. Pastikan kita di branch main
git branch
# Output: * main

# 2. Buat branch dev dari main
git checkout -b dev

# 3. Verifikasi branch aktif
git branch
# Output:
#   main
# * dev

# 4. (Opsional) Push branch dev ke remote
# git push -u origin dev
```

**Yang ditampilkan di layar:** Terminal menunjukkan pembuatan branch `dev` dan output `git branch`.

---

## Bagian 2 — Membuat Branch Fitur & Commit (±1 menit)

**Narasi:** *"Selanjutnya, dari branch `dev` kita buat branch fitur baru untuk menambahkan fitur menu."*

```bash
# 1. Buat branch fitur dari dev
git checkout -b feature/tambah-fitur-menu

# 2. Verifikasi posisi branch
git branch
# Output:
#   main
#   dev
# * feature/tambah-fitur-menu
```

### Simulasi Perubahan Kode

```bash
# 3. Buat file contoh untuk fitur menu
echo "<?php
// MenuController.php - Fitur CRUD Menu Kantingo
namespace App\Http\Controllers;

class MenuController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => []]);
    }
}" > contoh_menu_controller.php

# 4. Cek status perubahan
git status
# Output: Untracked files: contoh_menu_controller.php

# 5. Stage perubahan
git add contoh_menu_controller.php

# 6. Commit dengan format Conventional Commits
git commit -m "feat(menu): tambah MenuController dengan endpoint index"
```

### Tambahkan Commit Kedua (untuk simulasi rollback nanti)

```bash
# 7. Simulasi perubahan yang mengandung error
echo "KODE ERROR - INI SENGAJA SALAH" >> contoh_menu_controller.php

# 8. Commit perubahan yang salah
git add contoh_menu_controller.php
git commit -m "feat(menu): tambah fitur pencarian menu"

# 9. Lihat history commit
git log --oneline
# Output:
# abc1234 feat(menu): tambah fitur pencarian menu       <-- commit salah
# def5678 feat(menu): tambah MenuController dengan endpoint index
# ghi9012 chore: initial project setup Kantingo
```

**Yang ditampilkan di layar:** Terminal menunjukkan pembuatan file, `git status`, `git add`, `git commit`, dan `git log`.

---

## Bagian 3 — Simulasi Rollback (±1 menit)

**Narasi:** *"Ternyata commit terakhir mengandung error. Kita punya dua cara untuk rollback."*

### Cara 1: `git revert` (Aman untuk branch shared)

**Narasi:** *"Cara pertama, `git revert` — membuat commit baru yang membatalkan perubahan. Ini cara yang AMAN dan disarankan."*

```bash
# 1. Revert commit terakhir (yang error)
git revert HEAD

# Akan muncul editor untuk pesan commit revert.
# Simpan dan tutup editor (biasanya :wq di vim, atau Ctrl+S lalu tutup).

# 2. Lihat hasilnya
git log --oneline
# Output:
# xyz7890 Revert "feat(menu): tambah fitur pencarian menu"  <-- commit revert
# abc1234 feat(menu): tambah fitur pencarian menu
# def5678 feat(menu): tambah MenuController dengan endpoint index
# ghi9012 chore: initial project setup Kantingo

# 3. Verifikasi isi file sudah kembali normal
cat contoh_menu_controller.php
```

> ✅ **Kelebihan `git revert`:** History tetap lengkap, aman dipakai di branch shared (`dev`, `main`).

### Cara 2: `git reset` (Hanya untuk branch pribadi)

**Narasi:** *"Cara kedua, `git reset` — menghapus commit dari history. HANYA boleh dipakai di branch pribadi (feature branch), DILARANG di branch `main` atau `dev`."*

```bash
# Untuk demo, kita buat commit salah lagi
echo "KODE ERROR LAGI" >> contoh_menu_controller.php
git add .
git commit -m "fix(menu): perbaikan yang ternyata salah juga"

# 1. Reset soft — undo commit tapi perubahan tetap di staging
git reset --soft HEAD~1

# 2. Cek status
git status
# Output: Changes to be committed: modified contoh_menu_controller.php

# 3. Buang perubahan dari staging
git restore --staged contoh_menu_controller.php
git restore contoh_menu_controller.php

# 4. Verifikasi history bersih
git log --oneline
```

> ⚠️ **PERINGATAN:** `git reset` mengubah history! **DILARANG** digunakan di branch `main` atau `dev` sesuai aturan `agents.md`.

---

## Penutup Video (±15 detik)

**Narasi:** *"Jadi, untuk rollback di project Kantingo:"*

```
┌─────────────────────────────────────────────────────────┐
│  RINGKASAN ROLLBACK                                     │
│                                                         │
│  ✅ git revert  → Aman untuk SEMUA branch               │
│     Membuat commit baru, history tetap lengkap          │
│                                                         │
│  ⚠️  git reset   → HANYA untuk branch pribadi (feature) │
│     Mengubah history, DILARANG di main/dev              │
│                                                         │
│  ❌ git push --force → DILARANG di main/dev             │
│     Bisa menghapus commit anggota tim lain!             │
└─────────────────────────────────────────────────────────┘
```

---

## 📋 Checklist Sebelum Upload Video

- [ ] Video berdurasi ±3 menit
- [ ] Terminal terlihat jelas (font cukup besar)
- [ ] Setiap command diketik/ditunjukkan dengan jelas
- [ ] Output Git terlihat di layar
- [ ] Video di-upload sebagai **unlisted** di YouTube
- [ ] Link video dicantumkan dalam laporan

---

*Panduan ini dibuat untuk membantu pembuatan video demonstrasi Git workflow project Kantingo.*

