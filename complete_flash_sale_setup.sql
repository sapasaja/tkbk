-- Script Lengkap Setup Flash Sale
USE toko_marketplacemuv1;

-- 1. Hapus tabel flash_sale jika sudah ada
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

-- 3. Tambahkan modul flash_sale ke tabel modul
INSERT INTO `modul` (`id_modul`, `nama_modul`, `link`, `static_content`, `gambar`, `publish`, `status`, `urutan`, `aktif`) VALUES
(NULL, 'Flash Sale', 'flash_sale', '0', 'fa-bolt', 'Y', 'admin', '0', 'Y');

-- 4. Dapatkan ID modul yang baru dibuat
SET @flash_sale_modul_id = LAST_INSERT_ID();

-- 5. Tambahkan akses flash_sale untuk semua admin
INSERT INTO `users_modul` (`id_session`, `id_modul`) 
SELECT DISTINCT u.id_session, @flash_sale_modul_id
FROM users u 
WHERE u.level = 'admin';

-- 6. Verifikasi setup
SELECT 'Flash Sale Setup Selesai!' as status;
SELECT 'Tabel rb_flash_sale:' as info, COUNT(*) as jumlah FROM rb_flash_sale;
SELECT 'Modul flash_sale:' as info, COUNT(*) as jumlah FROM modul WHERE link = 'flash_sale';
SELECT 'Akses admin:' as info, COUNT(*) as jumlah FROM users_modul WHERE id_modul = @flash_sale_modul_id; 