<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test_rajaongkir extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('rajaongkir_new');
    }

    /**
     * Test koneksi dan response RajaOngkir
     */
    public function index() {
        echo "<h2>Test RajaOngkir API</h2>";
        
        // Test 1: Check config
        echo "<h3>1. Check Configuration</h3>";
        $this->load->config('rajaongkir', TRUE);
        $api_key = $this->config->item('rajaongkir_api_key', 'rajaongkir');
        $allowed_couriers = $this->config->item('rajaongkir_allowed_couriers', 'rajaongkir');
        
        echo "<p><strong>API Key:</strong> " . substr($api_key, 0, 20) . "...</p>";
        echo "<p><strong>Allowed Couriers:</strong> " . implode(', ', $allowed_couriers) . "</p>";
        
        // Test 2: Get Provinces
        echo "<h3>2. Test Get Provinces</h3>";
        $result = $this->rajaongkir_new->province();
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200 ? 'Yes' : 'No') . "</p>";
        if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200) {
            echo "<p style='color:green'>✓ Berhasil mendapatkan data provinsi</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Gagal mendapatkan data provinsi</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        // Test 3: Get Cities
        echo "<h3>3. Test Get Cities</h3>";
        $result = $this->rajaongkir_new->city(1); // Jakarta
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200 ? 'Yes' : 'No') . "</p>";
        if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200) {
            echo "<p style='color:green'>✓ Berhasil mendapatkan data kota</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Gagal mendapatkan data kota</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        // Test 4: Get Cost
        echo "<h3>4. Test Get Cost</h3>";
        $result = $this->rajaongkir_new->cost(1, 2, 500, 'jne'); // Jakarta ke Bandung, 500g, JNE
        $data = json_decode($result, true);
        
        echo "<p><strong>Success:</strong> " . (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200 ? 'Yes' : 'No') . "</p>";
        if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200) {
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
