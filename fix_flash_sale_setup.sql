-- Script untuk memperbaiki setup Flash Sale
USE toko_marketplacemuv1;

-- 1. Hapus tabel flash_sale jika sudah ada (untuk membuat ulang)
DROP TABLE IF EXISTS `rb_flash_sale`;

-- 2. Buat tabel flash_sale baru
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

-- 3. Cek tabel users yang ada untuk akses menu
-- Jalankan query ini untuk melihat tabel apa saja yang ada:
-- SHOW TABLES LIKE '%user%';
-- SHOW TABLES LIKE '%admin%';

-- 4. Jika ada tabel users, tambahkan akses flash_sale
-- (Uncomment salah satu sesuai dengan struktur database Anda)

-- Opsi 1: Jika ada tabel users
-- INSERT INTO `users` (`username`, `level`, `modul`) VALUES ('admin', 'admin', 'flash_sale');

-- Opsi 2: Jika ada tabel admin
-- INSERT INTO `admin` (`username`, `level`, `modul`) VALUES ('admin', 'admin', 'flash_sale');

-- Opsi 3: Jika ada tabel lain untuk akses
-- INSERT INTO `nama_tabel_akses` (`username`, `modul`) VALUES ('admin', 'flash_sale');

-- 5. Verifikasi tabel terbuat
SELECT 'Tabel rb_flash_sale berhasil dibuat!' as status; 