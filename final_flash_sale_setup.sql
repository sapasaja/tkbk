-- FINAL FLASH SALE SETUP - AMAN DAN LENGKAP
USE toko_marketplacemuv1;

-- 1. Hapus modul flash_sale jika sudah ada (untuk menghindari duplikasi)
DELETE FROM modul WHERE link = 'flash_sale';

-- 2. Tambahkan modul flash_sale baru
INSERT INTO `modul` (`id_modul`, `nama_modul`, `link`, `static_content`, `gambar`, `publish`, `status`, `urutan`, `aktif`) VALUES
(NULL, 'Flash Sale', 'flash_sale', '0', 'fa-bolt', 'Y', 'admin', '0', 'Y');

-- 3. Dapatkan ID modul yang baru dibuat
SET @flash_sale_modul_id = LAST_INSERT_ID();

-- 4. Tambahkan akses untuk admin (jika belum ada)
INSERT IGNORE INTO `users_modul` (`id_session`, `id_modul`) 
SELECT DISTINCT u.id_session, @flash_sale_modul_id
FROM users u 
WHERE u.level = 'admin';

-- 5. Hapus data flash sale lama jika ada
DELETE FROM rb_flash_sale;

-- 6. Masukkan flash sale test (ganti id_produk dengan ID produk yang ada)
INSERT INTO `rb_flash_sale` (`id_produk`, `harga_flash_sale`, `diskon_persen`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`) VALUES
(1, 15000.00, 25, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'aktif', NOW());

-- 7. Verifikasi setup
SELECT '=== FLASH SALE SETUP COMPLETE ===' as status;
SELECT 'Tabel rb_flash_sale:' as info, COUNT(*) as jumlah FROM rb_flash_sale;
SELECT 'Modul flash_sale:' as info, COUNT(*) as jumlah FROM modul WHERE link = 'flash_sale';
SELECT 'Akses admin:' as info, COUNT(*) as jumlah FROM users_modul WHERE id_modul = @flash_sale_modul_id; 