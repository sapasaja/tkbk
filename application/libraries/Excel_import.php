<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_import {
    
    public function __construct() {
        // Load helper yang diperlukan
        $CI =& get_instance();
        $CI->load->helper('file');
    }
    
    /**
     * Baca file Excel dan konversi ke array
     * @param string $file_path Path ke file Excel
     * @return array Data dari Excel
     */
    public function read_excel($file_path) {
        if (!file_exists($file_path)) {
            return array('error' => 'File tidak ditemukan');
        }
        
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        if ($file_extension == 'csv') {
            return $this->read_csv($file_path);
        } elseif (in_array($file_extension, array('xls', 'xlsx'))) {
            return $this->read_xlsx($file_path);
        } else {
            return array('error' => 'Format file tidak didukung. Gunakan CSV, XLS, atau XLSX');
        }
    }
    
    /**
     * Baca file CSV
     */
    private function read_csv($file_path) {
        $data = array();
        $handle = fopen($file_path, 'r');
        
        if ($handle !== FALSE) {
            $row = 0;
            while (($row_data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if ($row == 0) {
                    // Header row
                    $headers = $row_data;
                } else {
                    // Data row
                    $row_array = array();
                    foreach ($headers as $index => $header) {
                        $row_array[trim($header)] = isset($row_data[$index]) ? trim($row_data[$index]) : '';
                    }
                    $data[] = $row_array;
                }
                $row++;
            }
            fclose($handle);
        }
        
        return $data;
    }
    
    /**
     * Baca file XLSX (menggunakan PHP built-in functions)
     */
    private function read_xlsx($file_path) {
        // Untuk XLSX, kita akan menggunakan CSV sebagai fallback
        // Karena PHPExcel library tidak tersedia di project ini
        return array('error' => 'Format XLSX memerlukan library tambahan. Gunakan format CSV untuk saat ini.');
    }
    
    /**
     * Validasi data produk
     */
    public function validate_product_data($data, $type = 'physical') {
        $errors = array();
        $valid_data = array();
        
        foreach ($data as $row_index => $row) {
            $row_errors = array();
            
            // Validasi field wajib
            if (empty($row['nama_produk'])) {
                $row_errors[] = 'Nama produk wajib diisi';
            }
            
            if (empty($row['kategori'])) {
                $row_errors[] = 'Kategori wajib diisi';
            }
            
            if (empty($row['harga_konsumen'])) {
                $row_errors[] = 'Harga konsumen wajib diisi';
            }
            
            if (!is_numeric($row['harga_konsumen'])) {
                $row_errors[] = 'Harga konsumen harus berupa angka';
            }
            
            // Validasi khusus produk digital
            if ($type == 'digital') {
                if (empty($row['format_file'])) {
                    $row_errors[] = 'Format file wajib diisi (PDF, DOC, DOCX, EPUB, MOBI)';
                }
                
                $allowed_formats = array('PDF', 'DOC', 'DOCX', 'EPUB', 'MOBI');
                if (!in_array(strtoupper($row['format_file']), $allowed_formats)) {
                    $row_errors[] = 'Format file tidak valid. Gunakan: ' . implode(', ', $allowed_formats);
                }
            }
            
            if (empty($row_errors)) {
                $valid_data[] = $row;
            } else {
                $errors[$row_index + 2] = $row_errors; // +2 karena Excel dimulai dari baris 2 (header di baris 1)
            }
        }
        
        return array(
            'valid_data' => $valid_data,
            'errors' => $errors
        );
    }
    
    /**
     * Generate template Excel untuk produk fisik
     */
    public function generate_physical_template() {
        $headers = array(
            'nama_produk',
            'kategori',
            'sub_kategori',
            'satuan',
            'harga_beli',
            'harga_reseller',
            'harga_konsumen',
            'keterangan'
        );
        
        $sample_data = array(
            'Buku Matematika SD Kelas 1',
            'Buku Pelajaran',
            'SD',
            'unit',
            '15000',
            '20000',
            '25000',
            'Buku matematika untuk siswa SD kelas 1'
        );
        
        return array(
            'headers' => $headers,
            'sample_data' => $sample_data
        );
    }
    
    /**
     * Generate template Excel untuk produk digital
     */
    public function generate_digital_template() {
        $headers = array(
            'nama_produk',
            'kategori',
            'sub_kategori',
            'satuan',
            'harga_beli',
            'harga_reseller',
            'harga_konsumen',
            'keterangan',
            'format_file',
            'nama_ebook'
        );
        
        $sample_data = array(
            'Ebook Matematika SD Kelas 1',
            'Ebook',
            'SD',
            'unit',
            '10000',
            '15000',
            '20000',
            'Ebook matematika untuk siswa SD kelas 1',
            'PDF',
            'Ebook Matematika SD Kelas 1'
        );
        
        return array(
            'headers' => $headers,
            'sample_data' => $sample_data
        );
    }
} 