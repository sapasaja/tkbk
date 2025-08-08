<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RajaOngkir API Key
 * Silakan daftar akun di RajaOngkir.com untuk mendapatkan API Key
 * http://rajaongkir.com/akun/daftar
 */
$config['rajaongkir_api_key'] = "DnXSRbSfd35be650a8bec344jDWPH89j";

/**
 * RajaOngkir account type: starter or basic
 * http://rajaongkir.com/dokumentasi#akun-ringkasan
 * 
 */
$config['rajaongkir_account_type'] = "starter";

/**
 * Kurir yang diizinkan untuk ditampilkan
 * Kosongkan array untuk menampilkan semua kurir
 * Contoh: array('jne', 'pos', 'tiki') untuk hanya menampilkan JNE, POS, dan TIKI
 */
$config['rajaongkir_allowed_couriers'] = array('jne', 'jnt');
