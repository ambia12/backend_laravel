# 📑 ARTEFAK 03 — API CONTRACT DOKUMENTASI LENGKAP
# KANTINGO RESTFUL API (v1.0)

> **Nama Sistem:** Kantingo — Aplikasi Pemesanan Mandiri & Manajemen Kantin Digital  
> **Tech Stack Backend:** Laravel 11.x (RESTful JSON API)  
> **Klien Konsumen:** Flutter Mobile App & Web Dashboard Admin  
> **Format Data:** JSON (`application/json`)  
> **Base URL Development:**  
> - Android Emulator: `http://10.0.2.2:8000/api/v1`  
> - Localhost PC / Web: `http://127.0.0.1:8000/api/v1`  
> **Base URL Production:** `https://api.kantingo.my.id/api/v1`  
> **Terakhir Diperbarui:** November 2026

---

## 1. Standar Desain API & Format Data

### 1.1 HTTP Headers Standar
Setiap permintaan ke API wajib menyertakan header berikut:
```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <personal_access_token> # (Wajib untuk endpoint dengan autentikasi)
```

### 1.2 Format Respons JSON Terstandar

#### A. Respons Sukses (`HTTP 200 OK` / `HTTP 201 Created`)
```json
{
  "status": "success",
  "message": "Deskripsi singkat keberhasilan proses.",
  "data": {}
}
```

#### B. Respons Error Validasi (`HTTP 422 Unprocessable Entity`)
```json
{
  "status": "error",
  "message": "Data yang dikirimkan tidak valid.",
  "errors": {
    "field_name": [
      "Pesan spesifik error validasi atribut."
    ]
  }
}
```

#### C. Respons Error Akses & Server (`HTTP 400`, `401`, `403`, `404`, `500`)
```json
{
  "status": "error",
  "message": "Pesan rincian kegagalan akses atau kendala server.",
  "data": null
}
```

---

## 2. Tabel Ringkasan Endpoint Utama (Master Endpoint Matrix)

| No | Nama Fitur / Modul | Method | Endpoint URL | Auth? | Deskripsi Singkat |
|:--:|---|:---:|---|:---:|---|
| 1 | **Login Pengguna** | `POST` | `/api/v1/auth/login` | Publik | Autentikasi kasir/admin/civitas |
| 2 | **Get User Profile** | `GET` | `/api/v1/auth/profile` | Bearer | Mengambil profil user yang sedang login |
| 3 | **Logout Pengguna** | `POST` | `/api/v1/auth/logout` | Bearer | Mencabut token akses sesi aktif |
| 4 | **Get All Tables** | `GET` | `/api/v1/tables` | Publik | Mendapatkan seluruh daftar meja & status |
| 5 | **Scan QR Meja** | `GET` | `/api/v1/tables/scan/{qr_token}` | Publik | Validasi keaslian meja via token QR |
| 6 | **Get Products / Menus** | `GET` | `/api/v1/menus` | Publik | Katalog menu dengan pencarian & filter |
| 7 | **Get Detail Menu** | `GET` | `/api/v1/menus/{id}` | Publik | Rincian porsi, level pedas, dan topping |
| 8 | **Create Product / Menu** | `POST` | `/api/v1/menus` | Admin | Menambah item menu baru ke katalog |
| 9 | **Update Menu** | `PUT` | `/api/v1/menus/{id}` | Admin | Mengubah data menu & ketersediaan stok |
| 10 | **Delete Menu** | `DELETE`| `/api/v1/menus/{id}` | Admin | Menghapus menu dari sistem kantin |
| 11 | **Create Order (Checkout)**| `POST`| `/api/v1/orders` | Publik | Pemesanan mandiri customer dari meja |
| 12 | **Get Order Detail** | `GET` | `/api/v1/orders/{id}` | Publik | Cek status live pesanan & rincian nota |
| 13 | **Get All Orders (Antrean)**| `GET`| `/api/v1/orders` | Kasir/Dapur | Antrean pesanan aktif di kasir & dapur |
| 14 | **Update Order Status** | `PATCH`| `/api/v1/orders/{id}/status`| Kasir/Dapur | Perubahan status: cooking, ready, completed |
| 15 | **Payment Settlement** | `POST` | `/api/v1/orders/{id}/payment`| Publik | Penyelesaian bayar via QRIS atau kasir |

---

## 3. Dokumentasi Spesifikasi Endpoint Lengkap

