CREATE DATABASE IF NOT EXISTS db_pos_kasir CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pos_kasir;

CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL, role ENUM('admin','kasir') NOT NULL DEFAULT 'kasir', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS kategori (
 id INT AUTO_INCREMENT PRIMARY KEY, nama_kategori VARCHAR(100) NOT NULL UNIQUE
);
CREATE TABLE IF NOT EXISTS produk (
 id INT AUTO_INCREMENT PRIMARY KEY, kode_produk VARCHAR(50) NOT NULL UNIQUE, nama_produk VARCHAR(150) NOT NULL,
 id_kategori INT NULL, harga_modal DECIMAL(12,2) NOT NULL DEFAULT 0, harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0,
 stok INT NOT NULL DEFAULT 0, stok_minimum INT NOT NULL DEFAULT 5, gambar VARCHAR(255) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS transaksi (
 id INT AUTO_INCREMENT PRIMARY KEY, kode_transaksi VARCHAR(40) NOT NULL UNIQUE, user_id INT NULL,
 tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, total DECIMAL(12,2) NOT NULL,
 bayar DECIMAL(12,2) NOT NULL, kembalian DECIMAL(12,2) NOT NULL, keuntungan DECIMAL(12,2) NOT NULL,
 CONSTRAINT fk_transaksi_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS detail_transaksi (
 id INT AUTO_INCREMENT PRIMARY KEY, transaksi_id INT NOT NULL, produk_id INT NULL, nama_produk VARCHAR(150) NOT NULL,
 harga DECIMAL(12,2) NOT NULL, jumlah INT NOT NULL, subtotal DECIMAL(12,2) NOT NULL, keuntungan DECIMAL(12,2) NOT NULL,
 CONSTRAINT fk_detail_transaksi FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
 CONSTRAINT fk_detail_produk FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS pengaturan (
 id INT PRIMARY KEY, nama_toko VARCHAR(150) NOT NULL DEFAULT 'POS Kasir', alamat TEXT NULL, footer_struk VARCHAR(255) DEFAULT 'Terima kasih telah berbelanja'
);
INSERT IGNORE INTO kategori (nama_kategori) VALUES ('Makanan'),('Minuman'),('Kebutuhan Harian');
INSERT IGNORE INTO pengaturan (id,nama_toko,alamat,footer_struk) VALUES (1,'TOKO ANDINI','Alamat toko Anda','Terima kasih telah berbelanja');
-- Buat akun dengan password_hash melalui setup_admin.php (petunjuk tersedia di README).
