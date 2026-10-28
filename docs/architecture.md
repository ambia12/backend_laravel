# Arsitektur Backend - Laravel API (Tugas Pertemuan 5)

Dokumen ini menjelaskan struktur arsitektur, pola desain, dan endpoint utama yang digunakan pada sisi backend Laravel untuk diintegrasikan dengan aplikasi Flutter.

## 1. Pola Desain (MVC + RESTful API)
Proyek ini menggunakan arsitektur **Model-View-Controller (MVC)** yang dioptimalkan sebagai **RESTful API**. 
- **Model**: Mengelola interaksi basis data menggunakan Eloquent ORM.
- **Controller**: Mengatur logika bisnis dan memproses permintaan dari klien (Flutter), lalu mengembalikan respons dalam bentuk format JSON.
- **Routes (`routes/api.php`)**: Menjadi gerbang utama jalur komunikasi endpoint API.

## 2. Struktur Folder Utama (`app/Http/`)
- `Controllers/Api/`: Berisi logika pengontrol untuk endpoint API publik/terotentikasi.
- `Requests/`: Berisi aturan validasi input data masuk (*incoming request*).
- `Resources/`: Berisi kelas transformasi data untuk menyeragamkan format respons JSON ke frontend.

## 3. Konfigurasi Keamanan & Lingkungan
- Menggunakan file `.env` untuk menyimpan konfigurasi sensitif (koneksi database MySQL, kunci aplikasi, dll.).
- Mengamankan komunikasi data menggunakan format pertukaran standar JSON melalui HTTP Methods (`GET`, `POST`, `PUT`, `DELETE`).