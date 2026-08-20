# InternSync API ⚙️

InternSync API adalah service backend yang menyediakan RESTful API untuk sistem manajemen magang/PKL **InternSync**. Service ini dibangun menggunakan **Laravel 12** dan **PHP 8.2+**.

---

## 🛠️ Persyaratan Sistem

Sebelum menjalankan service ini, pastikan Anda telah menginstal:
*   [PHP](https://www.php.net/) (versi 8.2 atau lebih baru)
*   [Composer](https://getcomposer.org/) (pengatur paket dependensi PHP)
*   [MySQL / MariaDB](https://www.mysql.com/) atau relational database server lainnya.

---

## 🚀 Panduan Setup & Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menyiapkan service backend di lingkungan lokal Anda:

### 1. Install Dependensi PHP
Jalankan Composer untuk menginstal semua pustaka pendukung Laravel:
```bash
composer install
```

### 2. Salin & Sesuaikan File Konfigurasi Lingkungan (`.env`)
Salin template konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*(Di Windows PowerShell, gunakan perintah: `copy .env.example .env`)*

Buka file `.env` baru tersebut, lalu sesuaikan koneksi database Anda. Contoh untuk MySQL lokal:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308          # Sesuaikan port (default: 3306, di beberapa lokal port mysql di-set 3308)
DB_DATABASE=internsync-db
DB_USERNAME=root
DB_PASSWORD=123456    # Sesuaikan password database Anda
```
*Pastikan Anda telah membuat database kosong bernama `internsync-db` di server database Anda.*

### 3. Generate Security Key Aplikasi
Jalankan perintah ini untuk membuat `APP_KEY` unik di file `.env` Anda:
```bash
php artisan key:generate
```

### 4. Jalankan Migrasi & Data Seeder
Jalankan migrasi untuk membuat seluruh tabel database, dan sekaligus jalankan seeder untuk mengisi data awal:
```bash
php artisan migrate --seed
```

> [!NOTE]
> Proses seeding akan membuat akun **Admin** default untuk login pertama kali:
> *   **Email:** `admin@smkpgritelagasari.sch.id`
> *   **Password:** `admin`
> *   **Role Terbuat:** `Admin`, `Siswa`, `Pembimbing`, `Koordinator`, `Hubin`

---

## 🏃‍♂️ Menjalankan Aplikasi

Terdapat dua cara untuk menjalankan server backend:

### Metode A: Menjalankan Sekaligus (Menggunakan Script Composer)
Aplikasi ini dilengkapi script concurrency untuk menjalankan backend server, queue listener, logs, dan vite secara bersamaan:
```bash
composer run dev
```

### Metode B: Menjalankan Manual Terpisah
Jika ada kendala dengan tools concurrency atau ingin memantau secara terpisah:
1.  **Jalankan Laravel Built-in Server:**
    ```bash
    php artisan serve
    ```
    *(Server API akan berjalan secara default di `http://127.0.0.1:8000`)*
2.  **Jalankan Queue Listener** (di terminal baru, diperlukan untuk proses background kirim email/ekspor):
    ```bash
    php artisan queue:listen
    ```
3.  **Memantau Log Aktivitas** (opsional, di terminal baru):
    ```bash
    php artisan pail
    ```