### MODUL 1: AUTENTIKASI & PROFIL PENGGUNA

---

#### 1. Login Pengguna (`POST /api/v1/auth/login`)
- **Fungsi:** Mengotentikasi akun admin, kasir, atau pengguna untuk memperoleh token akses API (Laravel Sanctum).
- **HTTP Method:** `POST`
- **Endpoint URL:** `/api/v1/auth/login`
- **Hak Akses:** Publik
- **Request Body:**
```json
{
  "email": "kasir@kantingo.com",
  "password": "password123"
}
```
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Autentikasi berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Siti Nurhaliza",
      "email": "kasir@kantingo.com",
      "role": "cashier"
    },
    "token": "1|qW8eR9tY0uI1oP2aS3dF4gH5jK6lZ7x8cV9bN0m"
  }
}
```
- **Response Error (HTTP 422 Unprocessable Entity):**
```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": {
    "email": ["Email tidak valid atau belum terdaftar."],
    "password": ["Kata sandi wajib diisi."]
  }
}
```

---

#### 2. Get User Profile (`GET /api/v1/auth/profile`)
- **Fungsi:** Mengambil informasi akun dari token Bearer pengguna yang sedang login.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/auth/profile`
- **Hak Akses:** Wajib Login (`Authorization: Bearer <token>`)
- **Request Body:** *None*
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Data profil berhasil diambil.",
  "data": {
    "id": 1,
    "name": "Siti Nurhaliza",
    "email": "kasir@kantingo.com",
    "role": "cashier",
    "created_at": "2026-10-28T10:00:00Z"
  }
}
```
- **Response Error (HTTP 401 Unauthorized):**
```json
{
  "status": "error",
  "message": "Unauthenticated. Sesi login telah berakhir atau token tidak valid.",
  "data": null
}
```

---

#### 3. Logout Pengguna (`POST /api/v1/auth/logout`)
- **Fungsi:** Menghapus token autentikasi pengguna saat keluar dari aplikasi.
- **HTTP Method:** `POST`
- **Endpoint URL:** `/api/v1/auth/logout`
- **Hak Akses:** Wajib Login (`Authorization: Bearer <token>`)
- **Request Body:** *None*
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Logout berhasil. Token telah dicabut.",
  "data": null
}
```
- **Response Error (HTTP 401 Unauthorized):**
```json
{
  "status": "error",
  "message": "Unauthenticated.",
  "data": null
}
```

---

### MODUL 2: MEJA & SCAN QR CODE

---

