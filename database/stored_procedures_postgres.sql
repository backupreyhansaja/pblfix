-- PostgreSQL Stored Procedures for PBL Database
-- Created: 2025-12-09 13:21:37 UTC
-- Author: backupreyhansaja

-- ============================================================================
-- FORMS TABLE STORED PROCEDURES
-- ============================================================================

-- Get all forms
CREATE OR REPLACE FUNCTION get_all_forms()
RETURNS TABLE (
    id INTEGER,
    nama_form VARCHAR,
    deskripsi TEXT,
    tanggal_dibuat TIMESTAMP,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT f.id, f.nama_form, f.deskripsi, f.tanggal_dibuat, f.status, f.created_at, f.updated_at
    FROM forms f
    ORDER BY f.id ASC;
END;
$$ LANGUAGE plpgsql;

-- Get form by ID
CREATE OR REPLACE FUNCTION get_form_by_id(p_id INTEGER)
RETURNS TABLE (
    id INTEGER,
    nama_form VARCHAR,
    deskripsi TEXT,
    tanggal_dibuat TIMESTAMP,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT f.id, f.nama_form, f.deskripsi, f.tanggal_dibuat, f.status, f.created_at, f.updated_at
    FROM forms f
    WHERE f.id = p_id;
END;
$$ LANGUAGE plpgsql;

-- Create new form
CREATE OR REPLACE FUNCTION create_form(
    p_nama_form VARCHAR,
    p_deskripsi TEXT,
    p_tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    p_status VARCHAR DEFAULT 'active'
)
RETURNS TABLE (
    id INTEGER,
    message VARCHAR
) AS $$
DECLARE
    v_id INTEGER;
BEGIN
    INSERT INTO forms (nama_form, deskripsi, tanggal_dibuat, status, created_at, updated_at)
    VALUES (p_nama_form, p_deskripsi, COALESCE(p_tanggal_dibuat, CURRENT_TIMESTAMP), p_status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    RETURNING forms.id INTO v_id;
    
    RETURN QUERY SELECT v_id, 'Form created successfully'::VARCHAR;
END;
$$ LANGUAGE plpgsql;

-- Update form
CREATE OR REPLACE FUNCTION update_form(
    p_id INTEGER,
    p_nama_form VARCHAR,
    p_deskripsi TEXT,
    p_status VARCHAR
)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    UPDATE forms
    SET nama_form = p_nama_form,
        deskripsi = p_deskripsi,
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Form updated successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Form not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Delete form
CREATE OR REPLACE FUNCTION delete_form(p_id INTEGER)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    DELETE FROM forms WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Form deleted successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Form not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- KONTAKS TABLE STORED PROCEDURES
-- ============================================================================

-- Get all kontaks
CREATE OR REPLACE FUNCTION get_all_kontaks()
RETURNS TABLE (
    id INTEGER,
    nama VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    pesan TEXT,
    tipe_kontak VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT k.id, k.nama, k.email, k.nomor_telepon, k.pesan, k.tipe_kontak, k.status, k.created_at, k.updated_at
    FROM kontaks k
    ORDER BY k.id DESC;
END;
$$ LANGUAGE plpgsql;

-- Get kontak by ID
CREATE OR REPLACE FUNCTION get_kontak_by_id(p_id INTEGER)
RETURNS TABLE (
    id INTEGER,
    nama VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    pesan TEXT,
    tipe_kontak VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT k.id, k.nama, k.email, k.nomor_telepon, k.pesan, k.tipe_kontak, k.status, k.created_at, k.updated_at
    FROM kontaks k
    WHERE k.id = p_id;
END;
$$ LANGUAGE plpgsql;

-- Get kontaks by status
CREATE OR REPLACE FUNCTION get_kontaks_by_status(p_status VARCHAR)
RETURNS TABLE (
    id INTEGER,
    nama VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    pesan TEXT,
    tipe_kontak VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT k.id, k.nama, k.email, k.nomor_telepon, k.pesan, k.tipe_kontak, k.status, k.created_at, k.updated_at
    FROM kontaks k
    WHERE k.status = p_status
    ORDER BY k.created_at DESC;
END;
$$ LANGUAGE plpgsql;

-- Create new kontak
CREATE OR REPLACE FUNCTION create_kontak(
    p_nama VARCHAR,
    p_email VARCHAR,
    p_nomor_telepon VARCHAR,
    p_pesan TEXT,
    p_tipe_kontak VARCHAR DEFAULT 'umum',
    p_status VARCHAR DEFAULT 'pending'
)
RETURNS TABLE (
    id INTEGER,
    message VARCHAR
) AS $$
DECLARE
    v_id INTEGER;
BEGIN
    INSERT INTO kontaks (nama, email, nomor_telepon, pesan, tipe_kontak, status, created_at, updated_at)
    VALUES (p_nama, p_email, p_nomor_telepon, p_pesan, p_tipe_kontak, p_status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    RETURNING kontaks.id INTO v_id;
    
    RETURN QUERY SELECT v_id, 'Kontak created successfully'::VARCHAR;
END;
$$ LANGUAGE plpgsql;

-- Update kontak
CREATE OR REPLACE FUNCTION update_kontak(
    p_id INTEGER,
    p_nama VARCHAR,
    p_email VARCHAR,
    p_nomor_telepon VARCHAR,
    p_pesan TEXT,
    p_status VARCHAR
)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    UPDATE kontaks
    SET nama = p_nama,
        email = p_email,
        nomor_telepon = p_nomor_telepon,
        pesan = p_pesan,
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Kontak updated successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Kontak not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Delete kontak
CREATE OR REPLACE FUNCTION delete_kontak(p_id INTEGER)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    DELETE FROM kontaks WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Kontak deleted successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Kontak not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- PRODUKS TABLE STORED PROCEDURES
-- ============================================================================

-- Get all products
CREATE OR REPLACE FUNCTION get_all_produks()
RETURNS TABLE (
    id INTEGER,
    nama_produk VARCHAR,
    deskripsi TEXT,
    harga NUMERIC,
    stok INTEGER,
    kategori VARCHAR,
    gambar_url VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_produk, p.deskripsi, p.harga, p.stok, p.kategori, p.gambar_url, p.status, p.created_at, p.updated_at
    FROM produks p
    ORDER BY p.id ASC;
END;
$$ LANGUAGE plpgsql;

-- Get product by ID
CREATE OR REPLACE FUNCTION get_produk_by_id(p_id INTEGER)
RETURNS TABLE (
    id INTEGER,
    nama_produk VARCHAR,
    deskripsi TEXT,
    harga NUMERIC,
    stok INTEGER,
    kategori VARCHAR,
    gambar_url VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_produk, p.deskripsi, p.harga, p.stok, p.kategori, p.gambar_url, p.status, p.created_at, p.updated_at
    FROM produks p
    WHERE p.id = p_id;
END;
$$ LANGUAGE plpgsql;

-- Get products by category
CREATE OR REPLACE FUNCTION get_produks_by_category(p_kategori VARCHAR)
RETURNS TABLE (
    id INTEGER,
    nama_produk VARCHAR,
    deskripsi TEXT,
    harga NUMERIC,
    stok INTEGER,
    kategori VARCHAR,
    gambar_url VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_produk, p.deskripsi, p.harga, p.stok, p.kategori, p.gambar_url, p.status, p.created_at, p.updated_at
    FROM produks p
    WHERE p.kategori = p_kategori AND p.status = 'active'
    ORDER BY p.nama_produk ASC;
END;
$$ LANGUAGE plpgsql;

-- Create new product
CREATE OR REPLACE FUNCTION create_produk(
    p_nama_produk VARCHAR,
    p_deskripsi TEXT,
    p_harga NUMERIC,
    p_stok INTEGER,
    p_kategori VARCHAR,
    p_gambar_url VARCHAR DEFAULT NULL,
    p_status VARCHAR DEFAULT 'active'
)
RETURNS TABLE (
    id INTEGER,
    message VARCHAR
) AS $$
DECLARE
    v_id INTEGER;
BEGIN
    INSERT INTO produks (nama_produk, deskripsi, harga, stok, kategori, gambar_url, status, created_at, updated_at)
    VALUES (p_nama_produk, p_deskripsi, p_harga, p_stok, p_kategori, p_gambar_url, p_status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    RETURNING produks.id INTO v_id;
    
    RETURN QUERY SELECT v_id, 'Produk created successfully'::VARCHAR;
END;
$$ LANGUAGE plpgsql;

-- Update product
CREATE OR REPLACE FUNCTION update_produk(
    p_id INTEGER,
    p_nama_produk VARCHAR,
    p_deskripsi TEXT,
    p_harga NUMERIC,
    p_stok INTEGER,
    p_kategori VARCHAR,
    p_gambar_url VARCHAR,
    p_status VARCHAR
)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    UPDATE produks
    SET nama_produk = p_nama_produk,
        deskripsi = p_deskripsi,
        harga = p_harga,
        stok = p_stok,
        kategori = p_kategori,
        gambar_url = p_gambar_url,
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Produk updated successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Produk not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Update product stock
CREATE OR REPLACE FUNCTION update_produk_stok(
    p_id INTEGER,
    p_stok INTEGER
)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    UPDATE produks
    SET stok = p_stok,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Stok updated successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Produk not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Delete product
CREATE OR REPLACE FUNCTION delete_produk(p_id INTEGER)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    DELETE FROM produks WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Produk deleted successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Produk not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Get low stock products (stok < threshold)
CREATE OR REPLACE FUNCTION get_low_stock_produks(p_threshold INTEGER DEFAULT 10)
RETURNS TABLE (
    id INTEGER,
    nama_produk VARCHAR,
    stok INTEGER,
    kategori VARCHAR
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_produk, p.stok, p.kategori
    FROM produks p
    WHERE p.stok < p_threshold AND p.status = 'active'
    ORDER BY p.stok ASC;
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- PENGGUNA TABLE STORED PROCEDURES
-- ============================================================================

-- Get all pengguna (users)
CREATE OR REPLACE FUNCTION get_all_pengguna()
RETURNS TABLE (
    id INTEGER,
    nama_pengguna VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    alamat TEXT,
    role VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_pengguna, p.email, p.nomor_telepon, p.alamat, p.role, p.status, p.created_at, p.updated_at
    FROM pengguna p
    ORDER BY p.id ASC;
END;
$$ LANGUAGE plpgsql;

-- Get pengguna by ID
CREATE OR REPLACE FUNCTION get_pengguna_by_id(p_id INTEGER)
RETURNS TABLE (
    id INTEGER,
    nama_pengguna VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    alamat TEXT,
    role VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_pengguna, p.email, p.nomor_telepon, p.alamat, p.role, p.status, p.created_at, p.updated_at
    FROM pengguna p
    WHERE p.id = p_id;
END;
$$ LANGUAGE plpgsql;

-- Get pengguna by email
CREATE OR REPLACE FUNCTION get_pengguna_by_email(p_email VARCHAR)
RETURNS TABLE (
    id INTEGER,
    nama_pengguna VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    alamat TEXT,
    role VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_pengguna, p.email, p.nomor_telepon, p.alamat, p.role, p.status, p.created_at, p.updated_at
    FROM pengguna p
    WHERE LOWER(p.email) = LOWER(p_email);
END;
$$ LANGUAGE plpgsql;

-- Get pengguna by role
CREATE OR REPLACE FUNCTION get_pengguna_by_role(p_role VARCHAR)
RETURNS TABLE (
    id INTEGER,
    nama_pengguna VARCHAR,
    email VARCHAR,
    nomor_telepon VARCHAR,
    role VARCHAR,
    status VARCHAR,
    created_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, p.nama_pengguna, p.email, p.nomor_telepon, p.role, p.status, p.created_at
    FROM pengguna p
    WHERE p.role = p_role AND p.status = 'active'
    ORDER BY p.nama_pengguna ASC;
END;
$$ LANGUAGE plpgsql;

-- Create new pengguna
CREATE OR REPLACE FUNCTION create_pengguna(
    p_nama_pengguna VARCHAR,
    p_email VARCHAR,
    p_nomor_telepon VARCHAR,
    p_alamat TEXT,
    p_role VARCHAR DEFAULT 'user',
    p_status VARCHAR DEFAULT 'active'
)
RETURNS TABLE (
    id INTEGER,
    message VARCHAR
) AS $$
DECLARE
    v_id INTEGER;
BEGIN
    -- Check if email already exists
    IF EXISTS (SELECT 1 FROM pengguna WHERE LOWER(email) = LOWER(p_email)) THEN
        RETURN QUERY SELECT 0, 'Email already exists'::VARCHAR;
        RETURN;
    END IF;
    
    INSERT INTO pengguna (nama_pengguna, email, nomor_telepon, alamat, role, status, created_at, updated_at)
    VALUES (p_nama_pengguna, p_email, p_nomor_telepon, p_alamat, p_role, p_status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    RETURNING pengguna.id INTO v_id;
    
    RETURN QUERY SELECT v_id, 'Pengguna created successfully'::VARCHAR;
END;
$$ LANGUAGE plpgsql;

-- Update pengguna
CREATE OR REPLACE FUNCTION update_pengguna(
    p_id INTEGER,
    p_nama_pengguna VARCHAR,
    p_email VARCHAR,
    p_nomor_telepon VARCHAR,
    p_alamat TEXT,
    p_role VARCHAR,
    p_status VARCHAR
)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    -- Check if email is being changed and if new email already exists
    IF p_email != (SELECT email FROM pengguna WHERE id = p_id) THEN
        IF EXISTS (SELECT 1 FROM pengguna WHERE LOWER(email) = LOWER(p_email) AND id != p_id) THEN
            RETURN QUERY SELECT false, 'Email already exists'::VARCHAR;
            RETURN;
        END IF;
    END IF;
    
    UPDATE pengguna
    SET nama_pengguna = p_nama_pengguna,
        email = p_email,
        nomor_telepon = p_nomor_telepon,
        alamat = p_alamat,
        role = p_role,
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Pengguna updated successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Pengguna not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Update pengguna status
CREATE OR REPLACE FUNCTION update_pengguna_status(
    p_id INTEGER,
    p_status VARCHAR
)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    UPDATE pengguna
    SET status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Status updated successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Pengguna not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Delete pengguna
CREATE OR REPLACE FUNCTION delete_pengguna(p_id INTEGER)
RETURNS TABLE (
    success BOOLEAN,
    message VARCHAR
) AS $$
BEGIN
    DELETE FROM pengguna WHERE id = p_id;
    
    IF FOUND THEN
        RETURN QUERY SELECT true, 'Pengguna deleted successfully'::VARCHAR;
    ELSE
        RETURN QUERY SELECT false, 'Pengguna not found'::VARCHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Count active pengguna
CREATE OR REPLACE FUNCTION count_active_pengguna()
RETURNS TABLE (
    total_aktif INTEGER
) AS $$
BEGIN
    RETURN QUERY
    SELECT COUNT(*)::INTEGER
    FROM pengguna
    WHERE status = 'active';
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- UTILITY FUNCTIONS
-- ============================================================================

-- Get database statistics
CREATE OR REPLACE FUNCTION get_database_statistics()
RETURNS TABLE (
    total_forms INTEGER,
    total_kontaks INTEGER,
    total_produks INTEGER,
    total_pengguna INTEGER,
    total_active_pengguna INTEGER
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        (SELECT COUNT(*)::INTEGER FROM forms),
        (SELECT COUNT(*)::INTEGER FROM kontaks),
        (SELECT COUNT(*)::INTEGER FROM produks),
        (SELECT COUNT(*)::INTEGER FROM pengguna),
        (SELECT COUNT(*)::INTEGER FROM pengguna WHERE status = 'active');
END;
$$ LANGUAGE plpgsql;

-- Generate activity report (last N days)
CREATE OR REPLACE FUNCTION get_activity_report(p_days INTEGER DEFAULT 30)
RETURNS TABLE (
    table_name VARCHAR,
    created_count INTEGER,
    updated_count INTEGER
) AS $$
BEGIN
    RETURN QUERY
    SELECT 'forms'::VARCHAR, 
           COUNT(CASE WHEN created_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days THEN 1 END)::INTEGER,
           COUNT(CASE WHEN updated_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days AND updated_at > created_at THEN 1 END)::INTEGER
    FROM forms
    UNION ALL
    SELECT 'kontaks'::VARCHAR,
           COUNT(CASE WHEN created_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days THEN 1 END)::INTEGER,
           COUNT(CASE WHEN updated_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days AND updated_at > created_at THEN 1 END)::INTEGER
    FROM kontaks
    UNION ALL
    SELECT 'produks'::VARCHAR,
           COUNT(CASE WHEN created_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days THEN 1 END)::INTEGER,
           COUNT(CASE WHEN updated_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days AND updated_at > created_at THEN 1 END)::INTEGER
    FROM produks
    UNION ALL
    SELECT 'pengguna'::VARCHAR,
           COUNT(CASE WHEN created_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days THEN 1 END)::INTEGER,
           COUNT(CASE WHEN updated_at >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days AND updated_at > created_at THEN 1 END)::INTEGER
    FROM pengguna;
END;
$$ LANGUAGE plpgsql;
