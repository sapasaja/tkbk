<?php

/**
 * RajaOngkir CodeIgniter Library (V2 API)
 * Digunakan untuk mengkonsumsi API RajaOngkir V2 dengan mudah
 * 
 * @author Updated for RajaOngkir V2 API
 * @link https://komerceapi.readme.io/reference/rajaongkir-api
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class RajaOngkir_v2 {

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
            $this->api_url = "https://rajaongkir.komerce.id/api/v1/";
        }
    }

    /**
     * Fungsi untuk mendapatkan data propinsi di Indonesia (Step-by-Step Method)
     * @param integer $province_id ID propinsi, jika NULL tampilkan semua propinsi
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function province($province_id = NULL) {
        $params = (is_null($province_id)) ? array() : array('id' => $province_id);
        return $this->makeRequest('destination/province', 'GET', $params);
    }

    /**
     * Fungsi untuk mendapatkan data kota di Indonesia (Step-by-Step Method)
     * @param integer $province_id ID propinsi
     * @param integer $city_id ID kota, jika ID propinsi dan kota NULL maka tampilkan semua kota
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function city($province_id = NULL, $city_id = NULL) {
        $params = (is_null($province_id)) ? array() : array('province_id' => $province_id);
        if (!is_null($city_id)) {
            $params['id'] = $city_id;
        }
        return $this->makeRequest('destination/city', 'GET', $params);
    }

    /**
     * Fungsi untuk mendapatkan data district (Step-by-Step Method)
     * @param integer $city_id ID kota
     * @param integer $district_id ID district, jika NULL tampilkan semua district
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function district($city_id = NULL, $district_id = NULL) {
        $params = (is_null($city_id)) ? array() : array('city_id' => $city_id);
        if (!is_null($district_id)) {
            $params['id'] = $district_id;
        }
        return $this->makeRequest('destination/district', 'GET', $params);
    }

    /**
     * Fungsi untuk mendapatkan data subdistrict (Step-by-Step Method)
     * @param integer $district_id ID district
     * @param integer $subdistrict_id ID subdistrict, jika NULL tampilkan semua subdistrict
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function subdistrict($district_id = NULL, $subdistrict_id = NULL) {
        $params = (is_null($district_id)) ? array() : array('district_id' => $district_id);
        if (!is_null($subdistrict_id)) {
            $params['id'] = $subdistrict_id;
        }
        return $this->makeRequest('destination/subdistrict', 'GET', $params);
    }

    /**
     * Fungsi untuk mencari destinasi domestik (Direct Search Method)
     * @param string $search Kata kunci pencarian (nama kota)
     * @param integer $limit Limit hasil pencarian
     * @param integer $offset Offset untuk pagination
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function searchDomesticDestination($search, $limit = 10, $offset = 0) {
        $params = array(
            'search' => $search,
            'limit' => $limit,
            'offset' => $offset
        );
        return $this->makeRequest('destination/domestic-destination', 'GET', $params);
    }

    /**
     * Fungsi untuk mendapatkan data ongkos kirim (Step-by-Step Method - District)
     * @param integer $origin ID district asal
     * @param integer $destination ID district tujuan
     * @param integer $weight Berat kiriman dalam gram
     * @param string $courier Kode kurir
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function costByDistrict($origin, $destination, $weight, $courier) {
        $params = array(
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        );
        return $this->makeRequest('destination/district/calculate-cost', 'POST', $params);
    }

    /**
     * Fungsi untuk mendapatkan data ongkos kirim (Step-by-Step Method - Subdistrict)
     * @param integer $origin ID subdistrict asal
     * @param integer $destination ID subdistrict tujuan
     * @param integer $weight Berat kiriman dalam gram
     * @param string $courier Kode kurir
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function costBySubdistrict($origin, $destination, $weight, $courier) {
        $params = array(
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        );
        return $this->makeRequest('destination/subdistrict/calculate-cost', 'POST', $params);
    }

    /**
     * Fungsi untuk mendapatkan data ongkos kirim (Direct Search Method)
     * @param integer $origin ID subdistrict asal
     * @param integer $destination ID subdistrict tujuan
     * @param integer $weight Berat kiriman dalam gram
     * @param string $courier Kode kurir
     * @return string Response dari cURL, berupa string JSON balasan dari RajaOngkir
     */
    function cost($origin, $destination, $weight, $courier) {
        // Default menggunakan subdistrict untuk kompatibilitas
        return $this->costBySubdistrict($origin, $destination, $weight, $courier);
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
        return $this->makeRequest('tracking/airwaybills', 'POST', $params);
    }

    /**
     * Fungsi untuk membuat request ke API RajaOngkir V2
     * @param string $endpoint Endpoint API
     * @param string $method HTTP Method (GET, POST)
     * @param array $params Parameter yang dikirim
     * @return string Response dari API
     */
    private function makeRequest($endpoint, $method = 'GET', $params = array()) {
        $curl = curl_init();
        
        $headers = array(
            'Content-Type: application/json',
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
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);

        if ($err) {
            log_message('error', 'RajaOngkir V2 API Error: ' . $err);
            return json_encode(array('success' => false, 'error' => $err));
        }

        // Log response untuk debugging
        log_message('debug', 'RajaOngkir V2 API Response: ' . $response);
        log_message('debug', 'RajaOngkir V2 API HTTP Code: ' . $http_code);

        return $response;
    }
}
