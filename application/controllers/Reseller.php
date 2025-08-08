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
class Reseller extends CI_Controller {
	function index(){
		if (isset($_POST['submit'])){
			$username = $this->input->post('a');
			$password = hash("sha512", md5($this->input->post('b')));
			$cek = $this->db->query("SELECT * FROM rb_reseller where username='".$this->db->escape_str($username)."' AND password='".$this->db->escape_str($password)."'");
		    $row = $cek->row_array();
		    $total = $cek->num_rows();
			if ($total > 0){
				$this->session->set_userdata(array('id_reseller'=>$row['id_reseller'],
								   'username'=>$row['username'],
								   'level'=>'reseller'));
				redirect('reseller/home');
			}else{
				$data['title'] = 'Pelapak &rsaquo; Log In';
				$this->load->view('reseller/view_login',$data);
			}
		}else{
			$data['title'] = 'Pelapak &rsaquo; Log In';
			$this->load->view('reseller/view_login',$data);
		}
	}

	function ref(){
		if (isset($_POST['submit2'])){
			$cek  = $this->model_app->view_where('rb_reseller',array('username'=>$this->input->post('a')))->num_rows();	
			if ($cek >= 1){
				$username = $this->input->post('a');
				echo "<script>window.alert('Maaf, Username $username sudah dipakai oleh orang lain!');
                                  window.location=('".base_url()."/".$this->input->post('i')."')</script>";
			}else{
				$route = array('administrator','agenda','auth','berita','contact','download','gallery','konfirmasi','main','members','page','produk','reseller','testimoni','video');
				if (in_array($this->input->post('a'), $route)){
					$username = $this->input->post('a');
					echo "<script>window.alert('Maaf, Username $username sudah dipakai oleh orang lain!');
	                                  window.location=('".base_url()."/".$this->input->post('i')."')</script>";
				}else{
				$data = array('username'=>$this->input->post('a'),
		        			  'password'=>hash("sha512", md5($this->input->post('b'))),
		        			  'nama_reseller'=>$this->input->post('c'),
		        			  'jenis_kelamin'=>$this->input->post('d'),
		        			  'alamat_lengkap'=>$this->input->post('e'),
		        			  'no_telpon'=>$this->input->post('f'),
							  'email'=>$this->input->post('g'),
							  'kode_pos'=>$this->input->post('h'),
							  'referral'=>$this->input->post('i'),
							  'tanggal_daftar'=>date('Y-m-d H:i:s'));
				$this->model_app->insert('rb_reseller',$data);
				$id = $this->db->insert_id();
				$this->session->set_userdata(array('id_reseller'=>$id, 'level'=>'reseller'));
				$identitas = $this->model_app->view_where('identitas',array('id_identitas'=>'1'))->row_array();

				$ref = $this->model_app->view_where('rb_reseller',array('username'=>$this->input->post('i')))->row_array();
				$email_tujuan = $ref['email'];
				$tglaktif = date("d-m-Y H:i:s");
				$subject      = 'Pendaftaran Sebagai Reseller Berhasil...';

				$message      = "<html><body>Selamat, Pada Hari ini tanggal $tglaktif<br> Bpk/Ibk <b>".$this->input->post('c')."</b> Sukses Mendafatar Sebagai reseller dengan referral <b>".$ref['nama_reseller']."</b>...";
				$message      .= "<table style='width:100%; margin-left:25px'>
		   				<tr><td style='background:#337ab7; color:#fff; pading:20px' cellpadding=6 colspan='2'><b>Berikut Informasi akun : </b></td></tr>
						<tr><td><b>Nama Reseller</b></td>			<td> : ".$this->input->post('c')."</td></tr>
						<tr><td><b>Alamat Email</b></td>			<td> : ".$this->input->post('g')."</td></tr>
						<tr><td><b>No Telpon</b></td>				<td> : ".$this->input->post('f')."</td></tr>
						<tr><td><b>Jenis Kelamin</b></td>			<td> : ".$this->input->post('d')." </td></tr>
						<tr><td><b>Alamat Lengkap</b></td>			<td> : ".$this->input->post('e')." </td></tr>
						<tr><td><b>Kode Pos</b></td>				<td> : ".$this->input->post('h')." </td></tr>
						<tr><td><b>Waktu Daftar</b></td>			<td> : ".date('Y-m-d H:i:s')."</td></tr>
					</table><br>

					Admin, $identitas[nama_website] </body></html> \n";
				
				$this->email->from($identitas['email'], $identitas['nama_website']);
				$this->email->to($email_tujuan,$this->input->post('g'));
				$this->email->cc('');
				$this->email->bcc('');

				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->set_mailtype("html");
				$this->email->send();
				
				$config['protocol'] = 'sendmail';
				$config['mailpath'] = '/usr/sbin/sendmail';
				$config['charset'] = 'utf-8';
				$config['wordwrap'] = TRUE;
				$config['mailtype'] = 'html';
				$this->email->initialize($config);

				redirect($this->uri->segment(1).'/home');
				}
			}
		}
	}

	function home(){
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/view_home');
	}


	function edit_reseller(){
		cek_session_reseller();
		$id = $this->session->id_reseller;
		if (isset($_POST['submit'])){
			$config['upload_path'] = 'asset/foto_user/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '5000'; // kb
            $this->load->library('upload', $config);
            $this->upload->do_upload('gg');
            $hasil=$this->upload->data();
            if ($hasil['file_name']==''){
				if (trim($this->input->post('b')) != ''){
		            $data = array('password'=>hash("sha512", md5($this->input->post('b'))),
		                        'nama_reseller'=>$this->input->post('c'),
		                        'jenis_kelamin'=>$this->input->post('d'),
		                        'alamat_lengkap'=>$this->input->post('e'),
		                        'no_telpon'=>$this->input->post('f'),
		                        'email'=>$this->input->post('g'),
		                        'kode_pos'=>$this->input->post('h'),
		                        'keterangan'=>$this->input->post('i'),
		                        'referral'=>$this->input->post('j'),
		                        'kota_id'=>$this->input->post('kota'));
		        }else{
		           $data = array('nama_reseller'=>$this->input->post('c'),
		                        'jenis_kelamin'=>$this->input->post('d'),
		                        'alamat_lengkap'=>$this->input->post('e'),
		                        'no_telpon'=>$this->input->post('f'),
		                        'email'=>$this->input->post('g'),
		                        'kode_pos'=>$this->input->post('h'),
		                        'keterangan'=>$this->input->post('i'),
		                        'referral'=>$this->input->post('j'),
		                        'kota_id'=>$this->input->post('kota'));
		        }
		    }else{
				if (trim($this->input->post('b')) != ''){
		            $data = array('password'=>hash("sha512", md5($this->input->post('b'))),
		                        'nama_reseller'=>$this->input->post('c'),
		                        'jenis_kelamin'=>$this->input->post('d'),
		                        'alamat_lengkap'=>$this->input->post('e'),
		                        'no_telpon'=>$this->input->post('f'),
		                        'email'=>$this->input->post('g'),
		                        'kode_pos'=>$this->input->post('h'),
		                        'keterangan'=>$this->input->post('i'),
		                        'foto'=>$hasil['file_name'],
		                        'referral'=>$this->input->post('j'),
		                        'kota_id'=>$this->input->post('kota'));
		        }else{
		           $data = array('nama_reseller'=>$this->input->post('c'),
		                        'jenis_kelamin'=>$this->input->post('d'),
		                        'alamat_lengkap'=>$this->input->post('e'),
		                        'no_telpon'=>$this->input->post('f'),
		                        'email'=>$this->input->post('g'),
		                        'kode_pos'=>$this->input->post('h'),
		                        'keterangan'=>$this->input->post('i'),
		                        'foto'=>$hasil['file_name'],
		                        'referral'=>$this->input->post('j'),
		                        'kota_id'=>$this->input->post('kota'));
		        }
		    }
			$where = array('id_reseller' => $this->input->post('id'));
			$this->model_app->update('rb_reseller', $data, $where);
			redirect($this->uri->segment(1).'/detail_reseller');
		}else{
			$edit = $this->model_app->edit('rb_reseller',array('id_reseller'=>$id))->row_array();
			$data = array('rows' => $edit);
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_reseller/view_reseller_edit',$data);
		}
	}

