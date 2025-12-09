# Dokumentasi Kode — pblfix

Dokumentasi singkat dan terfokus untuk pengembang yang ingin memahami struktur kode, lokasi CRUD admin, halaman publik, koneksi ke database, penyimpanan file, dan alur pengiriman email.

**Catatan**: gunakan nama file dan path persis seperti di repo. Semua path relatif ke root project `pblfix`.

**Ringkasan Cepat**
- **Server**: PHP (plain), PostgreSQL
- **DB helper**: `config/database.php` (class `Database`) — gunakan untuk query, fetch, fetchAll.
- **Uploads**: folder `uploads/` dengan subfolder per-jenis (`berita`, `struktur`, `produk`, `sponsors`, `gallery`, dll.). Metadata file disimpan di tabel `files`.

**1. File konfigurasi utama**
- `config/database.php`: koneksi PostgreSQL, wrapper query/escape/fetch helper. Semua file PHP menggunakan ini untuk akses DB.

**2. Skema DB penting**
- `database/schema.sql` — definisi tabel utama. Tabel yang sering dipakai:
  - `files(id, filename, path, mime_type)` — metadata file. `path` biasanya seperti `/uploads/sponsors`.
  - `sponsors(title, logo_id, urutan, is_visible)` — sponsor, `logo_id` FK ke `files`.
  - `kolaborasi(id, nama_sponsor, jenis)` — partner/sponsor generik. `jenis` = 'partner' atau 'sponsor'.
  - `dosen`, `struktur_organisasi`, `publikasi` — data dosen dan publikasi.
  - `contact_messages`, `contact_replies` — pesan masuk dan reply log.
  - `setting_sosial_media` — sosial link + `no_hp`, `contact_email` (may be added at runtime by admin page).

**3. Admin (CRUD) — lokasi dan peran file**
- `admin/berita.php`: CRUD Berita (contoh pola upload image ke `files`).
- `admin/struktur.php`: CRUD Struktur Organisasi — membuat/menautkan `dosen`, upload foto ke `files`, mengatur jabatan.
- `admin/publikasi.php`: CRUD Publikasi per dosen (tabel `publikasi`).
- `admin/produk.php`: CRUD Produk (upload gambar ke `uploads/produk/`, metadata di kolom `produk.gambar`).
- `admin/messages.php`: Daftar pesan masuk + modal balas. Saat ini balasan menggunakan `mail()` dan juga mencatat ke `contact_replies` dan ke file debug log `storage/mail_debug.log`.
- `admin/sponsors.php`: (jika ada) CRUD Sponsors — menyimpan `logo` ke `files` dan `sponsors.logo_id`.
- `admin/partners.php`: (jika ada) CRUD Partners — mengelola `kolaborasi` dengan `jenis = 'partner'`.
- `admin/settings.php`: (baru) Halaman admin untuk mengatur `no_hp`, `contact_email`, dan social links (Facebook/Twitter/Instagram). Halaman ini akan membuat/menambah `contact_email` column jika belum ada dan menyimpan satu baris settings.

Catatan: semua halaman admin menggunakan template header/footer di `admin/includes/header.php` dan `admin/includes/footer.php`.

**4. Halaman publik — lokasi & apa yang ditampilkan**
- `pages/produk.php`: menampilkan daftar produk (kolom: `nama`, `gambar`, `link`, `deskripsi`). Link produk biasanya membuka target eksternal.
- `pages/struktur_organisasi.php`: kartu struktur organisasi. Setiap kartu mengarah ke `pages/publikasi.php?dosen_id=<id>`.
- `pages/publikasi.php`: halaman publikasi per dosen — menampilkan nama dosen, `deskripsi` dan tabel publikasi. Ada debug panel `?debug=1` untuk memeriksa path foto.
- `pages/partner_sponsor.php`: menampilkan partners dan sponsors; halaman ini sekarang membaca `kolaborasi` untuk partners dan `sponsors` JOIN `files` untuk logo sponsor.
- `includes/header.php`, `includes/footer.php`: header/footer publik. Footer menggunakan nilai dinamis untuk `Telepon` dan `Email` yang dibaca dari `setting_sosial_media`.

