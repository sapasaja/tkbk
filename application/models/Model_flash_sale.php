<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_flash_sale extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Ambil flash sale yang sedang aktif
     */
    public function get_active_flash_sale($limit = 8) {
        $now = date('Y-m-d H:i:s');
        $query = "SELECT fs.*, p.nama_produk, p.harga_konsumen, p.gambar, p.produk_seo, p.keterangan,
                                       pd.format_file, pd.ukuran_file, pd.nama_ebook
                                FROM rb_flash_sale fs 
                                JOIN rb_produk p ON fs.id_produk = p.id_produk 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                WHERE fs.status = 'aktif' 
                                AND fs.tanggal_mulai <= '$now' 
                                AND fs.tanggal_selesai >= '$now'
                                ORDER BY fs.created_at DESC 
                                LIMIT $limit";
        

        
        return $this->db->query($query);
    }
    
    /**
     * Cek apakah produk sedang dalam flash sale
     */
    public function is_product_in_flash_sale($id_produk) {
        $now = date('Y-m-d H:i:s');
        $result = $this->db->query("SELECT * FROM rb_flash_sale 
                                   WHERE id_produk = '$id_produk' 
                                   AND status = 'aktif' 
                                   AND tanggal_mulai <= '$now' 
                                   AND tanggal_selesai >= '$now'");
        return $result->num_rows() > 0 ? $result->row_array() : false;
    }
    
    /**
     * Ambil harga flash sale untuk produk tertentu
     */
    public function get_flash_sale_price($id_produk) {
        $flash_sale = $this->is_product_in_flash_sale($id_produk);
        return $flash_sale ? $flash_sale['harga_flash_sale'] : false;
    }
    
    /**
     * Ambil produk baru (7 hari terakhir)
     */
    public function get_new_products($limit = 8) {
        $date_7_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook
                                FROM rb_produk p 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                WHERE p.waktu_input >= '$date_7_days_ago'
                                ORDER BY p.waktu_input DESC 
                                LIMIT $limit");
    }
    
    /**
     * Ambil produk populer berdasarkan penjualan
     */
    public function get_popular_products($limit = 8) {
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook,
                                       COUNT(pd_penjualan.id_penjualan_detail) as total_sold
                                FROM rb_produk p 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                LEFT JOIN rb_penjualan_detail pd_penjualan ON p.id_produk = pd_penjualan.id_produk
                                GROUP BY p.id_produk
                                ORDER BY total_sold DESC 
                                LIMIT $limit");
    }
    
    /**
     * Ambil produk digital (ebook)
     */
    public function get_digital_products($limit = 8) {
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook
                                FROM rb_produk p 
                                JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                WHERE pd.status = 'aktif'
                                ORDER BY p.waktu_input DESC 
                                LIMIT $limit");
    }
    
    /**
     * Ambil produk terlaris berdasarkan penjualan
     */
    public function get_best_seller_products($limit = 8) {
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook,
                                       COALESCE(SUM(pd_penjualan.jumlah), 0) as total_quantity_sold
                                FROM rb_produk p 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                LEFT JOIN rb_penjualan_detail pd_penjualan ON p.id_produk = pd_penjualan.id_produk
                                GROUP BY p.id_produk
                                ORDER BY total_quantity_sold DESC 
                                LIMIT $limit");
    }
    
    // ===== PAGINATION FUNCTIONS =====
    
    public function count_active_flash_sale() {
        $now = date('Y-m-d H:i:s');
        $result = $this->db->query("SELECT COUNT(*) as count FROM rb_flash_sale fs 
                                   JOIN rb_produk p ON fs.id_produk = p.id_produk
                                   WHERE fs.status = 'aktif' 
                                   AND fs.tanggal_mulai <= '$now' 
                                   AND fs.tanggal_selesai >= '$now'");
        return $result->row()->count;
    }
    
    public function get_active_flash_sale_paginated($limit, $offset) {
        $now = date('Y-m-d H:i:s');
        return $this->db->query("SELECT fs.*, p.nama_produk, p.harga_konsumen, p.gambar, p.produk_seo, p.keterangan,
                                       pd.format_file, pd.ukuran_file, pd.nama_ebook
                                FROM rb_flash_sale fs 
                                JOIN rb_produk p ON fs.id_produk = p.id_produk 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                WHERE fs.status = 'aktif' 
                                AND fs.tanggal_mulai <= '$now' 
                                AND fs.tanggal_selesai >= '$now'
                                ORDER BY fs.created_at DESC 
                                LIMIT $limit OFFSET $offset");
    }
    
    public function count_new_products() {
        $date_7_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        $result = $this->db->query("SELECT COUNT(*) as count FROM rb_produk p 
                                   WHERE p.waktu_input >= '$date_7_days_ago'");
        return $result->row()->count;
    }
    
    public function get_new_products_paginated($limit, $offset) {
        $date_7_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook
                                FROM rb_produk p 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                WHERE p.waktu_input >= '$date_7_days_ago'
                                ORDER BY p.waktu_input DESC 
                                LIMIT $limit OFFSET $offset");
    }
    
    public function count_popular_products() {
        $result = $this->db->query("SELECT COUNT(DISTINCT p.id_produk) as count FROM rb_produk p 
                                   LEFT JOIN rb_penjualan_detail pd_penjualan ON p.id_produk = pd_penjualan.id_produk");
        return $result->row()->count;
    }
    
    public function get_popular_products_paginated($limit, $offset) {
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook,
                                       COUNT(pd_penjualan.id_penjualan_detail) as total_sold
                                FROM rb_produk p 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                LEFT JOIN rb_penjualan_detail pd_penjualan ON p.id_produk = pd_penjualan.id_produk
                                GROUP BY p.id_produk
                                ORDER BY total_sold DESC 
                                LIMIT $limit OFFSET $offset");
    }
    
    public function count_digital_products() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM rb_produk p 
                                   JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                   WHERE pd.status = 'aktif'");
        return $result->row()->count;
    }
    
    public function get_digital_products_paginated($limit, $offset) {
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook
                                FROM rb_produk p 
                                JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                WHERE pd.status = 'aktif'
                                ORDER BY p.waktu_input DESC 
                                LIMIT $limit OFFSET $offset");
    }
    
    public function count_best_seller_products() {
        $result = $this->db->query("SELECT COUNT(DISTINCT p.id_produk) as count FROM rb_produk p 
                                   LEFT JOIN rb_penjualan_detail pd_penjualan ON p.id_produk = pd_penjualan.id_produk");
        return $result->row()->count;
    }
    
    public function get_best_seller_products_paginated($limit, $offset) {
        return $this->db->query("SELECT p.*, pd.format_file, pd.ukuran_file, pd.nama_ebook,
                                       COALESCE(SUM(pd_penjualan.jumlah), 0) as total_quantity_sold
                                FROM rb_produk p 
                                LEFT JOIN rb_produk_digital pd ON p.id_produk = pd.id_produk
                                LEFT JOIN rb_penjualan_detail pd_penjualan ON p.id_produk = pd_penjualan.id_produk
                                GROUP BY p.id_produk
                                ORDER BY total_quantity_sold DESC 
                                LIMIT $limit OFFSET $offset");
    }
} 