	function detail_reseller(){
		cek_session_reseller();
		$id = $this->session->id_reseller;
		$edit = $this->model_app->edit('rb_reseller',array('id_reseller'=>$id))->row_array();
		$data = array('rows' => $edit);
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_reseller/view_reseller_detail',$data);
	}


	// Controller Modul Produk

	function produk(){
		cek_session_reseller();
		if (isset($_POST['submit'])){
			$jml = $this->model_app->view('rb_produk')->num_rows();
			for ($i=1; $i<=$jml; $i++){
				$a  = $_POST['a'][$i];
				$b  = $_POST['b'][$i];
				$cek = $this->model_app->edit('rb_produk_diskon',array('id_produk'=>$a,'id_reseller'=>$this->session->id_reseller))->num_rows();
				if ($cek >= 1){
					if ($b > 0){
						$data = array('diskon'=>$b);
						$where = array('id_produk' => $a,'id_reseller' => $this->session->id_reseller);
						$this->model_app->update('rb_produk_diskon', $data, $where);
					}else{
						$this->model_app->delete('rb_produk_diskon',array('id_produk'=>$a,'id_reseller'=>$this->session->id_reseller));
					}
				}else{
					if ($b > 0){
						$data = array('id_produk'=>$a,
			                          'id_reseller'=>$this->session->id_reseller,
			                          'diskon'=>$b);
						$this->model_app->insert('rb_produk_diskon',$data);
					}
				}
			}
			redirect($this->uri->segment(1).'/produk');
		}else{
			$data['record'] = $this->model_app->view_where_ordering('rb_produk',array('id_reseller'=>$this->session->id_reseller),'id_produk','DESC');
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_produk/view_produk',$data);
		}
	}

	function import_produk(){
		cek_session_reseller();
		$this->load->library('excel_import');
		
		if (isset($_POST['submit'])){
			$type = $this->input->post('type'); // 'physical' atau 'digital'
			
			if (isset($_FILES['excel_file']['name']) && !empty($_FILES['excel_file']['name'])) {
				$config['upload_path'] = 'asset/files/excel/';
				$config['allowed_types'] = 'csv|xls|xlsx';
				$config['max_size'] = '2048'; // 2MB
				$config['encrypt_name'] = TRUE;
				
				// Buat folder jika belum ada
				if (!is_dir($config['upload_path'])) {
					mkdir($config['upload_path'], 0777, true);
				}
				
				$this->load->library('upload', $config);
				
				if ($this->upload->do_upload('excel_file')) {
					$upload_data = $this->upload->data();
					$file_path = $config['upload_path'] . $upload_data['file_name'];
					
					// Baca file Excel
					$excel_data = $this->excel_import->read_excel($file_path);
					
					if (isset($excel_data['error'])) {
						$data['error'] = $excel_data['error'];
					} else {
						// Validasi data
						$validation = $this->excel_import->validate_product_data($excel_data, $type);
						
						if (!empty($validation['errors'])) {
							$data['errors'] = $validation['errors'];
							$data['success_count'] = count($validation['valid_data']);
						} else {
							// Import data ke database
							$success_count = 0;
							foreach ($validation['valid_data'] as $row) {
								$result = $this->import_single_product($row, $type);
								if ($result) $success_count++;
							}
							
							$data['success'] = "Berhasil mengimport $success_count produk!";
						}
					}
					
					// Hapus file temporary
					unlink($file_path);
				} else {
					$data['error'] = $this->upload->display_errors();
				}
			} else {
				$data['error'] = 'Pilih file Excel terlebih dahulu!';
			}
		}
		
		$data['title'] = 'Import Produk Excel';
		$this->template->load('reseller/template','reseller/mod_produk/view_import_produk',$data);
	}

	private function import_single_product($row, $type) {
		// Cari ID kategori berdasarkan nama
		$kategori = $this->db->query("SELECT id_kategori_produk FROM rb_kategori_produk WHERE nama_kategori = ?", array($row['kategori']))->row();
		$id_kategori = $kategori ? $kategori->id_kategori_produk : 1; // Default ke ID 1 jika tidak ditemukan
		
		// Cari ID sub kategori berdasarkan nama
		$sub_kategori = $this->db->query("SELECT id_kategori_produk_sub FROM rb_kategori_produk_sub WHERE nama_kategori_sub = ?", array($row['sub_kategori']))->row();
		$id_sub_kategori = $sub_kategori ? $sub_kategori->id_kategori_produk_sub : 1; // Default ke ID 1 jika tidak ditemukan
		
		// Data untuk tabel rb_produk
		$data_produk = array(
			'id_kategori_produk' => $id_kategori,
			'id_kategori_produk_sub' => $id_sub_kategori,
			'nama_produk' => $row['nama_produk'],
			'produk_seo' => seo_title($row['nama_produk']),
			'satuan' => $row['satuan'],
			'harga_beli' => $row['harga_beli'],
			'harga_reseller' => $row['harga_reseller'],
			'harga_konsumen' => $row['harga_konsumen'],
			'keterangan' => $row['keterangan'],
			'id_reseller' => $this->session->id_reseller,
			'username' => $this->session->username,
			'waktu_input' => date('Y-m-d H:i:s')
		);
		
		// Insert ke tabel rb_produk
		$this->model_app->insert('rb_produk', $data_produk);
		$id_produk = $this->db->insert_id();
		
		// Jika produk digital, insert ke tabel rb_produk_digital
		if ($type == 'digital') {
			$data_digital = array(
				'id_produk' => $id_produk,
				'nama_ebook' => $row['nama_ebook'] ?: $row['nama_produk'],
				'format_file' => strtoupper($row['format_file']),
				'status' => 'aktif'
			);
			
			$this->model_app->insert('rb_produk_digital', $data_digital);
		}
		
		return true;
	}

	function download_template($type = 'physical') {
		cek_session_reseller();
		$this->load->library('excel_import');
		
		if ($type == 'digital') {
			$template = $this->excel_import->generate_digital_template();
			$filename = 'template_produk_digital_reseller.csv';
		} else {
			$template = $this->excel_import->generate_physical_template();
			$filename = 'template_produk_fisik_reseller.csv';
		}
		
		// Generate CSV content
		$csv_content = implode(',', $template['headers']) . "\n";
		$csv_content .= implode(',', $template['sample_data']);
		
		// Set headers untuk download
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
		
		echo $csv_content;
		exit;
	}