**5. Uploads & File paths**
- Upload location di filesystem: `uploads/<type>/` (mis. `uploads/sponsors/logo.png`).
- Di DB: `files.path` menyimpan path relatif seperti `/uploads/sponsors` dan `files.filename` nama file.
- Untuk merender di halaman (pages under `pages/`) gunakan prefix `..` untuk mencapai root dari `pages/`, contohnya: `..` + `files.path` + `/` + `files.filename` -> `../uploads/sponsors/logo.png`.
- Helper upload pattern: lihat `admin/struktur.php` function `uploadFileToFilesTable()`; `admin/sponsors.php` dan `admin/produk.php` menggunakan pola serupa.

**6. Email / Balasan pesan**
- Lokasi utama: `admin/messages.php` — formulir balas. Implementasi saat ini
  - Mencoba mengirim dengan `mail()` (PHP built-in).
  - Menyimpan log pengiriman ke `storage/mail_debug.log` untuk debugging (hasil, headers, sendmail_path, SMTP ini/itu).
  - Menyimpan salinan balasan ke tabel `contact_replies`.
- Catatan penting: pada lingkungan development (Laragon/Windows) `mail()` mungkin tidak dikonfigurasi. Ada rencana/permintaan untuk fallback menggunakan PHPMailer SMTP. Implementasi PHPMailer belum selesai — jika Anda ingin saya tambahkan, saya butuh preferensi pemasangan (Composer vs vendor) dan kredensial SMTP.

**7. Cara menambahkan Sponsor / Partner (Admin)**
1. Login ke admin.
2. Buka `admin/partners.php` untuk menambah/edit partner (menulis `nama_sponsor`).
3. Buka `admin/sponsors.php` untuk menambah/edit sponsor — unggah logo gambar. Sistem akan menyimpan file di `uploads/sponsors/` dan record di `files`.

**8. Troubleshooting umum**
- Gambar tidak muncul
  - Pastikan `files.path` dan `files.filename` benar (`/uploads/...` dan `logo.png`).
  - Untuk halaman di `pages/` gunakan `..` prefix: `../uploads/...`.
  - Jika `file_exists` digunakan di server-side, periksa path absolut di server: `__DIR__ . '/../' . ltrim($foto_path, '/')`.
- Email balasan tidak terkirim
  - Periksa `storage/mail_debug.log` untuk detil (headers, PHP `mail()` return, sendmail settings).
  - Jika di Windows, `mail()` biasanya tidak tersedia — gunakan SMTP via PHPMailer.

**9. Pengembangan & Deployment**
- Menambahkan PHPMailer (rekomendasi):
  - Dengan Composer: jalankan `composer require phpmailer/phpmailer` di root project, lalu gunakan `use PHPMailer\\PHPMailer\\PHPMailer;` di `admin/messages.php`.
  - Atau vendor-in PHPMailer files jika Anda tidak ingin Composer.
  - Simpan kredensial SMTP di file yang tidak dikomit, contoh `config/mail.php` dan tambahkan `config/mail.php` ke `.gitignore`.
- DB migrations: saya menaruh perubahan schema di `database/schema.sql` (tambah `produk`, `contact_replies`, ubah `dosen.nip` -> `deskripsi` dll.). Untuk production, jalankan SQL manual atau gunakan tool migration.

**10. Perubahan yang sudah dibuat (catatan timeline)**
- Produk CRUD ditambahkan: `admin/produk.php`, `pages/produk.php`, dan `produk` table.
- Publikasi per-dosen: `admin/publikasi.php`, `pages/publikasi.php`.
- Footer sekarang membaca `setting_sosial_media` (dinamis) — lihat `admin/settings.php` untuk mengedit.

--
Jika Anda mau, saya bisa:
- Tambahkan PHPMailer + tutorial konfigurasi SMTP dan implementasi fallback pada `admin/messages.php`.
- Tambahkan admin CRUD untuk sponsors/partners bila belum tersedia di admin panel (atau perbaiki jika Anda menolak perubahan sebelumnya).
- Buatkan skrip migrasi SQL terpisah untuk perubahan schema.

Beritahu saya mana yang harus saya kerjakan berikutnya.

***End of documentation***

## Detailed Code Map (per-file, what each block does)

Below are focused, developer-friendly notes that map important code blocks to their purpose. Line numbers may shift as you edit files; use the function / comment markers shown in each file to navigate quickly.

