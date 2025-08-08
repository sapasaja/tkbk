<?php

/**
 * RajaOngkir CodeIgniter Library (Updated)
 * Digunakan untuk mengkonsumsi API RajaOngkir dengan mudah
 * 
 * @author Updated for new API
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class RajaOngkir_new {

    private $ci;
    private $api_key;
    private $api_url;

    public function __construct() {
        // Pastikan bahwa PHP mendukung cURL
        if (!function_exists('curl_init')) {
            log_message('error', 'RajaOngkir Class - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
        }
        $this->_ci = & get_instance();
        $this->_ci->load->config('rajaongkir', TRUE);
        
        // Pastikan Anda sudah memasukkan API Key di application/config/rajaongkir.php
        if ($this->_ci->config->item('rajaongkir_api_key', 'rajaongkir') == "") {
            log_message("error", "Harap masukkan API KEY Anda di config.");
        } else {
            $this->api_key = $this->_ci->config->item('rajaongkir_api_key', 'rajaongkir');
            $this->api_url = "https://api.rajaongkir.com/starter/";
        }
    }

    /**
     * Fungsi untuk mendapatkan data propinsi di Indonesia
     * @param integer $province_id ID propinsi, jika NULL tampilkan semua propinsi
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function province($province_id = NULL) {
        $params = (is_null($province_id)) ? array() : array('id' => $province_id);
        return $this->makeRequest('province', 'GET', $params);
    }

    /**
     * Fungsi untuk mendapatkan data kota di Indonesia
     * @param integer $province_id ID propinsi
     * @param integer $city_id ID kota, jika ID propinsi dan kota NULL maka tampilkan semua kota
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function city($province_id = NULL, $city_id = NULL) {
        $params = (is_null($province_id)) ? array() : array('province' => $province_id);
        if (!is_null($city_id)) {
            $params['id'] = $city_id;
        }
        return $this->makeRequest('city', 'GET', $params);
    }

    /**
     * Fungsi untuk mendapatkan data ongkos kirim
     * @param integer $origin ID kota asal
     * @param integer $destination ID kota tujuan
     * @param integer $weight Berat kiriman dalam gram
     * @param string $courier Kode kurir
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function cost($origin, $destination, $weight, $courier) {
        $params = array(
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        );
        return $this->makeRequest('cost', 'POST', $params);
    }

    /**
     * Fungsi untuk melacak paket/nomor resi
     * @param string $waybill_number Nomor resi
     * @param string $courier Kode kurir
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function waybill($waybill_number, $courier) {
        $params = array(
            'waybill' => $waybill_number,
            'courier' => $courier
        );
        return $this->makeRequest('waybill', 'POST', $params);
    }

    /**
     * Fungsi untuk membuat request ke API RajaOngkir
     * @param string $endpoint Endpoint API
     * @param string $method HTTP Method (GET, POST)
     * @param array $params Parameter yang dikirim
     * @return string Response dari API
     */
    private function makeRequest($endpoint, $method = 'GET', $params = array()) {
        $curl = curl_init();
        
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'key: ' . $this->api_key
        );

        $url = $this->api_url . $endpoint;
        
        if ($method == 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        if ($method == 'POST' && !empty($params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        if ($err) {
            log_message('error', 'RajaOngkir API Error: ' . $err);
            return json_encode(array('success' => false, 'error' => $err));
        }

        return $response;
    }
}
