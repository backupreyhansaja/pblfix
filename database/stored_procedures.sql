DELIMITER $$

-- Stored Procedure untuk Create Form
CREATE PROCEDURE sp_create_form(
    IN p_nama VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_pesan TEXT,
    OUT p_id INT
)
BEGIN
    INSERT INTO forms (nama, email, pesan, created_at) 
    VALUES (p_nama, p_email, p_pesan, NOW());
    SET p_id = LAST_INSERT_ID();
END$$

-- Stored Procedure untuk Read Semua Form
CREATE PROCEDURE sp_get_forms()
BEGIN
    SELECT id, nama, email, pesan, created_at FROM forms ORDER BY created_at DESC;
END$$

-- Stored Procedure untuk Read Form by ID
CREATE PROCEDURE sp_get_form_by_id(IN p_id INT)
BEGIN
    SELECT id, nama, email, pesan, created_at FROM forms WHERE id = p_id;
END$$

-- Stored Procedure untuk Update Form
CREATE PROCEDURE sp_update_form(
    IN p_id INT,
    IN p_nama VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_pesan TEXT
)
BEGIN
    UPDATE forms SET nama = p_nama, email = p_email, pesan = p_pesan, updated_at = NOW()
    WHERE id = p_id;
END$$

-- Stored Procedure untuk Delete Form
CREATE PROCEDURE sp_delete_form(IN p_id INT)
BEGIN
    DELETE FROM forms WHERE id = p_id;
END$$

-- Stored Procedure untuk Create Kontak
CREATE PROCEDURE sp_create_kontak(
    IN p_nama VARCHAR(255),
    IN p_telepon VARCHAR(20),
    IN p_alamat TEXT,
    OUT p_id INT
)
BEGIN
    INSERT INTO kontaks (nama, telepon, alamat, created_at) 
    VALUES (p_nama, p_telepon, p_alamat, NOW());
    SET p_id = LAST_INSERT_ID();
END$$

-- Stored Procedure untuk Read Semua Kontak
CREATE PROCEDURE sp_get_kontaks()
BEGIN
    SELECT id, nama, telepon, alamat, created_at FROM kontaks ORDER BY created_at DESC;
END$$

-- Stored Procedure untuk Read Kontak by ID
CREATE PROCEDURE sp_get_kontak_by_id(IN p_id INT)
BEGIN
    SELECT id, nama, telepon, alamat, created_at FROM kontaks WHERE id = p_id;
END$$

-- Stored Procedure untuk Update Kontak
CREATE PROCEDURE sp_update_kontak(
    IN p_id INT,
    IN p_nama VARCHAR(255),
    IN p_telepon VARCHAR(20),
    IN p_alamat TEXT
)
BEGIN
    UPDATE kontaks SET nama = p_nama, telepon = p_telepon, alamat = p_alamat, updated_at = NOW()
    WHERE id = p_id;
END$$

-- Stored Procedure untuk Delete Kontak
CREATE PROCEDURE sp_delete_kontak(IN p_id INT)
BEGIN
    DELETE FROM kontaks WHERE id = p_id;
END$$

-- Stored Procedure untuk Create Produk
CREATE PROCEDURE sp_create_produk(
    IN p_nama VARCHAR(255),
    IN p_deskripsi TEXT,
    IN p_harga DECIMAL(10, 2),
    IN p_stok INT,
    OUT p_id INT
)
BEGIN
    INSERT INTO produks (nama, deskripsi, harga, stok, created_at) 
    VALUES (p_nama, p_deskripsi, p_harga, p_stok, NOW());
    SET p_id = LAST_INSERT_ID();
END$$

-- Stored Procedure untuk Read Semua Produk
CREATE PROCEDURE sp_get_produks()
BEGIN
    SELECT id, nama, deskripsi, harga, stok, created_at FROM produks ORDER BY created_at DESC;
END$$

-- Stored Procedure untuk Read Produk by ID
CREATE PROCEDURE sp_get_produk_by_id(IN p_id INT)
BEGIN
    SELECT id, nama, deskripsi, harga, stok, created_at FROM produks WHERE id = p_id;
END$$

-- Stored Procedure untuk Update Produk
CREATE PROCEDURE sp_update_produk(
    IN p_id INT,
    IN p_nama VARCHAR(255),
    IN p_deskripsi TEXT,
    IN p_harga DECIMAL(10, 2),
    IN p_stok INT
)
BEGIN
    UPDATE produks SET nama = p_nama, deskripsi = p_deskripsi, harga = p_harga, stok = p_stok, updated_at = NOW()
    WHERE id = p_id;
END$$

-- Stored Procedure untuk Delete Produk
CREATE PROCEDURE sp_delete_produk(IN p_id INT)
BEGIN
    DELETE FROM produks WHERE id = p_id;
END$$

-- Stored Procedure untuk Create Pengguna
CREATE PROCEDURE sp_create_pengguna(
    IN p_username VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    IN p_nama VARCHAR(255),
    OUT p_id INT
)
BEGIN
    INSERT INTO penggunas (username, email, password, nama, created_at) 
    VALUES (p_username, p_email, p_password, p_nama, NOW());
    SET p_id = LAST_INSERT_ID();
END$$

-- Stored Procedure untuk Read Semua Pengguna
CREATE PROCEDURE sp_get_penggunas()
BEGIN
    SELECT id, username, email, nama, created_at FROM penggunas ORDER BY created_at DESC;
END$$

-- Stored Procedure untuk Read Pengguna by ID
CREATE PROCEDURE sp_get_pengguna_by_id(IN p_id INT)
BEGIN
    SELECT id, username, email, nama, created_at FROM penggunas WHERE id = p_id;
END$$

-- Stored Procedure untuk Update Pengguna
CREATE PROCEDURE sp_update_pengguna(
    IN p_id INT,
    IN p_username VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_nama VARCHAR(255)
)
BEGIN
    UPDATE penggunas SET username = p_username, email = p_email, nama = p_nama, updated_at = NOW()
    WHERE id = p_id;
END$$

-- Stored Procedure untuk Delete Pengguna
CREATE PROCEDURE sp_delete_pengguna(IN p_id INT)
BEGIN
    DELETE FROM penggunas WHERE id = p_id;
END$$

DELIMITER ;