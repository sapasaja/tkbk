-- Cek struktur tabel rb_penjualan
USE toko_marketplacemuv1;

-- Cek struktur tabel penjualan
DESCRIBE rb_penjualan;

-- Cek beberapa data sample
SELECT * FROM rb_penjualan LIMIT 3;

-- Cek kolom yang ada
SHOW COLUMNS FROM rb_penjualan; 