-- Buat tabel Flash Sale
USE toko_marketplacemuv1;

-- Hapus tabel jika sudah ada
DROP TABLE IF EXISTS `rb_flash_sale`;

-- Buat tabel baru tanpa foreign key constraint
CREATE TABLE `rb_flash_sale` (
  `id_flash_sale` int(11) NOT NULL AUTO_INCREMENT,
  `id_produk` int(11) NOT NULL,
  `harga_flash_sale` decimal(10,2) NOT NULL,
  `diskon_persen` int(3) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id_flash_sale`),
  KEY `id_produk` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan akses menu flash_sale untuk admin (jika belum ada)
INSERT IGNORE INTO `rb_users_akses` (`id_akses`, `id_session`, `username`, `modul`, `id_session_users`) VALUES
(NULL, 'admin', 'admin', 'flash_sale', 'admin');

-- Sample data flash sale (opsional - uncomment jika ingin menambah data sample)
-- INSERT INTO `rb_flash_sale` (`id_produk`, `harga_flash_sale`, `diskon_persen`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`) VALUES
-- (1, 15000.00, 25, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 'aktif', NOW()); 