#### 4. Get All Tables (`GET /api/v1/tables`)
- **Fungsi:** Mengambil semua data meja kantin beserta status ketersediaannya.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/tables`
- **Hak Akses:** Publik
- **Query Parameters:**
  - `status` (string, opsional): `available` atau `occupied`
- **Response Success (HTTP 200 OK):**
  ```json
  {
    "status": "success",
    "message": "Daftar meja berhasil dimuat.",
    "data": [
      {
        "id": 1,
        "table_number": "01",
        "status": "available",
        "qr_code_token": "KTG-TBL-01-A9F2"
      },
      {
        "id": 8,
        "table_number": "08",
        "status": "occupied",
        "qr_code_token": "KTG-TBL-08-C4B7"
      }
    ]
  }
  ```

---

#### 5. Scan QR Meja Customer (`GET /api/v1/tables/scan/{qr_token}`)
- **Fungsi:** Memvalidasi stiker QR fisik yang discan oleh customer di meja kantin.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/tables/scan/{qr_token}`
- **Contoh URL:** `/api/v1/tables/scan/KTG-TBL-08-C4B7`
- **Hak Akses:** Publik
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Meja terverifikasi dan siap digunakan.",
  "data": {
    "table_id": 8,
    "table_number": "08",
    "status": "available",
    "qr_token": "KTG-TBL-08-C4B7"
  }
}
```
- **Response Error (HTTP 404 Not Found):**
```json
{
  "status": "error",
  "message": "QR Code meja tidak valid atau tidak terdaftar.",
  "data": null
}
```

---

### MODUL 3: KATALOG MENU & PRODUK

---

#### 6. Get Products / Menus (`GET /api/v1/menus`)
- **Fungsi:** Menampilkan katalog hidangan dan minuman beserta filter kategori.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/menus`
- **Hak Akses:** Publik
- **Query Parameters:**
  - `category` (string, opsional): filter slug kategori (cth: `mie-makanan`, `minuman-segar`)
  - `search` (string, opsional): pencarian nama menu
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Katalog menu berhasil dimuat.",
  "data": {
    "categories": [
      { "id": 1, "name": "Mie & Makanan", "slug": "mie-makanan" },
      { "id": 2, "name": "Dimsum & Side", "slug": "dimsum-side" },
      { "id": 3, "name": "Minuman Segar", "slug": "minuman-segar" }
    ],
    "menus": [
      {
        "id": 101,
        "category_id": 1,
        "name": "Mie Pedas Kantingo Level 1-4",
        "price": 13000,
        "description": "Mie gurih pedas khas racikan cabai segar dengan topping ayam dan pangsit.",
        "image_url": "https://cdn.kantingo.com/images/mie_pedas.jpg",
        "estimated_time": "8-10 Menit",
        "rating": 4.9,
        "is_available": true,
        "spice_levels": ["Level 0 (Gurih)", "Level 1", "Level 2", "Level 3", "Level 4"],
        "toppings": ["Pangsit Goreng", "Siomay Kukus", "Ekstra Bawang"]
      },
      {
        "id": 105,
        "category_id": 3,
        "name": "Es Segar Kantingo",
        "price": 8000,
        "description": "Es campur buah harum segar dengan sirup manis dan jelly kenyal.",
        "image_url": "https://cdn.kantingo.com/images/es_segar.jpg",
        "estimated_time": "3-5 Menit",
        "rating": 4.8,
        "is_available": true,
        "spice_levels": [],
        "toppings": ["Ekstra Es Batu", "Less Sugar"]
      }
    ]
  }
}
```

---

#### 7. Get Detail Menu (`GET /api/v1/menus/{id}`)
- **Fungsi:** Mengambil informasi detail satu produk makanan/minuman spesifik.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/menus/{id}`
- **Contoh URL:** `/api/v1/menus/101`
- **Hak Akses:** Publik
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Detail menu berhasil dimuat.",
  "data": {
    "id": 101,
    "name": "Mie Pedas Kantingo Level 1-4",
    "category": "Mie & Makanan",
    "price": 13000,
    "description": "Mie gurih pedas dengan topping ayam cincang halus dan pangsit renyah.",
    "estimated_time": "8-10 Menit",
    "rating": 4.9,
    "is_available": true,
    "spice_levels": ["Level 0 (Gurih)", "Level 1", "Level 2", "Level 3", "Level 4"],
    "toppings": ["Pangsit Goreng", "Siomay Kukus", "Ekstra Bawang"]
  }
}
```
- **Response Error (HTTP 404 Not Found):**
```json
{
  "status": "error",
  "message": "Menu dengan ID 101 tidak ditemukan.",
  "data": null
}
```

---

#### 8. Create Product / Menu (`POST /api/v1/menus`)
- **Fungsi:** Menambahkan produk makanan/minuman baru (Hak akses Admin/Pengelola).
- **HTTP Method:** `POST`
- **Endpoint URL:** `/api/v1/menus`
- **Hak Akses:** Wajib Login Admin (`Authorization: Bearer <token>`)
- **Request Body:**
```json
{
  "category_id": 1,
  "name": "Dimsum Mentai Panggang (4 pcs)",
  "price": 16000,
  "description": "Dimsum ayam lembut dengan saus mentai gurih dibakar harum.",
  "estimated_time": "7-10 Menit",
  "is_available": true
}
```
- **Response Success (HTTP 201 Created):**
```json
{
  "status": "success",
  "message": "Menu baru berhasil ditambahkan.",
  "data": {
    "id": 108,
    "name": "Dimsum Mentai Panggang (4 pcs)",
    "price": 16000,
    "is_available": true,
    "created_at": "2026-11-10T11:00:00Z"
  }
}
```
- **Response Error (HTTP 422 Unprocessable Entity):**
```json
{
  "status": "error",
  "message": "Validasi penambahan menu gagal.",
  "errors": {
    "name": ["Nama menu sudah terdaftar."],
    "price": ["Harga wajib berupa angka positif minimal Rp 1.000."]
  }
}
```

---

#### 9. Update Menu (`PUT /api/v1/menus/{id}`)
- **Fungsi:** Mengubah data menu atau memperbarui ketersediaan stok produk.
- **HTTP Method:** `PUT`
- **Endpoint URL:** `/api/v1/menus/{id}`
- **Hak Akses:** Wajib Login Admin (`Authorization: Bearer <token>`)
- **Request Body:**
```json
{
  "name": "Mie Pedas Kantingo Level 1-4 (Spesial)",
  "price": 14000,
  "is_available": false
}
```
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Menu berhasil diperbarui.",
  "data": {
    "id": 101,
    "name": "Mie Pedas Kantingo Level 1-4 (Spesial)",
    "price": 14000,
    "is_available": false,
    "updated_at": "2026-11-10T11:30:00Z"
  }
}
```

