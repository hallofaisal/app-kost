-- Struktur Database Aplikasi Kos

-- 1. Tabel Penghuni
CREATE TABLE tb_penghuni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_ktp VARCHAR(20) NOT NULL UNIQUE,
    no_hp VARCHAR(15) NOT NULL,
    tgl_masuk DATE NOT NULL,
    tgl_keluar DATE
);

-- 2. Tabel Kamar
CREATE TABLE tb_kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor VARCHAR(10) NOT NULL UNIQUE,
    harga DECIMAL(12,2) NOT NULL
);

-- 3. Tabel Barang
CREATE TABLE tb_barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga DECIMAL(12,2) NOT NULL
);

-- 4. Tabel Kamar Penghuni (relasi kamar-penghuni)
CREATE TABLE tb_kmr_penghuni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_kamar INT NOT NULL,
    id_penghuni INT NOT NULL,
    tgl_masuk DATE NOT NULL,
    tgl_keluar DATE,
    FOREIGN KEY (id_kamar) REFERENCES tb_kamar(id),
    FOREIGN KEY (id_penghuni) REFERENCES tb_penghuni(id),
    INDEX idx_kamar (id_kamar),
    INDEX idx_penghuni (id_penghuni)
);

-- 5. Tabel Barang Bawaan
CREATE TABLE tb_brng_bawaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penghuni INT NOT NULL,
    id_barang INT NOT NULL,
    FOREIGN KEY (id_penghuni) REFERENCES tb_penghuni(id),
    FOREIGN KEY (id_barang) REFERENCES tb_barang(id),
    INDEX idx_penghuni (id_penghuni),
    INDEX idx_barang (id_barang)
);

-- 6. Tabel Tagihan
CREATE TABLE tb_tagihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bulan CHAR(7) NOT NULL, -- format: YYYY-MM
    id_kmr_penghuni INT NOT NULL,
    jml_tagihan DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_kmr_penghuni) REFERENCES tb_kmr_penghuni(id),
    INDEX idx_kmr_penghuni (id_kmr_penghuni),
    INDEX idx_bulan (bulan)
);

-- 7. Tabel Bayar
CREATE TABLE tb_bayar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tagihan INT NOT NULL,
    jml_bayar DECIMAL(12,2) NOT NULL,
    status ENUM('pending','lunas') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (id_tagihan) REFERENCES tb_tagihan(id),
    INDEX idx_tagihan (id_tagihan),
    INDEX idx_status (status)
);

-- Sample Data
INSERT INTO tb_penghuni (nama, no_ktp, no_hp, tgl_masuk, tgl_keluar) VALUES
('Budi Santoso', '1234567890123456', '081234567890', '2024-01-10', NULL),
('Siti Aminah', '2345678901234567', '082345678901', '2024-02-01', NULL);

INSERT INTO tb_kamar (nomor, harga) VALUES
('A1', 1000000.00),
('A2', 1200000.00);

INSERT INTO tb_barang (nama, harga) VALUES
('Kipas Angin', 200000.00),
('Meja Belajar', 150000.00);

INSERT INTO tb_kmr_penghuni (id_kamar, id_penghuni, tgl_masuk, tgl_keluar) VALUES
(1, 1, '2024-01-10', NULL),
(2, 2, '2024-02-01', NULL);

INSERT INTO tb_brng_bawaan (id_penghuni, id_barang) VALUES
(1, 1),
(2, 2);

INSERT INTO tb_tagihan (bulan, id_kmr_penghuni, jml_tagihan) VALUES
('2024-03', 1, 1000000.00),
('2024-03', 2, 1200000.00);

INSERT INTO tb_bayar (id_tagihan, jml_bayar, status) VALUES
(1, 1000000.00, 'lunas'),
(2, 600000.00, 'pending'); 