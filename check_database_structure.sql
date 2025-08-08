-- Script untuk mengecek struktur database
USE toko_marketplacemuv1;

-- 1. Cek tabel yang ada
SHOW TABLES;

-- 2. Cek apakah tabel flash_sale sudah ada
SHOW TABLES LIKE 'rb_flash_sale';

-- 3. Cek struktur tabel flash_sale jika ada
DESCRIBE rb_flash_sale;

-- 4. Cek tabel users atau admin yang ada
SHOW TABLES LIKE '%user%';
SHOW TABLES LIKE '%admin%';
SHOW TABLES LIKE '%akses%';

-- 5. Cek struktur tabel produk
DESCRIBE rb_produk; 