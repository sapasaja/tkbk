<?php
/*
-- ---------------------------------------------------------------
-- MARKETPLACE MULTI BUYER MULTI SELLER + SUPPORT RESELLER SYSTEM
-- CREATED BY : ROBBY PRIHANDAYA
-- COPYRIGHT  : Copyright (c) 2018 - 2019, PHPMU.COM. (https://phpmu.com/)
-- LICENSE    : http://opensource.org/licenses/MIT  MIT License
-- CREATED ON : 2019-03-26
-- UPDATED ON : 2019-03-27
-- ---------------------------------------------------------------
*/
defined('BASEPATH') OR exit('No direct script access allowed');
class Main extends CI_Controller {
	public function index(){
		$data['title'] = title();
		$data['description'] = description();
		$data['keywords'] = keywords();
		$data['ik1'] = $this->model_app->view_where_ordering_limit('sponsoratas',array('username'=>'default'),'id_sponsoratas','ASC',0,1)->row_array();
		$data['ik2'] = $this->model_app->view_where_ordering_limit('sponsoratas',array('username'=>'default'),'id_sponsoratas','ASC',1,1)->row_array();
		$data['ik3'] = $this->model_app->view_where_ordering_limit('sponsoratas',array('username'=>'default'),'id_sponsoratas','ASC',2,1)->row_array();
		$data['ik4'] = $this->model_app->view_where_ordering_limit('sponsoratas',array('username'=>'default'),'id_sponsoratas','ASC',3,1)->row_array();
		$data['kategori'] = $this->db->query("SELECT * FROM (SELECT a.*,b.produk FROM (SELECT * FROM `rb_kategori_produk`) as a LEFT JOIN
										(SELECT id_kategori_produk, COUNT(*) produk FROM rb_produk GROUP BY id_kategori_produk HAVING COUNT(id_kategori_produk)) as b on a.id_kategori_produk=b.id_kategori_produk ORDER BY RAND()) as c WHERE produk>=6 ORDER BY c.id_kategori_produk DESC LIMIT 5");
		
		// Load model flash sale
		$this->load->model('model_flash_sale');
		
		// Data untuk homepage baru
		$data['flash_sale'] = $this->model_flash_sale->get_active_flash_sale(8);
		$data['buku_baru'] = $this->model_flash_sale->get_new_products(8);
		$data['buku_populer'] = $this->model_flash_sale->get_popular_products(8);
		$data['ebook'] = $this->model_flash_sale->get_digital_products(8);
		$data['buku_terlaris'] = $this->model_flash_sale->get_best_seller_products(8);
		

		
		$this->template->load(template().'/template',template().'/content',$data);
	}

	// Halaman Buku Baru
	public function buku_baru(){
		$data['title'] = 'Buku Baru - ' . title();
		$data['description'] = 'Koleksi buku terbaru yang baru saja ditambahkan ke toko kami';
		$data['keywords'] = 'buku baru, buku terbaru, koleksi buku';
		
		$this->load->model('model_flash_sale');
		$data['produk'] = $this->model_flash_sale->get_new_products(50);
		
		$this->template->load(template().'/template',template().'/kategori_khusus',$data);
	}

	// Halaman Buku Populer
	public function buku_populer(){
		$data['title'] = 'Buku Populer - ' . title();
		$data['description'] = 'Buku-buku populer yang banyak diminati pembaca';
		$data['keywords'] = 'buku populer, buku favorit, buku terbaik';
		
		$this->load->model('model_flash_sale');
		$data['produk'] = $this->model_flash_sale->get_popular_products(50);
		
		$this->template->load(template().'/template',template().'/kategori_khusus',$data);
	}

	// Halaman Ebook
	public function ebook(){
		$data['title'] = 'Ebook & Produk Digital - ' . title();
		$data['description'] = 'Koleksi ebook dan produk digital yang bisa didownload langsung';
		$data['keywords'] = 'ebook, buku digital, produk digital, download ebook';
		
		$this->load->model('model_flash_sale');
		$data['produk'] = $this->model_flash_sale->get_digital_products(50);
		
		$this->template->load(template().'/template',template().'/kategori_khusus',$data);
	}

	// Halaman Buku Terlaris
	public function buku_terlaris(){
		$data['title'] = 'Buku Terlaris - ' . title();
		$data['description'] = 'Buku-buku terlaris berdasarkan penjualan tertinggi';
		$data['keywords'] = 'buku terlaris, best seller, buku laris';
		
		$this->load->model('model_flash_sale');
		$data['produk'] = $this->model_flash_sale->get_best_seller_products(50);
		
		$this->template->load(template().'/template',template().'/kategori_khusus',$data);
	}
}