	function tambah_produk(){
        cek_session_reseller();
        if (isset($_POST['submit'])){
            $files = $_FILES;
            $cpt = count($_FILES['userfile']['name']);
            for($i=0; $i<$cpt; $i++){
                $_FILES['userfile']['name']= $files['userfile']['name'][$i];
                $_FILES['userfile']['type']= $files['userfile']['type'][$i];
                $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
                $_FILES['userfile']['error']= $files['userfile']['error'][$i];
                $_FILES['userfile']['size']= $files['userfile']['size'][$i];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options());
                $this->upload->do_upload();
                $fileName = $this->upload->data()['file_name'];
                $images[] = $fileName;
            }
            $fileName = implode(';',$images);
            $fileName = str_replace(' ','_',$fileName);
            if (trim($fileName)!=''){
                $data = array('id_kategori_produk'=>$this->input->post('a'),
                			  'id_kategori_produk_sub'=>$this->input->post('aa'),
                			  'id_reseller'=>$this->session->id_reseller,
                              'nama_produk'=>$this->input->post('b'),
                              'produk_seo'=>seo_title($this->input->post('b')),
                              'satuan'=>$this->input->post('c'),
                              'harga_beli'=>$this->input->post('d'),
                              'harga_reseller'=>$this->input->post('e'),
                              'harga_konsumen'=>$this->input->post('f'),
                              'berat'=>$this->input->post('berat'),
                              'gambar'=>$fileName,
                              'keterangan'=>$this->input->post('ff'),
                              'username'=>$this->session->username,
                              'waktu_input'=>date('Y-m-d H:i:s'));
            }else{
                $data = array('id_kategori_produk'=>$this->input->post('a'),
                			  'id_kategori_produk_sub'=>$this->input->post('aa'),
                			  'id_reseller'=>$this->session->id_reseller,
                              'nama_produk'=>$this->input->post('b'),
                              'produk_seo'=>seo_title($this->input->post('b')),
                              'satuan'=>$this->input->post('c'),
                              'harga_beli'=>$this->input->post('d'),
                              'harga_reseller'=>$this->input->post('e'),
                              'harga_konsumen'=>$this->input->post('f'),
                              'berat'=>$this->input->post('berat'),
                              'keterangan'=>$this->input->post('ff'),
                              'username'=>$this->session->username,
                              'waktu_input'=>date('Y-m-d H:i:s'));
            }
            $this->model_app->insert('rb_produk',$data);
            $id_produk = $this->db->insert_id();
            if ($this->input->post('diskon') > 0){
            	$cek = $this->db->query("SELECT * FROM rb_produk_diskon where id_produk='".$id_produk."' AND id_reseller='".$this->session->id_reseller."'");
				if ($cek->num_rows()>=1){
					$data = array('diskon'=>$this->input->post('diskon'));
					$where = array('id_produk' => $id_produk,'id_reseller' => $this->session->id_reseller);
					$this->model_app->update('rb_produk_diskon', $data, $where);
				}else{
					$data = array('id_produk'=>$id_produk,
			                      'id_reseller'=>$this->session->id_reseller,
			                      'diskon'=>$this->input->post('diskon'));
					$this->model_app->insert('rb_produk_diskon',$data);
				}
			}


			if ($this->input->post('stok') != ''){
				$kode_transaksi = "TRX-".date('YmdHis');
				$data = array('kode_transaksi'=>$kode_transaksi,
			        		  'id_pembeli'=>$this->session->id_reseller,
			        		  'id_penjual'=>'1',
			        		  'status_pembeli'=>'reseller',
			        		  'status_penjual'=>'admin',
			        		  'service'=>'Stok Otomatis (Pribadi)',
			        		  'waktu_transaksi'=>date('Y-m-d H:i:s'),
			        		  'proses'=>'1');
				$this->model_app->insert('rb_penjualan',$data);
				$idp = $this->db->insert_id();

				$data = array('id_penjualan'=>$idp,
		        			  'id_produk'=>$id_produk,
		        			  'jumlah'=>$this->input->post('stok'),
		        			  'harga_jual'=>$this->input->post('e'),
		        			  'satuan'=>$this->input->post('c'));
				$this->model_app->insert('rb_penjualan_detail',$data);
			}

            redirect('reseller/produk');
        }else{
            $data['record'] = $this->model_app->view_ordering('rb_kategori_produk','id_kategori_produk','DESC');
            $this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_produk/view_produk_tambah',$data);
        }
    }

    function edit_produk(){
        cek_session_reseller();
        $id = $this->uri->segment(3);
        if (isset($_POST['submit'])){
            $files = $_FILES;
            $cpt = count($_FILES['userfile']['name']);
            for($i=0; $i<$cpt; $i++){
                $_FILES['userfile']['name']= $files['userfile']['name'][$i];
                $_FILES['userfile']['type']= $files['userfile']['type'][$i];
                $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
                $_FILES['userfile']['error']= $files['userfile']['error'][$i];
                $_FILES['userfile']['size']= $files['userfile']['size'][$i];
                $this->load->library('upload');
                $this->upload->initialize($this->set_upload_options());
                $this->upload->do_upload();
                $fileName = $this->upload->data()['file_name'];
                $images[] = $fileName;
            }
            $fileName = implode(';',$images);
            $fileName = str_replace(' ','_',$fileName);
            if (trim($fileName)!=''){
                $data = array('id_kategori_produk'=>$this->input->post('a'),
                			  'id_kategori_produk_sub'=>$this->input->post('aa'),
                              'nama_produk'=>$this->input->post('b'),
                              'produk_seo'=>seo_title($this->input->post('b')),
                              'satuan'=>$this->input->post('c'),
                              'harga_beli'=>$this->input->post('d'),
                              'harga_reseller'=>$this->input->post('e'),
                              'harga_konsumen'=>$this->input->post('f'),
                              'berat'=>$this->input->post('berat'),
                              'gambar'=>$fileName,
                              'keterangan'=>$this->input->post('ff'),
                              'username'=>$this->session->username);
            }else{
                $data = array('id_kategori_produk'=>$this->input->post('a'),
                			  'id_kategori_produk_sub'=>$this->input->post('aa'),
                              'nama_produk'=>$this->input->post('b'),
                              'produk_seo'=>seo_title($this->input->post('b')),
                              'satuan'=>$this->input->post('c'),
                              'harga_beli'=>$this->input->post('d'),
                              'harga_reseller'=>$this->input->post('e'),
                              'harga_konsumen'=>$this->input->post('f'),
                              'berat'=>$this->input->post('berat'),
                              'keterangan'=>$this->input->post('ff'),
                              'username'=>$this->session->username);
            }

            $where = array('id_produk' => $this->input->post('id'),'id_reseller'=>$this->session->id_reseller);
            $this->model_app->update('rb_produk', $data, $where);

            if ($this->input->post('diskon') >= 0){
            	$cek = $this->db->query("SELECT * FROM rb_produk_diskon where id_produk='".$this->input->post('id')."' AND id_reseller='".$this->session->id_reseller."'");
				if ($cek->num_rows()>=1){
					$data = array('diskon'=>$this->input->post('diskon'));
					$where = array('id_produk' => $this->input->post('id'),'id_reseller' => $this->session->id_reseller);
					$this->model_app->update('rb_produk_diskon', $data, $where);
				}else{
					$data = array('id_produk'=>$this->input->post('id'),
			                      'id_reseller'=>$this->session->id_reseller,
			                      'diskon'=>$this->input->post('diskon'));
					$this->model_app->insert('rb_produk_diskon',$data);
				}
			}

			if ($this->input->post('stok') != ''){
				$kode_transaksi = "TRX-".date('YmdHis');
				$data = array('kode_transaksi'=>$kode_transaksi,
			        		  'id_pembeli'=>$this->session->id_reseller,
			        		  'id_penjual'=>'1',
			        		  'status_pembeli'=>'reseller',
			        		  'status_penjual'=>'admin',
			        		  'service'=>'Stok Otomatis (Pribadi)',
			        		  'waktu_transaksi'=>date('Y-m-d H:i:s'),
			        		  'proses'=>'1');
				$this->model_app->insert('rb_penjualan',$data);
				$idp = $this->db->insert_id();

				$data = array('id_penjualan'=>$idp,
		        			  'id_produk'=>$this->input->post('id'),
		        			  'jumlah'=>$this->input->post('stok'),
		        			  'harga_jual'=>$this->input->post('e'),
		        			  'satuan'=>$this->input->post('c'));
				$this->model_app->insert('rb_penjualan_detail',$data);
			}

            redirect('reseller/produk');
        }else{
            $data['record'] = $this->model_app->view_ordering('rb_kategori_produk','id_kategori_produk','DESC');
            $data['rows'] = $this->model_app->edit('rb_produk',array('id_produk'=>$id,'id_reseller'=>$this->session->id_reseller))->row_array();
            $this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_produk/view_produk_edit',$data);
        }
    }

    private function set_upload_options(){
        $config = array();
        $config['upload_path'] = 'asset/foto_produk/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '5000'; // kb
        $config['encrypt_name'] = FALSE;
        $this->load->library('upload', $config);
      return $config;
    }

    function delete_produk(){
        cek_session_reseller();
        $id = array('id_produk' => $this->uri->segment(3));
        $this->model_app->delete('rb_produk',$id);
        redirect('reseller/produk');
    }


	// Controller Modul Rekening

	function rekening(){
		cek_session_reseller();
		$data['record'] = $this->model_app->view_where('rb_rekening_reseller',array('id_reseller'=>$this->session->id_reseller));
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_rekening/view_rekening',$data);
	}

	function tambah_rekening(){
		cek_session_reseller();
		if (isset($_POST['submit'])){
			$data = array('id_reseller'=>$this->session->id_reseller,
			              'nama_bank'=>$this->input->post('a'),
			              'no_rekening'=>$this->input->post('b'),
			              'pemilik_rekening'=>$this->input->post('c'));
						$this->model_app->insert('rb_rekening_reseller',$data);
			redirect($this->uri->segment(1).'/rekening');
		}else{
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_rekening/view_rekening_tambah');
		}
	}

	function edit_rekening(){
		cek_session_reseller();
		$id = $this->uri->segment(3);
		if (isset($_POST['submit'])){
			$data = array('id_reseller'=>$this->session->id_reseller,
			              'nama_bank'=>$this->input->post('a'),
			              'no_rekening'=>$this->input->post('b'),
			              'pemilik_rekening'=>$this->input->post('c'));
			$where = array('id_rekening_reseller' => $this->input->post('id'),'id_reseller' => $this->session->id_reseller);
			$this->model_app->update('rb_rekening_reseller', $data, $where);
			redirect($this->uri->segment(1).'/rekening');
		}else{
			$data['rows'] = $this->model_app->edit('rb_rekening_reseller',array('id_rekening_reseller'=>$id))->row_array();
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_rekening/view_rekening_edit',$data);
		}
	}

	function delete_rekening(){
		cek_session_reseller();
		$id = array('id_rekening_reseller' => $this->uri->segment(3));
		$this->model_app->delete('rb_rekening_reseller',$id);
		redirect($this->uri->segment(1).'/rekening');
	}



	// Controller Modul Pembelian

	function pembelian(){
		cek_session_reseller();
		$this->session->unset_userdata('idp');
		$data['record'] = $this->model_reseller->reseller_pembelian($this->session->id_reseller,'admin');
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_pembelian/view_pembelian',$data);
	}

	function detail_pembelian(){
		cek_session_reseller();
		$data['rows'] = $this->model_reseller->penjualan_detail($this->uri->segment(3))->row_array();
		$data['record'] = $this->model_app->view_join_where('rb_penjualan_detail','rb_produk','id_produk',array('id_penjualan'=>$this->uri->segment(3)),'id_penjualan_detail','DESC');
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_pembelian/view_pembelian_detail',$data);
	}

	function tambah_pembelian(){
		cek_session_reseller();
		if(isset($_POST['submit'])){
			if ($this->session->idp == ''){
				$kode_transaksi = "TRX-".date('YmdHis');
				$data = array('kode_transaksi'=>$kode_transaksi,
			        		  'id_pembeli'=>$this->session->id_reseller,
			        		  'id_penjual'=>'1',
			        		  'status_pembeli'=>'reseller',
			        		  'status_penjual'=>'admin',
			        		  'waktu_transaksi'=>date('Y-m-d H:i:s'),
			        		  'proses'=>'0');
				$this->model_app->insert('rb_penjualan',$data);
				$idp = $this->db->insert_id();
				$this->session->set_userdata(array('idp'=>$idp));
			}

	        if ($this->input->post('idpd')==''){
				$data = array('id_penjualan'=>$this->session->idp,
		        			  'id_produk'=>$this->input->post('aa'),
		        			  'jumlah'=>$this->input->post('dd'),
		        			  'harga_jual'=>$this->input->post('bb'),
		        			  'satuan'=>$this->input->post('ee'));
				$this->model_app->insert('rb_penjualan_detail',$data);
			}else{
		        $data = array('id_produk'=>$this->input->post('aa'),
		        			  'jumlah'=>$this->input->post('dd'),
		        			  'harga_jual'=>$this->input->post('bb'),
		        			  'satuan'=>$this->input->post('ee'));
				$where = array('id_penjualan_detail' => $this->input->post('idpd'));
				$this->model_app->update('rb_penjualan_detail', $data, $where);
			}
			redirect($this->uri->segment(1).'/tambah_pembelian');
		}else{
			$data['rows'] = $this->model_reseller->penjualan_detail($this->session->idp)->row_array();
			$data['record'] = $this->model_app->view_join_where('rb_penjualan_detail','rb_produk','id_produk',array('id_penjualan'=>$this->session->idp),'id_penjualan_detail','DESC');
			$data['barang'] = $this->model_app->view_where_ordering('rb_produk',array('id_reseller'=>'0'),'id_produk','ASC');
			$data['reseller'] = $this->model_app->view_ordering('rb_reseller','id_reseller','ASC');
			if ($this->uri->segment(3)!=''){
				$data['row'] = $this->model_app->view_where('rb_penjualan_detail',array('id_penjualan_detail'=>$this->uri->segment(3)))->row_array();
			}
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_pembelian/view_pembelian_tambah',$data);
		}
	}

	function delete_pembelian(){
        cek_session_reseller();
		$id = array('id_penjualan' => $this->uri->segment(3));
		$this->model_app->delete('rb_penjualan',$id);
		$this->model_app->delete('rb_penjualan_detail',$id);
		redirect($this->uri->segment(1).'/pembelian');
	}

	function delete_pembelian_tambah_detail(){
        cek_session_reseller();
		$id = array('id_penjualan_detail' => $this->uri->segment(3));
		$this->model_app->delete('rb_penjualan_detail',$id);
		redirect($this->uri->segment(1).'/tambah_pembelian');
	}

	function konfirmasi_pembayaran(){
		cek_session_reseller();
		$id = $this->uri->segment(3);
		if (isset($_POST['submit'])){
			$config['upload_path'] = 'asset/files/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '10000'; // kb
            $this->load->library('upload', $config);
            $this->upload->do_upload('f');
            $hasil=$this->upload->data();
            if ($hasil['file_name']==''){
				$data = array('id_penjualan'=>$this->input->post('id'),
			        		  'total_transfer'=>$this->input->post('b'),
			        		  'id_rekening'=>$this->input->post('c'),
			        		  'nama_pengirim'=>$this->input->post('d'),
			        		  'tanggal_transfer'=>$this->input->post('e'),
			        		  'waktu_konfirmasi'=>date('Y-m-d H:i:s'));
				$this->model_app->insert('rb_konfirmasi_pembayaran',$data);
			}else{
				$data = array('id_penjualan'=>$this->input->post('id'),
			        		  'total_transfer'=>$this->input->post('b'),
			        		  'id_rekening'=>$this->input->post('c'),
			        		  'nama_pengirim'=>$this->input->post('d'),
			        		  'tanggal_transfer'=>$this->input->post('e'),
			        		  'bukti_transfer'=>$hasil['file_name'],
			        		  'waktu_konfirmasi'=>date('Y-m-d H:i:s'));
				$this->model_app->insert('rb_konfirmasi_pembayaran',$data);
			}
				$data1 = array('proses'=>'2');
				$where = array('id_penjualan' => $this->input->post('id'));
				$this->model_app->update('rb_penjualan', $data1, $where);
			redirect($this->uri->segment(1).'/pembelian');
		}else{
			$data['record'] = $this->model_app->view('rb_rekening');
			$data['total'] = $this->db->query("SELECT sum((a.harga_jual*a.jumlah)-a.diskon) as total FROM `rb_penjualan_detail` a where a.id_penjualan='".$this->uri->segment(3)."'")->row_array();
			$data['rows'] = $this->model_app->view_where('rb_penjualan',array('id_penjualan'=>$this->uri->segment(3)))->row_array();
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_pembelian/view_konfirmasi_pembayaran',$data);
		}
	}

	function keterangan(){
		cek_session_reseller();
		if (isset($_POST['submit'])){
			$cek = $this->model_app->view_where('rb_keterangan',array('id_reseller'=>$this->session->id_reseller))->num_rows();
			if ($cek>=1){
				$data1 = array('keterangan'=>$this->input->post('a'));
				$where = array('id_keterangan' => $this->input->post('id'),'id_reseller'=>$this->session->id_reseller);
				$this->model_app->update('rb_keterangan', $data1, $where);
			}else{
				$data = array('id_reseller'=>$this->session->id_reseller,
							   'keterangan'=>$this->input->post('a'),
							   'tanggal_posting'=>date('Y-m-d H:i:s'));
				$this->model_app->insert('rb_keterangan',$data);
			}
			redirect($this->uri->segment(1).'/keterangan');
		}else{
			$data['record'] = $this->model_app->edit('rb_keterangan',array('id_reseller'=>$this->session->id_reseller))->row_array();
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_keterangan/view_keterangan',$data);
		}
	}

	function penjualan(){
		cek_session_reseller();
		$this->session->unset_userdata('idp');
		$id = $this->session->id_reseller;
		$data['record'] = $this->model_reseller->penjualan_list_konsumen($id,'reseller');
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_penjualan/view_penjualan',$data);
	}

	function detail_penjualan(){
		cek_session_reseller();
		$data['rows'] = $this->model_reseller->penjualan_konsumen_detail_reseller($this->uri->segment(3))->row_array();
		$data['record'] = $this->model_app->view_join_where('rb_penjualan_detail','rb_produk','id_produk',array('id_penjualan'=>$this->uri->segment(3)),'id_penjualan_detail','DESC');
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_penjualan/view_penjualan_detail',$data);
	}

	function tambah_penjualan(){
		cek_session_reseller();
		if (isset($_POST['submit1'])){
			if ($this->session->idp == ''){
				$data = array('kode_transaksi'=>$this->input->post('a'),
			        		  'id_pembeli'=>$this->input->post('b'),
			        		  'id_penjual'=>$this->session->id_reseller,
			        		  'status_pembeli'=>'konsumen',
			        		  'status_penjual'=>'reseller',
			        		  'waktu_transaksi'=>date('Y-m-d H:i:s'),
			        		  'proses'=>'0');
				$this->model_app->insert('rb_penjualan',$data);
				$idp = $this->db->insert_id();
				$this->session->set_userdata(array('idp'=>$idp));
			}else{
				$data = array('kode_transaksi'=>$this->input->post('a'),
			        		  'id_pembeli'=>$this->input->post('b'));
				$where = array('id_penjualan' => $this->session->idp);
				$this->model_app->update('rb_penjualan', $data, $where);
			}
				redirect($this->uri->segment(1).'/tambah_penjualan');

		}elseif(isset($_POST['submit'])){
			$jual = $this->model_reseller->jual_reseller($this->session->id_reseller, $this->input->post('aa'))->row_array();
            $beli = $this->model_reseller->beli_reseller($this->session->id_reseller, $this->input->post('aa'))->row_array();
            $stok = $beli['beli']-$jual['jual'];
            if ($this->input->post('dd') > $stok){
            	echo "<script>window.alert('Maaf, Stok Tidak Mencukupi!');
                                  window.location=('".base_url().$this->uri->segment(1)."/tambah_penjualan')</script>";
            }else{
		        if ($this->input->post('idpd')==''){
					$data = array('id_penjualan'=>$this->session->idp,
			        			  'id_produk'=>$this->input->post('aa'),
			        			  'jumlah'=>$this->input->post('dd'),
			        			  'harga_jual'=>$this->input->post('bb'),
			        			  'satuan'=>$this->input->post('ee'));
					$this->model_app->insert('rb_penjualan_detail',$data);
				}else{
			        $data = array('id_produk'=>$this->input->post('aa'),
			        			  'jumlah'=>$this->input->post('dd'),
			        			  'harga_jual'=>$this->input->post('bb'),
			        			  'satuan'=>$this->input->post('ee'));
					$where = array('id_penjualan_detail' => $this->input->post('idpd'));
					$this->model_app->update('rb_penjualan_detail', $data, $where);
				}
				redirect($this->uri->segment(1).'/tambah_penjualan');
			}
			
		}else{
			$data['rows'] = $this->model_reseller->penjualan_konsumen_detail_reseller($this->session->idp)->row_array();
			$data['record'] = $this->model_app->view_join_where('rb_penjualan_detail','rb_produk','id_produk',array('id_penjualan'=>$this->session->idp),'id_penjualan_detail','DESC');
			$data['barang'] = $this->model_app->view_ordering('rb_produk','id_produk','ASC');
			$data['konsumen'] = $this->model_app->view_ordering('rb_konsumen','id_konsumen','ASC');
			if ($this->uri->segment(3)!=''){
				$data['row'] = $this->model_app->view_where('rb_penjualan_detail',array('id_penjualan_detail'=>$this->uri->segment(3)))->row_array();
			}
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_penjualan/view_penjualan_tambah',$data);
		}
	}

	function edit_penjualan(){
		cek_session_reseller();
		if (isset($_POST['submit1'])){
			$data = array('kode_transaksi'=>$this->input->post('a'),
			        	  'id_pembeli'=>$this->input->post('b'),
			        	  'waktu_transaksi'=>$this->input->post('c'));
			$where = array('id_penjualan' => $this->input->post('idp'));
			$this->model_app->update('rb_penjualan', $data, $where);
			redirect($this->uri->segment(1).'/edit_penjualan/'.$this->input->post('idp'));

		}elseif(isset($_POST['submit'])){
			$cekk = $this->db->query("SELECT * FROM rb_penjualan_detail where id_penjualan='".$this->input->post('idp')."' AND id_produk='".$this->input->post('aa')."'")->row_array();
			$jual = $this->model_reseller->jual_reseller($this->session->id_reseller, $this->input->post('aa'))->row_array();
            $beli = $this->model_reseller->beli_reseller($this->session->id_reseller, $this->input->post('aa'))->row_array();
            $stok = $beli['beli']-$jual['jual']+$cekk['jumlah'];
            if ($this->input->post('dd') > $stok){
            	echo "<script>window.alert('Maaf, Stok $stok Tidak Mencukupi!');
                                  window.location=('".base_url().$this->uri->segment(1)."/edit_penjualan/".$this->input->post('idp')."')</script>";
            }else{
				if ($this->input->post('idpd')==''){
					$data = array('id_penjualan'=>$this->input->post('idp'),
			        			  'id_produk'=>$this->input->post('aa'),
			        			  'jumlah'=>$this->input->post('dd'),
			        			  'harga_jual'=>$this->input->post('bb'),
			        			  'satuan'=>$this->input->post('ee'));
					$this->model_app->insert('rb_penjualan_detail',$data);
				}else{
			        $data = array('id_produk'=>$this->input->post('aa'),
			        			  'jumlah'=>$this->input->post('dd'),
			        			  'harga_jual'=>$this->input->post('bb'),
			        			  'satuan'=>$this->input->post('ee'));
					$where = array('id_penjualan_detail' => $this->input->post('idpd'));
					$this->model_app->update('rb_penjualan_detail', $data, $where);
				}
				redirect($this->uri->segment(1).'/edit_penjualan/'.$this->input->post('idp'));
			}
			
		}else{
			$data['rows'] = $this->model_reseller->penjualan_konsumen_detail_reseller($this->uri->segment(3))->row_array();
			$data['record'] = $this->model_app->view_join_where('rb_penjualan_detail','rb_produk','id_produk',array('id_penjualan'=>$this->uri->segment(3)),'id_penjualan_detail','DESC');
			$data['barang'] = $this->model_app->view_ordering('rb_produk','id_produk','ASC');
			$data['konsumen'] = $this->model_app->view_ordering('rb_konsumen','id_konsumen','ASC');
			if ($this->uri->segment(4)!=''){
				$data['row'] = $this->model_app->view_where('rb_penjualan_detail',array('id_penjualan_detail'=>$this->uri->segment(4)))->row_array();
			}
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_penjualan/view_penjualan_edit',$data);
		}
	}

	function proses_penjualan(){
		cek_session_reseller();
	        $data = array('proses'=>$this->uri->segment(4));
			$where = array('id_penjualan' => $this->uri->segment(3));
			$this->model_app->update('rb_penjualan', $data, $where);
			redirect($this->uri->segment(1).'/penjualan');
	}

	function proses_penjualan_detail(){
		cek_session_reseller();
        $data = array('proses'=>$this->uri->segment(4));
		$where = array('id_penjualan' => $this->uri->segment(3));
		$this->model_app->update('rb_penjualan', $data, $where);
		redirect($this->uri->segment(1).'/detail_penjualan/'.$this->uri->segment(3));
	}

	function delete_penjualan(){
        cek_session_reseller();
		$id = array('id_penjualan' => $this->uri->segment(3));
		$this->model_app->delete('rb_penjualan',$id);
		$this->model_app->delete('rb_penjualan_detail',$id);
		redirect($this->uri->segment(1).'/penjualan');
	}

	function delete_penjualan_detail(){
        cek_session_reseller();
		$id = array('id_penjualan_detail' => $this->uri->segment(4));
		$this->model_app->delete('rb_penjualan_detail',$id);
		redirect($this->uri->segment(1).'/edit_penjualan/'.$this->uri->segment(3));
	}

	function delete_penjualan_tambah_detail(){
        cek_session_reseller();
		$id = array('id_penjualan_detail' => $this->uri->segment(3));
		$this->model_app->delete('rb_penjualan_detail',$id);
		redirect($this->uri->segment(1).'/tambah_penjualan');
	}

	function detail_konsumen(){
		cek_session_reseller();
		$id = $this->uri->segment(3);
		$edit = $this->model_app->edit('rb_konsumen',array('id_konsumen'=>$id))->row_array();
		$data = array('rows' => $edit);
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_konsumen/view_konsumen_detail',$data);
	}

	function pembayaran_konsumen(){
		cek_session_reseller();
		$data['record'] = $this->db->query("SELECT a.*, b.*, c.kode_transaksi, c.proses FROM `rb_konfirmasi_pembayaran_konsumen` a JOIN rb_rekening_reseller b ON a.id_rekening=b.id_rekening_reseller JOIN rb_penjualan c ON a.id_penjualan=c.id_penjualan where b.id_reseller='".$this->session->id_reseller."'");
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_konsumen/view_konsumen_pembayaran',$data);
	}

	function download(){
		$name = $this->uri->segment(3);
		$data = file_get_contents("asset/files/".$name);
		force_download($name, $data);
	}

	function keuangan(){
		cek_session_reseller();
		$id = $this->session->id_reseller;
		$record = $this->model_reseller->reseller_pembelian($id,'admin');
		$penjualan = $this->model_reseller->penjualan_list_konsumen($id,'reseller');
		$edit = $this->model_app->edit('rb_reseller',array('id_reseller'=>$id))->row_array();
		$reward = $this->model_app->view_ordering('rb_reward','id_reward','ASC');

		$data = array('rows' => $edit,'record'=>$record,'penjualan'=>$penjualan,'reward'=>$reward);
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_reseller/view_reseller_keuangan',$data);
	}

	// Controller Modul COD

	function alamat_cod(){
		cek_session_reseller();
		$data['record'] = $this->model_app->view_where('rb_reseller_cod',array('id_reseller'=>$this->session->id_reseller));
		$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_alamat_cod/view',$data);
	}

	function tambah_cod(){
		cek_session_reseller();
		if (isset($_POST['submit'])){
			$data = array('id_reseller'=>$this->session->id_reseller,
			              'nama_alamat'=>$this->input->post('a'),
			              'biaya_cod'=>$this->input->post('b'));
						$this->model_app->insert('rb_reseller_cod',$data);
			redirect($this->uri->segment(1).'/alamat_cod');
		}else{
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_alamat_cod/tambah');
		}
	}

	function edit_cod(){
		cek_session_reseller();
		$id = $this->uri->segment(3);
		if (isset($_POST['submit'])){
			$data = array('id_reseller'=>$this->session->id_reseller,
			              'nama_alamat'=>$this->input->post('a'),
			              'biaya_cod'=>$this->input->post('b'));
			$where = array('id_cod' => $this->input->post('id'),'id_reseller' => $this->session->id_reseller);
			$this->model_app->update('rb_reseller_cod', $data, $where);
			redirect($this->uri->segment(1).'/alamat_cod');
		}else{
			$data['rows'] = $this->model_app->edit('rb_reseller_cod',array('id_cod'=>$id))->row_array();
			$this->template->load($this->uri->segment(1).'/template',$this->uri->segment(1).'/mod_alamat_cod/edit',$data);
		}
	}

	function delete_cod(){
		cek_session_reseller();
		$id = array('id_cod' => $this->uri->segment(3));
		$this->model_app->delete('rb_reseller_cod',$id);
		redirect($this->uri->segment(1).'/alamat_cod');
	}

	function produk_digital(){
		cek_session_reseller();
		$data['title'] = 'Produk Digital';
		$data['record'] = $this->db->query("SELECT pd.*, p.nama_produk, p.harga_konsumen, p.produk_seo, p.gambar, p.satuan, p.keterangan, pd.nama_ebook, pd.format_file, pd.ukuran_file 
											FROM rb_produk_digital pd 
											JOIN rb_produk p ON pd.id_produk = p.id_produk 
											WHERE pd.status = 'aktif' 
											ORDER BY pd.id_produk_digital DESC");
		$this->template->load('reseller/template','reseller/mod_produk_digital/view_produk_digital',$data);
	}

	function tambah_produk_digital(){
		cek_session_reseller();
		if (isset($_POST['submit'])){
			$id_produk_digital = $this->input->post('id_produk_digital');
			$qty = $this->input->post('qty');
			
			if (empty($id_produk_digital)) {
				echo "<script>window.alert('Silakan pilih produk digital terlebih dahulu!');
					  window.location=('".base_url()."reseller/tambah_produk_digital')</script>";
				return;
			}
			
			// Ambil data produk digital
			$produk = $this->db->query("SELECT pd.*, p.nama_produk, p.harga_konsumen, p.satuan 
										FROM rb_produk_digital pd 
										JOIN rb_produk p ON pd.id_produk = p.id_produk 
										WHERE pd.id_produk_digital='$id_produk_digital'")->row_array();
			
			if (empty($produk)) {
				echo "<script>window.alert('Produk digital tidak ditemukan!');
					  window.location=('".base_url()."reseller/tambah_produk_digital')</script>";
				return;
			}
			
			// Cek apakah sudah ada di pembelian
			$cek = $this->db->query("SELECT * FROM rb_penjualan_detail WHERE id_produk='".$produk['id_produk']."' AND id_penjualan IN (SELECT id_penjualan FROM rb_penjualan WHERE id_pembeli='".$this->session->id_reseller."' AND status_pembeli='reseller' AND status_penjual='admin')")->num_rows();
			
			if ($cek == 0){
				// Buat transaksi pembelian baru
				$kode_transaksi = 'TRX-'.date('YmdHis');
				$data = array('kode_transaksi'=>$kode_transaksi,
							'id_pembeli'=>$this->session->id_reseller,
							'id_penjual'=>'0',
							'status_pembeli'=>'reseller',
							'status_penjual'=>'admin',
							'waktu_transaksi'=>date('Y-m-d H:i:s'),
							'proses'=>'1');
				$this->model_app->insert('rb_penjualan',$data);
				$id_penjualan = $this->db->insert_id();
				
				// Tambah detail pembelian
				$data_detail = array('id_penjualan'=>$id_penjualan,
									'id_produk'=>$produk['id_produk'],
									'jumlah'=>$qty,
									'harga_jual'=>$produk['harga_konsumen'],
									'satuan'=>$produk['satuan']);
				$this->model_app->insert('rb_penjualan_detail',$data_detail);
				
				echo "<script>window.alert('Produk digital berhasil ditambahkan ke pembelian!');
					  window.location=('".base_url()."reseller/produk_digital')</script>";
			}else{
				echo "<script>window.alert('Produk digital ini sudah ada di pembelian Anda!');
					  window.location=('".base_url()."reseller/produk_digital')</script>";
			}
		}else{
			$data['title'] = 'Tambah Produk Digital';
			$data['record'] = $this->db->query("SELECT pd.*, p.nama_produk, p.harga_konsumen, p.produk_seo, p.gambar, p.satuan, p.keterangan, pd.nama_ebook, pd.format_file, pd.ukuran_file 
												FROM rb_produk_digital pd 
												JOIN rb_produk p ON pd.id_produk = p.id_produk 
												WHERE pd.status = 'aktif' 
												ORDER BY pd.id_produk_digital DESC");
			$this->template->load('reseller/template','reseller/mod_produk_digital/view_produk_digital_tambah',$data);
		}
	}

	function buat_produk_digital(){
		cek_session_reseller();
		if (isset($_POST['submit'])){
			$fileName = '';
			$fileName_ebook = '';
			$fileName_preview = '';
			
			// Upload foto produk
			if (isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name'])) {
				$this->load->library('upload');
				$this->upload->initialize($this->set_upload_options());
				if ($this->upload->do_upload()) {
					$fileName = $this->upload->data()['file_name'];
				}
			}
			
			// Upload file ebook
			if (isset($_FILES['file_ebook']['name']) && !empty($_FILES['file_ebook']['name'])) {
				$config['upload_path'] = 'asset/files/ebook/';
				$config['allowed_types'] = 'pdf|doc|docx|epub|mobi';
				$config['max_size'] = '1048576'; // 1GB
				$config['encrypt_name'] = FALSE;
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('file_ebook')) {
					$upload_data = $this->upload->data();
					$fileName_ebook = $upload_data['file_name'];
					$ukuran_file = $this->formatBytes($upload_data['file_size']);
					$format_file = $upload_data['file_ext'];
				}
			}
			
			// Upload file preview
			if (isset($_FILES['preview_file']['name']) && !empty($_FILES['preview_file']['name'])) {
				$config['upload_path'] = 'asset/files/ebook/preview/';
				$config['allowed_types'] = 'pdf|jpg|jpeg|png';
				$config['max_size'] = '5120'; // 5MB
				$config['encrypt_name'] = FALSE;
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('preview_file')) {
					$upload_data = $this->upload->data();
					$fileName_preview = $upload_data['file_name'];
				}
			}
			
			// Insert ke tabel rb_produk
			$data_produk = array(
				'id_kategori_produk'=>$this->input->post('a'),
				'id_kategori_produk_sub'=>$this->input->post('aa'),
				'nama_produk'=>$this->input->post('b'),
				'produk_seo'=>seo_title($this->input->post('b')),
				'satuan'=>$this->input->post('c'),
				'harga_beli'=>$this->input->post('d'),
				'harga_reseller'=>$this->input->post('e'),
				'harga_konsumen'=>$this->input->post('f'),
				'gambar'=>$fileName,
				'keterangan'=>$this->input->post('ff'),
				'id_reseller'=>$this->session->id_reseller,
				'username'=>$this->session->username,
				'waktu_input'=>date('Y-m-d H:i:s')
			);
			
			$this->model_app->insert('rb_produk',$data_produk);
			$id_produk = $this->db->insert_id();
			
			// Insert ke tabel rb_produk_digital
			$data_digital = array(
				'id_produk'=>$id_produk,
				'nama_ebook'=>$this->input->post('b'), // Sama dengan nama produk
				'file_ebook'=>$fileName_ebook,
				'preview_file'=>$fileName_preview,
				'format_file'=>str_replace('.', '', $format_file),
				'ukuran_file'=>$ukuran_file,
				'status'=>'aktif'
			);
			
			$this->model_app->insert('rb_produk_digital',$data_digital);
			
			echo "<script>window.alert('Produk digital berhasil dibuat!');
				  window.location=('".base_url()."reseller/produk_digital_saya')</script>";
		}else{
			$data['title'] = 'Buat Produk Digital';
			$data['kategori'] = $this->model_app->view_ordering('rb_kategori_produk','id_kategori_produk','DESC');
			$this->template->load('reseller/template','reseller/mod_produk_digital/view_buat_produk_digital',$data);
		}
	}

	function produk_digital_saya(){
		cek_session_reseller();
		$data['title'] = 'Produk Digital Saya';
		$data['record'] = $this->db->query("SELECT pd.*, p.nama_produk, p.harga_konsumen, p.produk_seo, p.gambar, p.satuan, p.keterangan, pd.nama_ebook, pd.format_file, pd.ukuran_file 
											FROM rb_produk_digital pd 
											JOIN rb_produk p ON pd.id_produk = p.id_produk 
											WHERE p.id_reseller='".$this->session->id_reseller."' 
											ORDER BY pd.id_produk_digital DESC");
		$this->template->load('reseller/template','reseller/mod_produk_digital/view_produk_digital_saya',$data);
	}

	function edit_produk_digital_saya(){
		cek_session_reseller();
		$id = $this->uri->segment(3);
		if (isset($_POST['submit'])){
			$fileName = '';
			$fileName_ebook = '';
			$fileName_preview = '';
			
			// Upload foto produk
			if (isset($_FILES['userfile']['name']) && !empty($_FILES['userfile']['name'])) {
				$this->load->library('upload');
				$this->upload->initialize($this->set_upload_options());
				if ($this->upload->do_upload()) {
					$fileName = $this->upload->data()['file_name'];
				}
			}
			
			// Upload file ebook
			if (isset($_FILES['file_ebook']['name']) && !empty($_FILES['file_ebook']['name'])) {
				$config['upload_path'] = 'asset/files/ebook/';
				$config['allowed_types'] = 'pdf|doc|docx|epub|mobi';
				$config['max_size'] = '1048576'; // 1GB
				$config['encrypt_name'] = FALSE;
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('file_ebook')) {
					$upload_data = $this->upload->data();
					$fileName_ebook = $upload_data['file_name'];
					$ukuran_file = $this->formatBytes($upload_data['file_size']);
					$format_file = $upload_data['file_ext'];
				}
			}
			
			// Upload file preview
			if (isset($_FILES['preview_file']['name']) && !empty($_FILES['preview_file']['name'])) {
				$config['upload_path'] = 'asset/files/ebook/preview/';
				$config['allowed_types'] = 'pdf|jpg|jpeg|png';
				$config['max_size'] = '5120'; // 5MB
				$config['encrypt_name'] = FALSE;
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('preview_file')) {
					$upload_data = $this->upload->data();
					$fileName_preview = $upload_data['file_name'];
				}
			}
			
			// Update tabel rb_produk
			$data_produk = array(
				'id_kategori_produk'=>$this->input->post('a'),
				'id_kategori_produk_sub'=>$this->input->post('aa'),
				'nama_produk'=>$this->input->post('b'),
				'produk_seo'=>seo_title($this->input->post('b')),
				'satuan'=>$this->input->post('c'),
				'harga_beli'=>$this->input->post('d'),
				'harga_reseller'=>$this->input->post('e'),
				'harga_konsumen'=>$this->input->post('f'),
				'keterangan'=>$this->input->post('ff')
			);
			
			if (!empty($fileName)) {
				$data_produk['gambar'] = $fileName;
			}
			
			$where_produk = array('id_produk' => $this->input->post('id_produk'));
			$this->model_app->update('rb_produk', $data_produk, $where_produk);
			
			// Update tabel rb_produk_digital
			$data_digital = array(
				'nama_ebook'=>$this->input->post('b')
			);
			
			if (!empty($fileName_ebook)) {
				$data_digital['file_ebook'] = $fileName_ebook;
				$data_digital['format_file'] = str_replace('.', '', $format_file);
				$data_digital['ukuran_file'] = $ukuran_file;
			}
			
			if (!empty($fileName_preview)) {
				$data_digital['preview_file'] = $fileName_preview;
			}
			
			$where_digital = array('id_produk_digital' => $this->input->post('id_produk_digital'));
			$this->model_app->update('rb_produk_digital', $data_digital, $where_digital);
			
			echo "<script>window.alert('Produk digital berhasil diupdate!');
				  window.location=('".base_url()."reseller/produk_digital_saya')</script>";
		}else{
			$data['title'] = 'Edit Produk Digital';
			$data['kategori'] = $this->model_app->view_ordering('rb_kategori_produk','id_kategori_produk','DESC');
			$data['rows'] = $this->db->query("SELECT pd.*, p.* FROM rb_produk_digital pd 
												JOIN rb_produk p ON pd.id_produk = p.id_produk 
												WHERE pd.id_produk_digital='$id' AND p.id_reseller='".$this->session->id_reseller."'")->row_array();
			$this->template->load('reseller/template','reseller/mod_produk_digital/view_edit_produk_digital_saya',$data);
		}
	}

	function delete_produk_digital_saya(){
		cek_session_reseller();
		$id = $this->uri->segment(3);
		
		// Ambil data produk digital
		$produk = $this->db->query("SELECT pd.*, p.id_produk FROM rb_produk_digital pd 
									JOIN rb_produk p ON pd.id_produk = p.id_produk 
									WHERE pd.id_produk_digital='$id' AND p.id_reseller='".$this->session->id_reseller."'")->row_array();
		
		if (!empty($produk)) {
			// Hapus dari tabel rb_produk_digital
			$this->model_app->delete('rb_produk_digital', array('id_produk_digital' => $id));
			
			// Hapus dari tabel rb_produk
			$this->model_app->delete('rb_produk', array('id_produk' => $produk['id_produk']));
			
			echo "<script>window.alert('Produk digital berhasil dihapus!');
				  window.location=('".base_url()."reseller/produk_digital_saya')</script>";
		} else {
			echo "<script>window.alert('Produk digital tidak ditemukan!');
				  window.location=('".base_url()."reseller/produk_digital_saya')</script>";
		}
	}

	private function formatBytes($size, $precision = 2) {
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
		return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
	}

	function logout(){
		$this->session->sess_destroy();
		redirect('main');
	}
}
