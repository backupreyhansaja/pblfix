-- ==========================================
-- 1. INDEPENDENT TABLES
-- ==========================================

CREATE TABLE public.contact_messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE public.setting_sosial_media (
    id SERIAL PRIMARY KEY,
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    no_hp VARCHAR(20)
);

CREATE TABLE public.admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE public.content_dashboard (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    data JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Validasi tipe konten
    CONSTRAINT chk_content_type CHECK (type IN ('sejarah', 'visi_misi'))
);

CREATE TABLE public.kolaborasi (
    id SERIAL PRIMARY KEY,
    nama_sponsor VARCHAR(255) NOT NULL,
    jenis VARCHAR(20) NOT NULL,
    czreated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Validasi jenis kolaborasi
    CONSTRAINT chk_kolaborasi_jenis CHECK (jenis IN ('sponsor', 'partner'))
);


-- ==========================================
-- 2. RELATIONAL TABLES
-- ==========================================

-- Table FILES

CREATE TABLE public.files (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,     -- Nama file
    path VARCHAR(255) NOT NULL,         -- Path penyimpanan (contoh: /uploads/images/amati-logo.png)
    mime_type VARCHAR(50),              -- (Opsional) Untuk validasi backend: image/png, image/jpeg
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE public.sponsors (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,         -- Judul 
    logo_id INTEGER NOT NULL,            -- ID dari tabel public.files
    urutan INTEGER DEFAULT 0,        -- Untuk mengatur urutan posisi (1, 2, 3...)
    is_visible BOOLEAN DEFAULT TRUE,     -- Agar bisa di-hide tanpa menghapus data
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Menghubungkan kolom file_id ke tabel files
    CONSTRAINT fk_sponsors_file
      FOREIGN KEY(logo_id) REFERENCES public.files(id) ON DELETE SET NULL
);

-- Table DOSEN (Parent)
CREATE TABLE public.dosen (
    id SERIAL PRIMARY KEY,
    nip VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table PUBLIKASI
CREATE TABLE public.publikasi (
    id SERIAL PRIMARY KEY,
    id_dosen INT NOT NULL,
    judul VARCHAR(500) NOT NULL,
    tahun INT NOT NULL,
    jenis VARCHAR(30) NOT NULL,
    penerbit VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Validasi jenis publikasi 
    CONSTRAINT chk_publikasi_jenis CHECK (jenis IN ('jurnal', 'conference', 'thesis')),

    CONSTRAINT fk_publikasi_dosen 
        FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE CASCADE
);

-- Table STRUKTUR ORGANISASI
CREATE TABLE public.struktur_organisasi (
    id SERIAL PRIMARY KEY,
    id_dosen INT NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    foto_id INT, 
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_struktur_dosen 
        FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE CASCADE,
    
    CONSTRAINT fk_struktur_foto  
        FOREIGN KEY (foto_id) REFERENCES files(id) ON DELETE SET NULL
);

-- Table BERITA
CREATE TABLE public.berita (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    deskripsi TEXT,
    image_id INT,
    kategori VARCHAR(100),
    tanggal DATE NOT NULL,
    uploaded_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_berita_image 
        FOREIGN KEY (image_id) REFERENCES files(id) ON DELETE SET NULL
);

-- Table GALLERY
CREATE TABLE public.gallery (
    id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_id INT, 
    tanggal DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_gallery_image 
        FOREIGN KEY (image_id) REFERENCES files(id) ON DELETE SET NULL
);

-- Table BLUEPRINT
CREATE TABLE public.blueprint (
    id SERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    icon_id INT,
    color VARCHAR(50),
    urutan INT DEFAULT 0,
    uploaded_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_blueprint_icon 
        FOREIGN KEY (icon_id) REFERENCES files(id) ON DELETE SET NULL
);

-- Table SCOPE
CREATE TABLE public.scope (
    id SERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    icon_id INT,
    color VARCHAR(50),
    urutan INT DEFAULT 0,
    uploaded_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_scope_icon 
        FOREIGN KEY (icon_id) REFERENCES files(id) ON DELETE SET NULL
);


-- ==========================================
-- 3. INDEXING
-- ==========================================

-- Percepat query berdasarkan dosen 
CREATE INDEX idx_publikasi_dosen ON publikasi(id_dosen);
CREATE INDEX idx_struktur_dosen ON struktur_organisasi(id_dosen);

-- Percepat filter file (misal developer mau ambil semua 'banner')
CREATE INDEX idx_files_type ON files(file_type);

-- Percepat filter berita
CREATE INDEX idx_berita_kategori ON berita(kategori);

-- Percepat JOIN / DELETE operations (Foreign Keys)
CREATE INDEX idx_berita_image ON berita(image_id);
CREATE INDEX idx_gallery_image ON gallery(image_id);
CREATE INDEX idx_blueprint_icon ON blueprint(icon_id);
CREATE INDEX idx_scope_icon ON scope(icon_id);
CREATE INDEX idx_struktur_foto ON struktur_organisasi(foto_id);