- **`admin/struktur.php`**
  - Helper: `uploadFileToFilesTable($db, $fileInputName)` — top of file. Handles receiving `$_FILES['foto']`, moves uploaded file to `../uploads/struktur/`, inserts a record into `files` and returns the `files.id`. Use this for any file-to-files-table uploads.
  - Delete handler: checks `$_GET['delete']` and deletes from `struktur_organisasi`.
  - POST handler (Add / Edit): takes `jabatan`, `nama`, `deskripsi` from `$_POST`.
    - Creates or finds a `dosen` row by `deskripsi` (or by `nama` if deskripsi empty).
    - Calls upload helper and stores `foto_id` when provided.
    - Builds `INSERT` or `UPDATE` SQL for `struktur_organisasi` and executes it.
  - Data fetch section (SELECT): joins `struktur_organisasi` -> `dosen` -> `files` (foto) and orders by role priority. This block populates `$data` for rendering the admin table and edit form.

- **`admin/messages.php`**
  - POST reply flow (top POST handler): validates `reply_email`, `reply_subject`, `reply_body`.
    - Uses `sendMailSMTP()` (from `config/mailer.php`) to send HTML email. On success marks original message `is_read` and sets success message.
  - Delete / Mark-as-read handlers (`$_GET['delete']`, `$_GET['read']`).
  - Fetch data (SELECT * FROM contact_messages) to list messages.
  - Reply modal (client-side): `openReplyModal()` / `closeReplyModal()` JS functions set hidden inputs and show modal.

- **`admin/settings.php`**
  - Fetch existing settings: `SELECT * FROM setting_sosial_media LIMIT 1`.
  - POST handler: reads `alamat`, `facebook`, `twitter`, `instagram`, `no_hp`, `contact_email`.
    - Checks `information_schema.columns` for `contact_email` column; if missing issues `ALTER TABLE setting_sosial_media ADD COLUMN contact_email VARCHAR(255)`.
    - Then `INSERT` or `UPDATE` the single settings row.
  - UI: simple form to edit address, phone, contact email and social URLs.

- **`includes/footer.php`**
  - At file top it requires `config/database.php` and runs `SELECT * FROM setting_sosial_media LIMIT 1` to populate `$alamat`, `$phone`, `$email` used in the contact cards in the footer and contact section.
  - Contact form posts to `kirim_pesan.php` (action path depends on `pageInPages` flag).

- **`pages/partner_sponsor.php`**
  - Fetch partners: selects from `kolaborasi` where `jenis = 'partner'`, left joins `files` to get `filename` & `path` for logos. Renders grid of partners; if `filename` exists uses `<?= '../' . $path . '/' . $filename ?>` as `img src`.
  - Fetch sponsors: selects from `kolaborasi` where `jenis != 'partner'` (or a dedicated `sponsors` table where available) joined to `files` likewise.

- **`admin/sponsors.php`** (admin CRUD for sponsors)
  - Upload helper: move uploaded logo to `../uploads/sponsors/`, insert into `files`, return `id`.
  - POST handler: creates or updates `sponsors` record setting `title`, `urutan`, `is_visible`, and `logo_id` if new upload present.
  - GET handlers: `?edit=` to load a sponsor for edit, `?delete=` to remove sponsor.

- **`admin/partners.php`**
  - Simple CRUD on `kolaborasi` with `jenis='partner'`. POST handler inserts or updates `nama_sponsor`. `?delete=` deletes row.

- **`admin/produk.php`**
  - (If present) Uses the same upload pattern for `uploads/produk/` and stores filename in `produk.gambar`. Public page `pages/produk.php` reads `produk` table to list products.

Notes on navigation: search each file for comment markers such as `/* ================================` or `// FETCH DATA` — these are anchor points used in this codebase to find handlers (upload, POST, GET, render) quickly.

If you want, I can now:
- Add exact line-numbered anchors in each file as comments (e.g., `// DOC_ANCHOR:upload_helper:start`) so the documentation can reference stable anchors that survive edits.
- Or I can update this README to include exact line numbers now (I can compute them and insert them), but keep in mind those numbers will drift as files are edited.

Which would you prefer? Add stable anchors in code (recommended) or insert exact current line numbers into the README now? 
*** End of update ***
