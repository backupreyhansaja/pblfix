# Website Laboratorium Kampus

Website laboratorium kampus dengan fitur lengkap menggunakan PHP Native, PostgreSQL, dan Tailwind CSS.

## 🚀 Fitur

### Landing Page
- ✅ Hero Section dengan animasi menarik
- ✅ Visi & Misi
- ✅ Sejarah Laboratorium
- ✅ Struktur Organisasi
- ✅ Daftar Staff
- ✅ Daftar Mahasiswa Terlibat
- ✅ Galeri Foto Kegiatan
- ✅ Form Kontak
- ✅ Animasi menggunakan AOS (Animate On Scroll)
- ✅ Desain responsive dengan Tailwind CSS

### Dashboard Admin
- ✅ Login System
- ✅ Dashboard dengan statistik
- ✅ CRUD Visi & Misi
- ✅ CRUD Sejarah
- ✅ CRUD Struktur Organisasi
- ✅ CRUD Staff
- ✅ CRUD Mahasiswa
- ✅ CRUD Gallery
- ✅ Kelola Pesan Masuk
- ✅ Upload Foto

## 📋 Requirements

- PHP 7.4 atau lebih tinggi
- PostgreSQL 12 atau lebih tinggi
- PHP PostgreSQL Extension (php_pgsql)
- Web Server (Apache/Nginx)
- Laragon/XAMPP/WAMP (Opsional)

## 🔧 Instalasi

### 1. Clone atau Download Project
```bash
# Jika menggunakan git
git clone <repository-url>

# Atau download dan extract ke folder htdocs/www
# Contoh: c:/laragon/www/123
```

### 2. Setup Database PostgreSQL

#### Buat Database Baru
```sql
CREATE DATABASE lab_kampus;
```

#### Import Schema Database
```bash
# Masuk ke psql
psql -U postgres

# Connect ke database
\c lab_kampus

# Import schema
\i c:/laragon/www/123/database/schema.sql
```

Atau import manual dari file `database/schema.sql`

### 3. Konfigurasi Database

Edit file `config/database.php` sesuai dengan pengaturan PostgreSQL Anda:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'lab_kampus');
define('DB_USER', 'postgres');
define('DB_PASS', 'your_password'); // Ganti dengan password PostgreSQL Anda
```

### 4. Enable PostgreSQL Extension di PHP

Pastikan extension PostgreSQL sudah aktif di `php.ini`:

```ini
extension=pgsql
extension=pdo_pgsql
```

Restart web server setelah mengubah konfigurasi.

### 5. Set Permissions

Pastikan folder `uploads/` memiliki write permission:

```bash
# Linux/Mac
chmod -R 777 uploads/

# Windows (tidak perlu setting khusus)
```

### 6. Akses Website

- **Landing Page**: http://localhost/123/
- **Admin Login**: http://localhost/123/admin/login.php

### 7. Login Admin

Default credentials:
- **Username**: admin
- **Password**: admin123

⚠️ **PENTING**: Segera ganti password default setelah login pertama kali!

## 📁 Struktur Folder

```
123/
├── admin/                  # Admin dashboard
│   ├── includes/          # Auth & header/footer admin
│   ├── index.php          # Dashboard utama
│   ├── login.php          # Halaman login
│   ├── visi-misi.php      # Kelola visi & misi
│   ├── sejarah.php        # Kelola sejarah
│   ├── struktur.php       # Kelola struktur organisasi
│   ├── staff.php          # Kelola staff
│   ├── mahasiswa.php      # Kelola mahasiswa
│   ├── gallery.php        # Kelola galeri
│   └── messages.php       # Kelola pesan masuk
├── api/                   # API endpoints
│   └── contact.php        # Handle contact form
├── config/                # Configuration files
│   └── database.php       # Database connection
├── database/              # Database files
│   └── schema.sql         # Database schema
├── includes/              # Include files
│   ├── header.php         # Header landing page
│   └── footer.php         # Footer landing page
├── uploads/               # Upload directory
│   ├── staff/            # Foto staff
│   ├── mahasiswa/        # Foto mahasiswa
│   ├── struktur/         # Foto struktur organisasi
│   └── gallery/          # Foto galeri
├── index.php              # Landing page
└── README.md             # Documentation
```

## 🎨 Teknologi yang Digunakan

- **Backend**: PHP Native
- **Database**: PostgreSQL
- **Frontend**: 
  - HTML5
  - Tailwind CSS (via CDN)
  - JavaScript (Vanilla)
- **Icons**: Font Awesome 6
- **Animations**: AOS (Animate On Scroll)

## 📝 Penggunaan

### Mengelola Konten

1. Login ke admin panel
2. Pilih menu yang ingin dikelola
3. Tambah/Edit/Hapus data sesuai kebutuhan
4. Perubahan akan langsung terlihat di landing page

### Upload Foto

- Format yang didukung: JPG, JPEG, PNG, GIF
- Ukuran maksimal: Tergantung konfigurasi PHP (default 2MB)
- Foto akan otomatis tersimpan di folder `uploads/`

### Mengelola Pesan Kontak

- Semua pesan dari form kontak akan masuk ke menu "Pesan Masuk"
- Tandai pesan sebagai sudah dibaca
- Balas langsung via email
- Hapus pesan yang tidak diperlukan

## 🔒 Keamanan

- Password di-hash menggunakan `password_hash()` dan `password_verify()`
- Input sanitization menggunakan `pg_escape_string()`
- Session management untuk autentikasi admin
- Protected admin routes

## 🐛 Troubleshooting

### Database Connection Error
- Pastikan PostgreSQL service sudah running
- Cek kredensial database di `config/database.php`
- Pastikan PHP PostgreSQL extension sudah aktif

### Upload File Gagal
- Cek permission folder `uploads/`
- Cek `upload_max_filesize` dan `post_max_size` di `php.ini`

### Halaman Blank
- Enable error reporting di `php.ini`:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
- Cek log error PHP

## 📞 Support

Jika ada pertanyaan atau issues, silakan hubungi administrator.

## 📜 License

This project is open source and available for educational purposes.

---

**Dibuat dengan ❤️ menggunakan PHP Native, PostgreSQL, dan Tailwind CSS**
