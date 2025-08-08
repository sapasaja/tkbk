<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_terlaris extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_flash_sale');
    }

    public function index() {
        $data['title'] = 'Buku Terlaris - '.title();
        $data['description'] = 'Buku-buku terlaris dan best seller sepanjang masa';
        $data['keywords'] = 'buku terlaris, best seller, terpopuler, laris';
        
        // Pagination setup
        $this->load->library('pagination');
        $config['base_url'] = base_url().'buku_terlaris/index/';
        $config['total_rows'] = $this->model_flash_sale->count_best_seller_products();
        $config['per_page'] = 16;
        $config['uri_segment'] = 3;
        
        // Pagination styling
        $config['full_tag_open'] = '<nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['buku_terlaris'] = $this->model_flash_sale->get_best_seller_products_paginated($config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();
        
        $this->template->load(template().'/template',template().'/buku_terlaris_page',$data);
    }
}
?>