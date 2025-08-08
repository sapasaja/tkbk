<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_app');
        
        // Cek apakah user sudah login
        if (!$this->session->userdata('username')) {
            redirect('administrator/auth');
        }
    }

    /**
     * Halaman utama pengaturan kurir
     */
    public function index() {
        $data['title'] = 'Pengaturan Kurir Pengiriman';
        $data['description'] = 'Atur kurir yang tersedia untuk pengiriman';
        $data['keywords'] = 'kurir, pengiriman, shipping, rajaongkir';
        
        // Load konfigurasi RajaOngkir
        $this->load->config('rajaongkir', TRUE);
        $data['allowed_couriers'] = $this->config->item('rajaongkir_allowed_couriers', 'rajaongkir');
        
        // Daftar kurir yang tersedia
        $data['available_couriers'] = array(
            'jne' => 'JNE Express',
            'pos' => 'POS Indonesia',
            'tiki' => 'TIKI',
            'wahana' => 'Wahana',
            'sicepat' => 'SiCepat',
            'jnt' => 'J&T Express',
            'lion' => 'Lion Parcel',
            'ninja' => 'Ninja Express',
            'first' => 'First Logistics',
            'anteraja' => 'AnterAja'
        );
        
        $this->template->load('administrator/template', 'administrator/mod_shipping/view_settings', $data);
    }

    /**
     * Update pengaturan kurir
     */
    public function update_couriers() {
        $selected_couriers = $this->input->post('couriers');
        
        if ($selected_couriers === null) {
            $selected_couriers = array(); // Kosongkan jika tidak ada yang dipilih
        }
        
        // Baca file config
        $config_file = APPPATH . 'config/rajaongkir.php';
        $config_content = file_get_contents($config_file);
        
        // Update allowed couriers
        $couriers_string = "array('" . implode("', '", $selected_couriers) . "')";
        if (empty($selected_couriers)) {
            $couriers_string = "array()";
        }
        
        $config_content = preg_replace(
            '/\$config\[\'rajaongkir_allowed_couriers\'\]\s*=\s*array\([^)]*\);/',
            '$config[\'rajaongkir_allowed_couriers\'] = ' . $couriers_string . ';',
            $config_content
        );
        
        // Tulis kembali ke file
        if (file_put_contents($config_file, $config_content)) {
            $this->session->set_flashdata('message', 'Pengaturan kurir berhasil diperbarui!');
            $this->session->set_flashdata('type', 'success');
        } else {
            $this->session->set_flashdata('message', 'Gagal memperbarui pengaturan kurir!');
            $this->session->set_flashdata('type', 'danger');
        }
        
        redirect('shipping_settings');
    }

    /**
     * Test koneksi RajaOngkir
     */
    public function test_connection() {
        $this->load->library('rajaongkir_v2');
        
        // Test dengan mengambil data provinsi
        $result = $this->rajaongkir_v2->province();
        $data = json_decode($result, true);
        
        if (isset($data['success']) && $data['success'] == true) {
            $this->session->set_flashdata('message', 'Koneksi RajaOngkir V2 berhasil!');
            $this->session->set_flashdata('type', 'success');
        } else {
            $this->session->set_flashdata('message', 'Koneksi RajaOngkir V2 gagal: ' . json_encode($data));
            $this->session->set_flashdata('type', 'danger');
        }
        
        redirect('shipping_settings');
    }
}
