<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Biteship extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_app');
        $this->load->library('biteship');
        
        // Cek apakah user sudah login
        if (!$this->session->userdata('username')) {
            redirect('administrator/auth');
        }
    }

    /**
     * Halaman utama pengaturan Biteship
     */
    public function index() {
        $data['title'] = 'Pengaturan Biteship API';
        $data['description'] = 'Konfigurasi sistem pengiriman Biteship';
        $data['keywords'] = 'biteship, api, pengiriman, shipping';
        
        // Load konfigurasi Biteship
        $this->load->config('biteship', TRUE);
        $data['config'] = array(
            'biteship_api_key' => $this->config->item('biteship_api_key', 'biteship'),
            'biteship_environment' => $this->config->item('biteship_environment', 'biteship'),
            'biteship_api_url' => $this->config->item('biteship_api_url', 'biteship')
        );
        
        $this->template->load('administrator/template', 'administrator/mod_biteship/view_biteship', $data);
    }

    /**
     * Update konfigurasi Biteship
     */
    public function update_config() {
        $api_key = $this->input->post('api_key');
        $environment = $this->input->post('environment');
        $api_url = $this->input->post('api_url');
        
        if (empty($api_key) || empty($api_url)) {
            $this->session->set_flashdata('message', 'Semua field harus diisi!');
            $this->session->set_flashdata('type', 'danger');
            redirect('administrator/biteship');
            return;
        }
        
        // Baca file config
        $config_file = APPPATH . 'config/biteship.php';
        $config_content = file_get_contents($config_file);
        
        // Update API Key
        $config_content = preg_replace(
            '/\$config\[\'biteship_api_key\'\]\s*=\s*"[^"]*";/',
            '$config[\'biteship_api_key\'] = "' . $api_key . '";',
            $config_content
        );
        
        // Update Environment
        $config_content = preg_replace(
            '/\$config\[\'biteship_environment\'\]\s*=\s*"[^"]*";/',
            '$config[\'biteship_environment\'] = "' . $environment . '";',
            $config_content
        );
        
        // Update API URL
        $config_content = preg_replace(
            '/\$config\[\'biteship_api_url\'\]\s*=\s*"[^"]*";/',
            '$config[\'biteship_api_url\'] = "' . $api_url . '";',
            $config_content
        );
        
        // Tulis kembali ke file
        if (file_put_contents($config_file, $config_content)) {
            $this->session->set_flashdata('message', 'Konfigurasi berhasil diperbarui!');
            $this->session->set_flashdata('type', 'success');
        } else {
            $this->session->set_flashdata('message', 'Gagal memperbarui konfigurasi!');
            $this->session->set_flashdata('type', 'danger');
        }
        
        redirect('administrator/biteship');
    }

    /**
     * Test koneksi API
     */
    public function test_connection() {
        $result = $this->biteship->getCouriers();
        
        if ($result['success']) {
            $this->session->set_flashdata('message', 'Koneksi API Biteship berhasil!');
            $this->session->set_flashdata('type', 'success');
        } else {
            $this->session->set_flashdata('message', 'Koneksi API Biteship gagal: ' . json_encode($result['error']));
            $this->session->set_flashdata('type', 'danger');
        }
        
        redirect('administrator/biteship');
    }

    /**
     * Halaman untuk melihat log pengiriman
     */
    public function shipping_logs() {
        $data['title'] = 'Log Pengiriman Biteship';
        $data['description'] = 'Riwayat pengiriman menggunakan Biteship';
        $data['keywords'] = 'biteship, log, pengiriman, shipping';
        
        // Ambil data log pengiriman dari database
        $data['logs'] = $this->db->query("
            SELECT p.*, k.nama_lengkap, k.no_hp, k.alamat_lengkap, 
                   kota.nama_kota, prov.nama_provinsi
            FROM rb_penjualan p 
            JOIN rb_konsumen k ON p.id_pembeli = k.id_konsumen
            JOIN rb_kota kota ON k.kota_id = kota.kota_id
            JOIN rb_provinsi prov ON kota.provinsi_id = prov.provinsi_id
            WHERE p.kurir IS NOT NULL 
            ORDER BY p.waktu_transaksi DESC
            LIMIT 50
        ")->result_array();
        
        $this->template->load('administrator/template', 'administrator/mod_biteship/view_shipping_logs', $data);
    }

    /**
     * Halaman untuk tracking manual
     */
    public function manual_tracking() {
        $data['title'] = 'Manual Tracking Biteship';
        $data['description'] = 'Tracking pengiriman manual';
        $data['keywords'] = 'biteship, tracking, manual';
        
        if ($this->input->post('submit')) {
            $waybill = $this->input->post('waybill');
            $courier = $this->input->post('courier');
            
            if (!empty($waybill) && !empty($courier)) {
                $result = $this->biteship->trackWaybill($waybill, $courier);
                $data['tracking_result'] = $result;
                $data['waybill'] = $waybill;
                $data['courier'] = $courier;
            }
        }
        
        $this->template->load('administrator/template', 'administrator/mod_biteship/view_manual_tracking', $data);
    }

    /**
     * Halaman untuk melihat daftar kurir
     */
    public function couriers() {
        $data['title'] = 'Daftar Kurir Biteship';
        $data['description'] = 'Informasi kurir yang didukung';
        $data['keywords'] = 'biteship, kurir, courier';
        
        $result = $this->biteship->getCouriers();
        $data['couriers'] = $result['success'] ? $result['data'] : array();
        
        $this->template->load('administrator/template', 'administrator/mod_biteship/view_couriers', $data);
    }

    /**
     * Halaman untuk melihat daftar area
     */
    public function areas() {
        $data['title'] = 'Daftar Area Biteship';
        $data['description'] = 'Informasi area/provinsi yang didukung';
        $data['keywords'] = 'biteship, area, provinsi';
        
        $result = $this->biteship->getAreas();
        $data['areas'] = $result['success'] ? $result['data'] : array();
        
        $this->template->load('administrator/template', 'administrator/mod_biteship/view_areas', $data);
    }
}
