# Sistem Produk Digital (Ebook) - Toko Buku

## Deskripsi
Sistem ini memungkinkan toko buku untuk menjual produk digital (ebook) yang dapat diakses oleh customer setelah pembayaran berhasil. Customer dapat mengunduh ebook yang telah dibeli dan melihat preview sebelum membeli.

## Fitur Utama

### 1. Manajemen Produk Digital (Admin)
- **Upload Ebook**: Admin dapat mengupload file ebook (PDF, DOC, DOCX, EPUB, MOBI)
- **Preview File**: Admin dapat mengupload file preview (PDF, gambar)
- **Kategori Digital**: Kategori khusus untuk produk digital
- **Status Aktif/Nonaktif**: Kontrol ketersediaan ebook

### 2. Akses Customer
- **My Ebooks**: Halaman khusus customer untuk melihat ebook yang dibeli
- **Download Ebook**: Customer dapat mengunduh ebook setelah pembayaran
- **Preview Ebook**: Customer dapat melihat preview sebelum membeli
- **Detail Ebook**: Informasi lengkap tentang ebook

### 3. Keamanan
- **Akses Terbatas**: Hanya customer yang sudah membeli yang bisa mengakses
- **Expired Date**: Sistem expired date untuk akses ebook
- **Download Log**: Mencatat semua aktivitas download
- **IP Tracking**: Mencatat IP address dan user agent

### 4. Dashboard Admin
- **Produk Digital**: Kelola semua produk digital
- **Akses Ebook**: Lihat siapa saja yang memiliki akses
- **Download Log**: Riwayat download ebook

## Struktur Database

### Tabel Baru
1. **rb_produk_digital**: Data produk digital
2. **rb_akses_ebook**: Data akses customer ke ebook
3. **rb_download_log**: Log aktivitas download

### Modifikasi Tabel
1. **rb_produk**: Menambah kolom `is_digital` dan `digital_expired_days`
2. **rb_kategori_produk**: Menambah kategori "Produk Digital & Ebook"

## Cara Penggunaan

### Untuk Admin
1. **Tambah Produk Digital**:
   - Buka menu "Produk Digital" di dashboard admin
   - Klik "Tambah Produk Digital"
   - Pilih produk yang akan dijadikan digital
   - Upload file ebook dan preview (opsional)
   - Simpan

2. **Kelola Akses**:
   - Menu "Akses Ebook" untuk melihat customer yang memiliki akses
   - Menu "Download Log" untuk melihat riwayat download

### Untuk Customer
1. **Beli Produk Digital**:
   - Pilih produk digital di toko
   - Lakukan pembayaran seperti biasa
   - Setelah pembayaran dikonfirmasi, akses ebook otomatis diberikan

2. **Akses Ebook**:
   - Login ke akun customer
   - Klik "My Ebooks" di header
   - Pilih ebook yang ingin diunduh atau preview

## File yang Dibuat/Dimodifikasi

### Controller
- `application/controllers/Administrator.php` - Tambah fungsi produk digital
- `application/controllers/Ebook.php` - Controller untuk customer
- `application/controllers/Konfirmasi.php` - Modifikasi untuk akses otomatis

### View
- `application/views/administrator/additional/mod_produk_digital/` - View admin
- `application/views/phpmu-tigo/ebook_list.php` - Daftar ebook customer
- `application/views/phpmu-tigo/ebook_detail.php` - Detail ebook
- `application/views/phpmu-tigo/ebook_preview.php` - Preview ebook

### Database
- `DATABASE/digital_products.sql` - Script database

### Menu
- `application/views/administrator/menu-admin.php` - Menu admin
- `application/views/phpmu-tigo/header.php` - Menu customer

## Keamanan
- File ebook disimpan di `asset/files/ebook/` dengan akses terbatas
- Preview file disimpan di `asset/files/ebook/preview/` dengan akses publik
- Setiap download dicatat dengan IP address dan user agent
- Akses ebook hanya diberikan setelah pembayaran dikonfirmasi

## Format File yang Didukung
- **Ebook**: PDF, DOC, DOCX, EPUB, MOBI (Max: 50MB)
- **Preview**: PDF, JPG, JPEG, PNG (Max: 5MB)

## Catatan Penting
1. Pastikan direktori `asset/files/ebook/` dan `asset/files/ebook/preview/` sudah dibuat
2. Set permission direktori agar bisa ditulis oleh web server
3. Jalankan script SQL `DATABASE/digital_products.sql` untuk membuat tabel
4. Sistem ini terintegrasi dengan sistem pembayaran yang sudah ada

## Troubleshooting
- Jika file tidak bisa diupload, cek permission direktori
- Jika akses ebook tidak muncul, cek apakah pembayaran sudah dikonfirmasi
- Jika download gagal, cek apakah file masih ada di server 