---

#### 10. Delete Menu (`DELETE /api/v1/menus/{id}`)
- **Fungsi:** Menghapus menu dari daftar sistem kantin.
- **HTTP Method:** `DELETE`
- **Endpoint URL:** `/api/v1/menus/{id}`
- **Hak Akses:** Wajib Login Admin (`Authorization: Bearer <token>`)
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Menu berhasil dihapus dari sistem.",
  "data": null
}
```

---

### MODUL 4: PEMESANAN & TRANSAKSI (ORDERS & CHECKOUT)

---

#### 11. Create Order / Checkout (`POST /api/v1/orders`)
- **Fungsi:** Membuat pesanan mandiri baru dari customer di meja kantin.
- **HTTP Method:** `POST`
- **Endpoint URL:** `/api/v1/orders`
- **Hak Akses:** Publik
- **Request Body:**
```json
{
  "table_id": 8,
  "customer_name": "Ahmad Rizky",
  "payment_method": "QRIS",
  "notes": "Mie jangan terlalu pedas, es dipisah",
  "items": [
    {
      "menu_id": 101,
      "quantity": 2,
      "spice_level": "Level 2",
      "topping": "Pangsit Goreng"
    },
    {
      "menu_id": 105,
      "quantity": 1,
      "spice_level": null,
      "topping": "Less Sugar"
    }
  ]
}
```
- **Response Success (HTTP 201 Created):**
```json
{
  "status": "success",
  "message": "Pesanan berhasil dibuat dan diteruskan ke dapur!",
  "data": {
    "order_id": 342,
    "order_code": "KTG-2611-0342",
    "table_number": "08",
    "customer_name": "Ahmad Rizky",
    "payment_method": "QRIS",
    "payment_status": "unpaid",
    "order_status": "pending",
    "subtotal": 34000,
    "tax_amount": 3400,
    "total_amount": 37400,
    "qris_payload": "00020101021226680016ID.CO.KANTINGO.WWW01189360000201103425204581253033605405374005802ID5913KANTINGO FOOD6007BANDUNG6304A1B2",
    "created_at": "2026-11-10T12:30:15Z"
  }
}
```
- **Response Error (HTTP 400 Bad Request / Stok Habis):**
```json
{
  "status": "error",
  "message": "Menu pesanan (ID: 101) saat ini sudah habis atau tidak aktif.",
  "data": null
}
```
- **Response Error (HTTP 422 Unprocessable Entity):**
```json
{
  "status": "error",
  "message": "Validasi input order gagal.",
  "errors": {
    "table_id": ["Nomor meja wajib dipilih."],
    "customer_name": ["Nama pemesan tidak boleh kosong."],
    "items": ["Pesanan harus berisi minimal 1 item makanan/minuman."]
  }
}
```

---

#### 12. Get Detail Order (`GET /api/v1/orders/{id}`)
- **Fungsi:** Mengambil status live pengerjaan makanan dan rincian nota pesanan.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/orders/{id}`
- **Contoh URL:** `/api/v1/orders/342`
- **Hak Akses:** Publik
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Detail pesanan berhasil diambil.",
  "data": {
    "order_id": 342,
    "order_code": "KTG-2611-0342",
    "table_number": "08",
    "customer_name": "Ahmad Rizky",
    "order_status": "cooking",
    "payment_status": "paid",
    "payment_method": "QRIS",
    "estimated_ready_time": "10-15 Menit",
    "notes": "Mie jangan terlalu pedas, es dipisah",
    "items": [
      {
        "menu_name": "Mie Pedas Kantingo Level 1-4",
        "customization": "Level 2 • Pangsit Goreng",
        "quantity": 2,
        "price": 13000,
        "subtotal": 26000
      },
      {
        "menu_name": "Es Segar Kantingo",
        "customization": "Less Sugar",
        "quantity": 1,
        "price": 8000,
        "subtotal": 8000
      }
    ],
    "pricing": {
      "subtotal": 34000,
      "tax_pb1": 3400,
      "grand_total": 37400
    }
  }
}
```

---

#### 13. Get All Orders / Antrean Dapur (`GET /api/v1/orders`)
- **Fungsi:** Menampilkan antrean seluruh pesanan untuk monitor staf kasir dan dapur.
- **HTTP Method:** `GET`
- **Endpoint URL:** `/api/v1/orders`
- **Hak Akses:** Wajib Login Staf (`Authorization: Bearer <token>`)
- **Query Parameters:**
  - `status` (string, opsional): `pending`, `cooking`, `ready`, `completed`
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Daftar antrean pesanan berhasil dimuat.",
  "data": [
    {
      "order_id": 342,
      "order_code": "KTG-2611-0342",
      "table_number": "08",
      "customer_name": "Ahmad Rizky",
      "total_amount": 37400,
      "order_status": "cooking",
      "payment_status": "paid",
      "item_count": 3,
      "created_at": "2026-11-10T12:30:15Z"
    }
  ]
}
```

