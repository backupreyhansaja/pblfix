# Landing Page Laboratorium - Dokumentasi

## 📋 Overview

Landing page ini telah dikembangkan dengan fitur dropdown navigation dan halaman-halaman terpisah sesuai dengan kebutuhan:

### ✅ Fitur yang Sudah Dibuat:

1. **Navigation dengan Dropdown Menu**
   - Menu "Informasi" dengan dropdown berisi:
     - Gallery
     - Struktur Organisasi
     - Produk
     - Partner & Sponsor

2. **Halaman Utama (index.php)**
   Menampilkan konten sesuai gambar:
   - Hero Section
   - **SCOPE** - Lingkup layanan laboratorium
   - **OUR MISSION** - Misi laboratorium
   - **PRIORITY RESEARCH TOPICS** - Topik penelitian prioritas
   - **BLUEPRINT** - Peta rencana pengembangan
   - Contact Section

3. **Halaman Terpisah:**
   - `gallery.php` - Galeri foto kegiatan
   - `struktur_organisasi.php` - Struktur organisasi tim
   - `produk.php` - Produk/hasil penelitian
   - `partner_sponsor.php` - Partner dan sponsor

## 🗄️ Database Setup

### Langkah 1: Import Database
Jalankan file SQL untuk membuat tabel baru:
```sql
pbl/database/additional_tables.sql
```

Tabel yang akan dibuat:
- `produk` - untuk menyimpan data produk/hasil penelitian
- `partners` - untuk menyimpan data partner
- `sponsors` - untuk menyimpan data sponsor

### Langkah 2: Buat Folder Upload
Buat folder-folder berikut untuk upload gambar:
```
pbl/uploads/produk/
pbl/uploads/partners/
pbl/uploads/sponsors/
pbl/uploads/gallery/
pbl/uploads/struktur/
```

Set permission folder menjadi 755 atau 777 (tergantung server).

## 🎨 Struktur File

```
pbl/
├── index.php                    # Landing page utama (SCOPE, MISSION, RESEARCH, BLUEPRINT)
├── pages/                       # 📁 Folder untuk semua halaman konten
│   ├── gallery.php             # Halaman galeri
│   ├── struktur_organisasi.php # Halaman struktur organisasi
│   ├── produk.php              # Halaman produk
│   ├── partner_sponsor.php     # Halaman partner & sponsor
│   ├── news.php                # Halaman berita
│   ├── news_detail.php         # Halaman detail berita
│   └── README.md               # Dokumentasi folder pages
├── includes/
│   ├── header.php              # Header dengan dropdown menu
│   └── footer.php              # Footer dengan link updated
├── database/
│   └── additional_tables.sql   # SQL untuk tabel baru
└── uploads/
    ├── produk/
    ├── partners/
    ├── sponsors/
    ├── gallery/
    └── struktur/
```

## 🚀 Cara Menggunakan

### 1. Landing Page Utama
File: `index.php`

Menampilkan 4 section utama:
- **SCOPE**: Data dari tabel `scope`
- **MISSION**: Data dari tabel `mission`  
- **PRIORITY RESEARCH TOPICS**: Data dari tabel `priority_research_topics`
- **BLUEPRINT**: Display statis sesuai gambar

### 2. Gallery
File: `pages/gallery.php`

- Menampilkan grid foto dari tabel `gallery`
- Fitur modal untuk melihat gambar lebih besar
- Mobile responsive

### 3. Struktur Organisasi
File: `pages/struktur_organisasi.php`

- Menampilkan hierarki organisasi dari tabel `struktur_organisasi`
- Level 1: Kepala lab (featured)
- Level 2+: Anggota tim (grid layout)

### 4. Produk
File: `pages/produk.php`

- Menampilkan produk/hasil penelitian dari tabel `produk`
- Badge kategori dan teknologi
- Status: Planning, Ongoing, Completed
- Link eksternal ke produk (opsional)

### 5. Partner & Sponsor
File: `pages/partner_sponsor.php`

- Section Partners: dari tabel `partners`
- Section Sponsors: dari tabel `sponsors`
- Section keuntungan berkolaborasi

### 6. Berita
File: `pages/news.php` dan `pages/news_detail.php`

- Daftar berita dengan pagination
- Detail berita dengan gambar dan konten lengkap

## 🎯 Navigation Menu

### Desktop Menu:
- Beranda
- **Informasi** (Dropdown)
  - Gallery
  - Struktur Organisasi
  - Produk
  - Partner & Sponsor
- Berita
- Kontak
- Admin (login)

### Mobile Menu:
- Same structure dengan expandable dropdown

## 💾 Data Management

### Sample Data
File SQL sudah menyertakan sample data untuk:
- 6 produk
- 6 partners
- 10 sponsors

### Untuk Menambah Data:
Gunakan admin panel atau insert manual via phpMyAdmin:

```sql
-- Contoh insert produk
INSERT INTO produk (nama, deskripsi, kategori, teknologi, status) 
VALUES ('Nama Produk', 'Deskripsi produk...', 'Kategori', 'Tech1, Tech2', 'ongoing');

-- Contoh insert partner
INSERT INTO partners (nama, tipe, deskripsi) 
VALUES ('Nama Partner', 'perusahaan', 'Deskripsi...');

-- Contoh insert sponsor
INSERT INTO sponsors (nama, level, deskripsi) 
VALUES ('Nama Sponsor', 'gold', 'Deskripsi...');
```

## 🎨 Design Features

### Color Scheme:
- Primary: Blue (#1e3a8a, #1e40af, #3b82f6)
- Secondary: Gray variations
- Accent colors untuk categories

### Animations:
- AOS (Animate On Scroll) untuk smooth entrance
- Hover effects pada cards
- Smooth scroll navigation
- Modal transitions

### Responsive:
- Mobile-first approach
- Grid layouts adaptif
- Collapsible mobile menu

## 📱 Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## 🔧 Customization

### Mengubah Warna:
Edit file `includes/header.php` pada section `<style>`:
```css
/* Ganti warna primary */
.gradient-bg {
    background: your-color;
}
```

### Menambah Menu Dropdown:
Edit `includes/header.php` pada section Desktop Menu dan Mobile Menu.

### Mengubah Footer:
Edit `includes/footer.php` untuk mengubah link dan informasi kontak.

## 📝 Notes

1. **Database Connection**: Pastikan koneksi database di `config/database.php` sudah benar
2. **Image Upload**: Pastikan folder uploads memiliki permission yang benar
3. **Sample Data**: Data sample sudah disediakan, bisa dihapus/edit sesuai kebutuhan
4. **Admin Panel**: Perlu dibuat CRUD untuk manage data (belum included)

## 🆘 Troubleshooting

### Gambar tidak muncul:
- Check folder uploads exists dan memiliki permission
- Check path gambar di database
- Check nama file gambar

### Dropdown tidak berfungsi:
- Check JavaScript di header.php sudah di-load
- Check console browser untuk error

### Data tidak muncul:
- Check koneksi database
- Check nama tabel sesuai
- Check data sudah ada di database

## 📞 Support

Untuk pertanyaan atau bantuan, silakan hubungi tim development.

---

**Version**: 1.0  
**Last Updated**: December 2025  
**Developer**: Lab Development Team

