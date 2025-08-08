<?php
/**
 * Test File untuk Sistem Produk Digital
 * Jalankan file ini untuk memverifikasi sistem sudah terpasang dengan benar
 */

echo "<h2>Test Sistem Produk Digital</h2>";

// Test 1: Cek direktori
echo "<h3>1. Test Direktori</h3>";
$directories = [
    'asset/files/ebook/',
    'asset/files/ebook/preview/',
    'application/views/administrator/additional/mod_produk_digital/',
    'application/views/phpmu-tigo/'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "✅ Direktori <strong>$dir</strong> sudah ada<br>";
    } else {
        echo "❌ Direktori <strong>$dir</strong> belum ada<br>";
    }
}

// Test 2: Cek file penting
echo "<h3>2. Test File Penting</h3>";
$files = [
    'application/controllers/Ebook.php',
    'application/controllers/Administrator.php',
    'application/views/phpmu-tigo/ebook_list.php',
    'application/views/phpmu-tigo/ebook_detail.php',
    'application/views/phpmu-tigo/ebook_preview.php',
    'DATABASE/digital_products.sql',
    'DIGITAL_PRODUCTS_README.md'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ File <strong>$file</strong> sudah ada<br>";
    } else {
        echo "❌ File <strong>$file</strong> belum ada<br>";
    }
}

// Test 3: Cek file keamanan
echo "<h3>3. Test File Keamanan</h3>";
$security_files = [
    'asset/files/ebook/.htaccess',
    'asset/files/ebook/index.php',
    'asset/files/ebook/preview/.htaccess',
    'asset/files/ebook/preview/index.php'
];

foreach ($security_files as $file) {
    if (file_exists($file)) {
        echo "✅ File keamanan <strong>$file</strong> sudah ada<br>";
    } else {
        echo "❌ File keamanan <strong>$file</strong> belum ada<br>";
    }
}

// Test 4: Cek permission direktori
echo "<h3>4. Test Permission Direktori</h3>";
$test_dir = 'asset/files/ebook/';
if (is_writable($test_dir)) {
    echo "✅ Direktori <strong>$test_dir</strong> bisa ditulis<br>";
} else {
    echo "❌ Direktori <strong>$test_dir</strong> tidak bisa ditulis (perlu set permission)<br>";
}

echo "<h3>5. Langkah Selanjutnya</h3>";
echo "1. Jalankan script SQL: <code>DATABASE/digital_products.sql</code><br>";
echo "2. Login ke admin panel dan cek menu 'Produk Digital'<br>";
echo "3. Tambah produk digital pertama<br>";
echo "4. Test pembelian dan akses ebook sebagai customer<br>";

echo "<h3>6. URL Penting</h3>";
echo "• Admin - Produk Digital: <code>/administrator/produk_digital</code><br>";
echo "• Admin - Akses Ebook: <code>/administrator/akses_ebook</code><br>";
echo "• Admin - Download Log: <code>/administrator/download_log</code><br>";
echo "• Customer - My Ebooks: <code>/ebook</code><br>";

echo "<hr>";
echo "<p><strong>Catatan:</strong> Pastikan database sudah diupdate dengan script SQL sebelum menggunakan sistem.</p>";
?> 