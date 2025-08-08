-- SQL Sederhana untuk Tabel Flash Sale
-- Jalankan query ini di phpMyAdmin atau MySQL client

-- 1. Pilih database terlebih dahulu
USE toko_marketplacemuv1;

-- 2. Buat tabel flash sale
CREATE TABLE `rb_flash_sale` (
  `id_flash_sale` int(11) NOT NULL AUTO_INCREMENT,
  `id_produk` int(11) NOT NULL,
  `harga_flash_sale` decimal(10,2) NOT NULL,
  `diskon_persen` int(3) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id_flash_sale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tambahkan akses menu untuk admin
INSERT INTO `rb_users_akses` (`id_session`, `username`, `modul`, `id_session_users`) 
VALUES ('admin', 'admin', 'flash_sale', 'admin')
ON DUPLICATE KEY UPDATE `modul` = 'flash_sale'; 