-- Fix Diskon dan Flash Sale Issues
-- File: fix_diskon_flash_sale.sql
-- Tanggal: 2025-08-06

-- 1. Periksa struktur tabel diskon
DESCRIBE rb_produk_diskon;

-- 2. Periksa data diskon yang ada
SELECT pd.id_produk, pd.diskon, p.nama_produk, p.harga_konsumen 
FROM rb_produk_diskon pd 
JOIN rb_produk p ON pd.id_produk = p.id_produk 
WHERE pd.diskon > 0 
LIMIT 10;

-- 3. Periksa struktur tabel flash sale
DESCRIBE rb_flash_sale;

-- 4. Periksa data flash sale yang aktif
SELECT fs.*, p.nama_produk, p.harga_konsumen 
FROM rb_flash_sale fs 
JOIN rb_produk p ON fs.id_produk = p.id_produk 
WHERE fs.status = 'aktif' 
AND fs.tanggal_mulai <= NOW() 
AND fs.tanggal_selesai >= NOW();

-- 5. Periksa apakah ada produk yang tidak memiliki gambar
SELECT id_produk, nama_produk, gambar 
FROM rb_produk 
WHERE gambar = '' OR gambar IS NULL 
LIMIT 10;

-- 6. Update produk yang tidak memiliki gambar dengan default image
UPDATE rb_produk 
SET gambar = 'no-image.png' 
WHERE gambar = '' OR gambar IS NULL;

-- 7. Periksa apakah ada diskon yang tidak valid (lebih besar dari harga)
SELECT pd.id_produk, pd.diskon, p.nama_produk, p.harga_konsumen 
FROM rb_produk_diskon pd 
JOIN rb_produk p ON pd.id_produk = p.id_produk 
WHERE pd.diskon >= p.harga_konsumen;

-- 8. Perbaiki diskon yang tidak valid (set ke 0 jika lebih besar dari harga)
UPDATE rb_produk_diskon pd 
JOIN rb_produk p ON pd.id_produk = p.id_produk 
SET pd.diskon = 0 
WHERE pd.diskon >= p.harga_konsumen;

-- 9. Periksa flash sale yang sudah berakhir tapi masih aktif
SELECT fs.*, p.nama_produk 
FROM rb_flash_sale fs 
JOIN rb_produk p ON fs.id_produk = p.id_produk 
WHERE fs.status = 'aktif' 
AND fs.tanggal_selesai < NOW();

-- 10. Nonaktifkan flash sale yang sudah berakhir
UPDATE rb_flash_sale 
SET status = 'nonaktif' 
WHERE status = 'aktif' 
AND tanggal_selesai < NOW();

-- 11. Periksa hasil perbaikan
SELECT 'Diskon yang valid:' AS info, COUNT(*) AS jumlah 
FROM rb_produk_diskon pd 
JOIN rb_produk p ON pd.id_produk = p.id_produk 
WHERE pd.diskon > 0 AND pd.diskon < p.harga_konsumen;

SELECT 'Flash Sale Aktif:' AS info, COUNT(*) AS jumlah 
FROM rb_flash_sale 
WHERE status = 'aktif' 
AND tanggal_mulai <= NOW() 
AND tanggal_selesai >= NOW();

-- 12. Tambahkan index untuk optimasi query

-- Hapus index jika sudah ada, lalu tambahkan kembali agar tidak terjadi duplikat
-- Cek terlebih dahulu apakah database kamu mendukung IF EXISTS

-- Index untuk diskon
DROP INDEX IF EXISTS idx_produk_diskon ON rb_produk_diskon;
ALTER TABLE rb_produk_diskon ADD INDEX idx_produk_diskon (id_produk, diskon);

-- Index untuk flash sale aktif
DROP INDEX IF EXISTS idx_flash_sale_status ON rb_flash_sale;
ALTER TABLE rb_flash_sale ADD INDEX idx_flash_sale_status (status, tanggal_mulai, tanggal_selesai);

-- 13. Periksa apakah ada produk yang tidak terhubung dengan flash sale
SELECT p.id_produk, p.nama_produk 
FROM rb_produk p 
LEFT JOIN rb_flash_sale fs ON p.id_produk = fs.id_produk 
WHERE fs.id_produk IS NULL 
LIMIT 10;

-- 14. Periksa apakah ada flash sale yang tidak terhubung dengan produk
SELECT fs.id_flash_sale, fs.id_produk 
FROM rb_flash_sale fs 
LEFT JOIN rb_produk p ON fs.id_produk = p.id_produk 
WHERE p.id_produk IS NULL;

-- 15. Hapus flash sale yang tidak terhubung dengan produk
DELETE fs FROM rb_flash_sale fs 
LEFT JOIN rb_produk p ON fs.id_produk = p.id_produk 
WHERE p.id_produk IS NULL;

-- 16. Periksa hasil akhir
SELECT 'Total Produk:' AS info, COUNT(*) AS jumlah FROM rb_produk;
SELECT 'Total Diskon Aktif:' AS info, COUNT(*) AS jumlah FROM rb_produk_diskon WHERE diskon > 0;
SELECT 'Total Flash Sale Aktif:' AS info, COUNT(*) AS jumlah 
FROM rb_flash_sale 
WHERE status = 'aktif' 
AND tanggal_mulai <= NOW() 
AND tanggal_selesai >= NOW();
