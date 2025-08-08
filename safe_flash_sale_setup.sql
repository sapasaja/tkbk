-- Script Aman Setup Flash Sale
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

-- 3. Cek apakah modul flash_sale sudah ada
SELECT COUNT(*) as modul_exists FROM modul WHERE link = 'flash_sale';

-- 4. Jika modul belum ada, tambahkan
INSERT IGNORE INTO `modul` (`id_modul`, `nama_modul`, `link`, `static_content`, `gambar`, `publish`, `status`, `urutan`, `aktif`) VALUES
(NULL, 'Flash Sale', 'flash_sale', '0', 'fa-bolt', 'Y', 'admin', '0', 'Y');

-- 5. Dapatkan ID modul flash_sale
SELECT @flash_sale_modul_id := id_modul FROM modul WHERE link = 'flash_sale' LIMIT 1;

-- 6. Tambahkan akses untuk admin (jika belum ada)
INSERT IGNORE INTO `users_modul` (`id_session`, `id_modul`) 
SELECT DISTINCT u.id_session, @flash_sale_modul_id
FROM users u 
WHERE u.level = 'admin' 
AND NOT EXISTS (
    SELECT 1 FROM users_modul um 
    WHERE um.id_session = u.id_session 
    AND um.id_modul = @flash_sale_modul_id
);

-- 7. Verifikasi hasil
SELECT '=== FLASH SALE SETUP COMPLETE ===' as status;
SELECT 'Tabel rb_flash_sale created successfully!' as info; 