---

#### 14. Update Order Status (`PATCH /api/v1/orders/{id}/status`)
- **Fungsi:** Memperbarui tahapan status pengerjaan pesanan oleh staf dapur/kasir.
- **HTTP Method:** `PATCH`
- **Endpoint URL:** `/api/v1/orders/{id}/status`
- **Hak Akses:** Wajib Login Staf (`Authorization: Bearer <token>`)
- **Request Body:**
```json
{
  "order_status": "ready"
}
```
*Pilihan status yang valid:* `pending` ➔ `cooking` ➔ `ready` ➔ `completed` / `cancelled`
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Status pesanan berhasil diperbarui.",
  "data": {
    "order_id": 342,
    "previous_status": "cooking",
    "current_status": "ready",
    "updated_at": "2026-11-10T12:42:10Z"
  }
}
```

---

### MODUL 5: PEMBAYARAN (PAYMENT SETTLEMENT)

---

#### 15. Konfirmasi Pembayaran (`POST /api/v1/orders/{id}/payment`)
- **Fungsi:** Memverifikasi pelunasan tagihan melalui QRIS e-wallet atau bayar kasir tunai.
- **HTTP Method:** `POST`
- **Endpoint URL:** `/api/v1/orders/{id}/payment`
- **Contoh URL:** `/api/v1/orders/342/payment`
- **Hak Akses:** Publik / Webhook Callback
- **Request Body:**
```json
{
  "payment_method": "QRIS",
  "amount_paid": 37400,
  "transaction_reference": "TRX-QRIS-20261110-88392"
}
```
- **Response Success (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Pembayaran lunas terverifikasi.",
  "data": {
    "order_id": 342,
    "payment_status": "paid",
    "amount_paid": 37400,
    "settlement_time": "2026-11-10T12:31:05Z"
  }
}
```
- **Response Error (HTTP 400 Bad Request):**
```json
{
  "status": "error",
  "message": "Nominal pembayaran tidak sesuai dengan total tagihan pesanan.",
  "data": null
}
```

---

## 4. Matriks Kode Status HTTP (Error Handling Guide)

| HTTP Status | Arti Standar | Penggunaan di Proyek Kantingo |
|---|---|---|
| **`200 OK`** | Request Sukses | Pengambilan data (GET), pembaruan data (PUT/PATCH), pelunasan bayar |
| **`201 Created`** | Sumber Daya Dibuat | Penambahan menu baru, pembuatan Order baru oleh customer |
| **`400 Bad Request`** | Parameter Salah | Stok habis, pembayaran kurang, transisi status order tidak valid |
| **`401 Unauthorized`** | Belum Terautentikasi | Token Bearer tidak ada, kedaluwarsa, atau tidak terdaftar |
| **`403 Forbidden`** | Akses Ditolak | Kasir non-admin mencoba menambah atau menghapus menu |
| **`404 Not Found`** | Tidak Ditemukan | ID Order, Meja, atau Menu tidak ditemukan di database |
| **`422 Unprocessable`**| Kesalahan Validasi Input | Format email salah, kuantitas porsi 0/minus, field wajib kosong |
| **`500 Server Error`** | Kendala Internal Backend | Kegagalan query SQL, koneksi database down, exception runtime |

---

*Dokumen Artefak `03_API_Contract` ini disahkan untuk pedoman kolaborasi tim Backend (Laravel) dan Frontend (Flutter) Kantingo.*
