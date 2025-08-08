<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ebook extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_flash_sale');
    }

    public function index() {
        $data['title'] = 'Ebook & Produk Digital - '.title();
        $data['description'] = 'Koleksi ebook dan produk digital terlengkap';
        $data['keywords'] = 'ebook, produk digital, digital, download';
        
        // Pagination setup
        $this->load->library('pagination');
        $config['base_url'] = base_url().'ebook/index/';
        $config['total_rows'] = $this->model_flash_sale->count_digital_products();
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
        $data['ebook'] = $this->model_flash_sale->get_digital_products_paginated($config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();
        
        $this->template->load(template().'/template',template().'/ebook_page',$data);
    }
}
?>