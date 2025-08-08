<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test_rajaongkir_v2 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('rajaongkir_v2');
    }

    /**
     * Test koneksi dan response RajaOngkir V2
     */
    public function index() {
        echo "<h2>Test RajaOngkir V2 API</h2>";
        
        // Test 1: Check config
        echo "<h3>1. Check Configuration</h3>";
        $this->load->config('rajaongkir', TRUE);
        $api_key = $this->config->item('rajaongkir_api_key', 'rajaongkir');
        $allowed_couriers = $this->config->item('rajaongkir_allowed_couriers', 'rajaongkir');
        
        echo "<p><strong>API Key:</strong> " . substr($api_key, 0, 20) . "...</p>";
        echo "<p><strong>Allowed Couriers:</strong> " . implode(', ', $allowed_couriers) . "</p>";
        
        // Test 2: Get Provinces
        echo "<h3>2. Test Get Provinces</h3>";
        $result = $this->rajaongkir_v2->province();
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['success']) && $data['success'] == true ? 'Yes' : 'No') . "</p>";
        if (isset($data['success']) && $data['success'] == true) {
            echo "<p style='color:green'>✓ Berhasil mendapatkan data provinsi</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Gagal mendapatkan data provinsi</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        // Test 3: Get Cities
        echo "<h3>3. Test Get Cities</h3>";
        $result = $this->rajaongkir_v2->city(1); // Jakarta
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['success']) && $data['success'] == true ? 'Yes' : 'No') . "</p>";
        if (isset($data['success']) && $data['success'] == true) {
            echo "<p style='color:green'>✓ Berhasil mendapatkan data kota</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Gagal mendapatkan data kota</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        // Test 4: Search Domestic Destination
        echo "<h3>4. Test Search Domestic Destination</h3>";
        $result = $this->rajaongkir_v2->searchDomesticDestination('jakarta', 5, 0);
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['success']) && $data['success'] == true ? 'Yes' : 'No') . "</p>";
        if (isset($data['success']) && $data['success'] == true) {
            echo "<p style='color:green'>✓ Berhasil mencari destinasi domestik</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Gagal mencari destinasi domestik</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        // Test 5: Get Cost (menggunakan subdistrict ID)
        echo "<h3>5. Test Get Cost</h3>";
        // Contoh: Jakarta ke Bandung, 500g, JNE
        // Note: Perlu subdistrict ID yang valid dari hasil search
        $result = $this->rajaongkir_v2->cost(1, 2, 500, 'jne');
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['success']) && $data['success'] == true ? 'Yes' : 'No') . "</p>";
        if (isset($data['success']) && $data['success'] == true) {
            echo "<p style='color:green'>✓ Berhasil mendapatkan data ongkos kirim</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Gagal mendapatkan data ongkos kirim</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . base_url() . "'>Kembali ke Home</a></p>";
    }
}
