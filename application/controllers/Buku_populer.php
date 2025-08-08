<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_populer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_flash_sale');
    }

    public function index() {
        $data['title'] = 'Buku Populer - '.title();
        $data['description'] = 'Buku-buku paling populer dan best seller';
        $data['keywords'] = 'buku populer, best seller, terpopuler';
        
        // Pagination setup
        $this->load->library('pagination');
        $config['base_url'] = base_url().'buku_populer/index/';
        $config['total_rows'] = $this->model_flash_sale->count_popular_products();
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
        $data['buku_populer'] = $this->model_flash_sale->get_popular_products_paginated($config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();
        
        $this->template->load(template().'/template',template().'/buku_populer_page',$data);
    }
}
?>