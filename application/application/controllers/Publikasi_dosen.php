<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Controller for handling publikasi_dosen module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Publikasi_dosen extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('publikasi_dosen');
		//enable dibawah ini jika divalidasi semua
		//$this->auth->validate(TRUE);
		//enable dibawah ini jika divalidasi user telah login saja
		//$this->auth->validate(TRUE, TRUE);

		//load this page model
		$this->load->model('m_publikasi_dosen');
		$this->load->model('m_anggota');
		$this->load->model('m_pegawai');
		$this->load->model('m_citations');

		//load foregin lang if exist
		$this->lang->load('module/detil_kode_publikasi');
		$this->lang->load('module/anggota');
		$this->lang->load('module/pegawai');
		$this->lang->load('module/citation');

		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/publikasi_dosen');
  	}

	/*
	 * method index controller publikasi_dosen
     * generated by Doni's Framework Generator
	 *
	 * this method act as module entry point
	 *
	 * @author: Doni Setio Pambudi
	 * @access: public
	 * @return: no return, view a page
	 */
	public function index(){
		//$this->auth->set_access('view');
		//$this->auth->validate(TRUE);

		//set informasi halaman
		$this->site_info->set_page_title($this->lang->line('module_title'), $this->lang->line('module_subtitle'));
		//set breadcrumb
		$this->site_info->add_breadcrumb($this->lang->line('module_title'));
		//add menu highlight
		$this->site_info->set_current_module('publikasi_dosen');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('js/pages/publikasi_dosen.js');
		$this->asset_library->add_js('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
		$this->asset_library->add_js('plugins/jquery-sortable.js');
		$this->asset_library->add_css('plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css', 'core-style');
        $pegawai = -1;
        $pegawai_name = "";
        $status_tarik = -1;
        if($this->input->get("pegawai") != null){
        	$pegawai = $this->input->get("pegawai");
        	$pegawai_name = $this->m_pegawai->get_by_column($pegawai)->peg_name;

        }

        if($this->input->get("status_tarik") != null){
        	$status_tarik = $this->input->get("status_tarik");
        }

		$data = array();
		$this->load->model('m_detil_kode_publikasi');
		$data['detil_kode_publikasi'] = $this->m_detil_kode_publikasi->get('', 'dkp_keterangan asc');
		$data["pegawai"] = $pegawai;
		$data["pegawai_name"] = $pegawai_name;
		$data["status_tarik"] = $status_tarik;
		//load view
		$this->load->view('base/header');
		$this->load->view('publikasi_dosen/master', $data);
		$this->load->view('base/footer');
	}

	/*
	 * get_datamaster method
     * generated by Doni's Framework Generator
	 *
	 * this method handle get datamaster request from datatables
	 *
	 * @author	Doni Setio Pambudi
	 * @access	public
	 * @return	json string
	 */
	public function get_datamaster(){
		//only ajax is allowed
		if(!$this->input->is_ajax_request()) show_404();

		//$this->auth->set_access('view');
		//$this->auth->validate();

		$filter_cols = array('pub_detilkodepub' => /*uintval(*/$this->input->post('pub_detilkodepub'), 'pub_tahun' => $this->input->post('pub_tahun'), 'pub_status_tarik' => $this->input->post('pub_status_tarik')/*, TRUE)*/);
		//$add_where = '';
		//set default where query
		if ($this->input->post('pub_pegawai') != '')
		{
			$add_where = 'ang_pegawai = ' .$this->input->post('pub_pegawai');
			$unfiltered = 2;
		}
		else {
			$add_where = '';
			$unfiltered = 0;
		}
			
		$where = build_masterpage_filter($filter_cols, $add_where);

		//get data
		$this->m_publikasi_dosen->get_datatable($unfiltered, $where);
	}

	/*
	 * detail method
     * generated by Doni's Framework Generator
	 *
	 * this method handle get data detail from one primary key
	 *
	 * @author	Doni Setio Pambudi
	 * @access	public
	 * @return		json string
	 */
	public function detail(){
		//only ajax is allowed
		if(!$this->input->is_ajax_request()) show_404();

		//$this->auth->set_access('view');
		//$this->auth->validate();

		//search data with pk for default
		$search_data = $this->input->post('pub_id');
		//if no search data exist, response error
		if($search_data === NULL)
			ajax_response('error');

		//activate if search data is integer
		//$search_data = uintval($search_data);

		//get detail
		$detail = $this->m_publikasi_dosen->get_by_column($search_data);
		$detail->anggota = $this->m_anggota->get("ang_publikasi = $detail->pub_id");
		$detail->citation = $this->m_citations->get("cit_publikasi = $detail->pub_id", "cit_tahun asc");

		//output return value
		if($detail !== NULL) ajax_response('ok', '', $detail);
		else ajax_response('error');
	}

	/*
	 * add method
     * generated by Doni's Framework Generator
	 *
	 * this method handle add data submit from master framework
	 *
	 * @author	Doni Setio Pambudi
	 * @access	public
	 * @return		json string result ok or error
	 */
	public function add(){
		//$this->auth->set_access('add');
		//$this->auth->validate();

		//call save method
		$this->save();
	}

	/*
	 * edit method
     * generated by Doni's Framework Generator
	 *
	 * this method handle edit data submit from master framework
	 *
	 * @author	Doni Setio Pambudi
	 * @access	public
	 * @return		json string result ok or error
	 */
	public function edit(){
		//$this->auth->set_access('edit');
		//$this->auth->validate();

		//call save method
		$this->save();
	}

	private function updateAnggotaPublikasi($anggota, $pub_id)
	{
		foreach ($anggota as $value) {
			if($value->id == -1)
			{
				//insert new anggota
				$pk_id = $this->m_anggota->insert($value->peg_id,
										$pub_id,
										$value->ang_sebagai);
				write_log('anggota_publikasi', 'insert', "PK = $pk_id");
			}
			else
			{
				//update anggota
				$this->m_anggota->update($value->id,
										$value->peg_id,
										false,
										$value->ang_sebagai);
				write_log('anggota_publikasi', 'update', "PK = $value->id");
			}
		}
	}

	/*
	 * save method
     * generated by Doni's Framework Generator
	 *
	 * private method handle add and edit method
	 *
	 * @author	Doni Setio Pambudi
	 * @access	private
	 * @return		json string result ok or error
	 */
	private function save(){
		if(!$this->input->is_ajax_request()) show_404();

		//validation
		$this->load->library('form_validation');
		$this->load->library('framework_validation');
		$this->form_validation->set_rules('pub_id'                    , 'lang:pub_id'                    , 'integer');
		$this->form_validation->set_rules('pub_detilkodepub'          , 'lang:pub_detilkodepub'          , 'integer');
		$this->form_validation->set_rules('pub_kode'                  , 'lang:pub_kode'                  , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_url_scholar'         , 'lang:pub_url_scholar'         , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_jenis_peneliti'        , 'lang:pub_jenis_peneliti'        , 'trim|max_length[1]');
		$this->form_validation->set_rules('pub_media_publikasi'       , 'lang:pub_media_publikasi'       , 'trim|max_length[1]');
		$this->form_validation->set_rules('pub_pelaksanaan_penelitian', 'lang:pub_pelaksanaan_penelitian', 'trim|max_length[1]');
		$this->form_validation->set_rules('pub_jenis_pembiayaan'      , 'lang:pub_jenis_pembiayaan'      , 'trim|max_length[1]');
		$this->form_validation->set_rules('pub_status_validasi'       , 'lang:pub_status_validasi'       , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_periode_pelaporan'     , 'lang:pub_periode_pelaporan'     , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_kode_pegawai'          , 'lang:pub_kode_pegawai'          , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_jumlah_pembiayaan'     , 'lang:pub_jumlah_pembiayaan'     , 'numeric');
		$this->form_validation->set_rules('pub_tahun'                 , 'lang:pub_tahun'                 , 'integer');
		$this->form_validation->set_rules('pub_bulan'                 , 'lang:pub_bulan'                 , 'integer');
		$this->form_validation->set_rules('pub_judul'                 , 'lang:pub_judul'                 , 'trim');
		$this->form_validation->set_rules('pub_kata_kunci'            , 'lang:pub_kata_kunci'            , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_total_waktu'           , 'lang:pub_total_waktu'           , 'integer');
		$this->form_validation->set_rules('pub_lokasi'                , 'lang:pub_lokasi'                , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_abstraksi'             , 'lang:pub_abstraksi'             , 'trim');
		$this->form_validation->set_rules('pub_pengarang'             , 'lang:pub_pengarang'             , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_keterangan'            , 'lang:pub_keterangan'            , 'trim');
		$this->form_validation->set_rules('pub_tanggal_mulai'         , 'lang:pub_tanggal_mulai'         , array(array($this->framework_validation, 'valid_date')));
		$this->form_validation->set_rules('pub_tanggal_selesai'       , 'lang:pub_tanggal_selesai'       , array(array($this->framework_validation, 'valid_date')));
		$this->form_validation->set_rules('pub_url_unduh'             , 'lang:pub_url_unduh'             , 'trim|max_length[255]');
		$this->form_validation->set_rules('pub_duplicate'             , 'lang:pub_duplicate'             , 'integer');
		$this->form_validation->set_rules('pub_citation'            , 'lang:pub_citation'            , 'integer');
		// $this->form_validation->set_rules('pub_created_at'            , 'lang:pub_created_at'            , array(array($this->framework_validation, 'valid_date')));
		// $this->form_validation->set_rules('pub_updated_at'            , 'lang:pub_updated_at'            , array(array($this->framework_validation, 'valid_date')));
		// $this->form_validation->set_rules('pub_deleted_at'            , 'lang:pub_deleted_at'            , array(array($this->framework_validation, 'valid_date')));

		if ($this->form_validation->run())
		{
			//get daftar anggota publikasi
			$anggota = json_decode($this->input->post("anggota_publikasi"));


			// insert
			if($this->input->post('pub_id') == '')
			{
				$pub_id = $this->m_publikasi_dosen->insert(
					$this->input->post('pub_detilkodepub'),
					$this->input->post('pub_kode'),
					$this->input->post('pub_url_scholar'),
					$this->input->post('pub_jenis_peneliti'),
					$this->input->post('pub_media_publikasi'),
					$this->input->post('pub_pelaksanaan_penelitian'),
					$this->input->post('pub_jenis_pembiayaan'),
					$this->input->post('pub_status_validasi'),
					$this->input->post('pub_periode_pelaporan'),
					$this->input->post('pub_kode_pegawai'),
					$this->input->post('pub_jumlah_pembiayaan'),
					$this->input->post('pub_tahun'),
					$this->input->post('pub_bulan'),
					$this->input->post('pub_judul'),
					$this->input->post('pub_kata_kunci'),
					$this->input->post('pub_total_waktu'),
					$this->input->post('pub_lokasi'),
					$this->input->post('pub_abstraksi'),
					$this->input->post('pub_pengarang'),
					$this->input->post('pub_volume'),
					$this->input->post('pub_halaman'),
					$this->input->post('pub_issue'),
					$this->input->post('pub_keterangan'),
					date("Y-m-d", strtotime($this->input->post('pub_tanggal_mulai'))),
					date("Y-m-d", strtotime($this->input->post('pub_tanggal_selesai'))),
					$this->input->post('pub_url_unduh'),
					$this->input->post('pub_duplicate'),
					$this->input->post('pub_citation'),
					date("Y-m-d"),
					false,
					false
				);
				write_log('publikasi_dosen', 'insert', "PK = $pub_id");
				//$this->updateAnggotaPublikasi($anggota, $pub_id);
				
				ajax_response();
			}
			// update
			else
			{
				$pk_id = $this->input->post('pub_id');
				//check if pk integer
				//$pk_id = uintval($pk_id);
				$this->m_publikasi_dosen->update(
					$pk_id,
					$this->input->post('pub_detilkodepub'),
					$this->input->post('pub_kode'),
					$this->input->post('pub_url_scholar'),
					$this->input->post('pub_jenis_peneliti'),
					$this->input->post('pub_media_publikasi'),
					$this->input->post('pub_pelaksanaan_penelitian'),
					$this->input->post('pub_jenis_pembiayaan'),
					$this->input->post('pub_status_validasi'),
					$this->input->post('pub_periode_pelaporan'),
					$this->input->post('pub_kode_pegawai'),
					$this->input->post('pub_jumlah_pembiayaan'),
					$this->input->post('pub_tahun'),
					$this->input->post('pub_bulan'),
					$this->input->post('pub_judul'),
					$this->input->post('pub_kata_kunci'),
					$this->input->post('pub_total_waktu'),
					$this->input->post('pub_lokasi'),
					$this->input->post('pub_abstraksi'),
					$this->input->post('pub_pengarang'),
					$this->input->post('pub_volume'),
					$this->input->post('pub_halaman'),
					$this->input->post('pub_issue'),
					$this->input->post('pub_keterangan'),
					date("Y-m-d", strtotime($this->input->post('pub_tanggal_mulai'))),
					date("Y-m-d", strtotime($this->input->post('pub_tanggal_selesai'))),
					$this->input->post('pub_url_unduh'),
					$this->input->post('pub_duplicate'),
					$this->input->post('pub_citation'),
					false,
					date("Y-m-d"),
					false
				);
				write_log('publikasi_dosen', 'update', "PK = $pk_id");
				//$this->updateAnggotaPublikasi($anggota, $pk_id);
				ajax_response();
			}
		}
		else
		{ ajax_response('error', validation_errors()); }
	}

	/*
	 * delete method
     * generated by Doni's Framework Generator
	 *
	 * method handle delete from master framework
	 *
	 * @author	Doni Setio Pambudi
	 * @access	public
	 * @return		json string result ok or error
	 */
	public function delete(){
		if(!$this->input->is_ajax_request()) show_404();

		//$this->auth->set_access('delete');
		//$this->auth->validate();

		if($this->input->post('pub_id') === NULL) ajax_response();

		$all_deleted = array();
		foreach($this->input->post('pub_id') as $row){
			//$row = uintval($row);
			//permanent delete row, check MY_Model, you can set flag with ->update_single_column
			$this->m_publikasi_dosen->permanent_delete($row);

			//this is sample code if you cannot delete, but you must update status
			//$this->m_publikasi_dosen->update_single_column('pub_deleted', 1, $row);
			$all_deleted[] = $row;
		}
		write_log('publikasi_dosen', 'delete', 'PK = ' . implode(",", $all_deleted));
		ajax_response();
	}

	public function delete_anggota(){
		$ang_id = $this->input->post("ang_id");
		if($ang_id == null)
			ajax_response('error', 'Data tidak ditemukan.');

		$this->m_anggota->permanent_delete($ang_id);
		ajax_response();
	}

	public function pull_data()
	{
		
		$this->load->model('m_pelacakansetting');
		$this->load->model('m_pegawai');

		$counter = 1;
		$url_front = "http://scholar.google.co.id/citations?hl=en&oe=ASCII&user=";
		$pegs = $this->m_pegawai->get("peg_google_schoolar != '' and peg_status_tarik != 1 and peg_status_aktivitas_pegawai in ('A','DP','TB','TI','DK','DT','MP') and peg_is_dosen=1 and peg_ikatan_kerja_pegawai in (1,2,3)", 'peg_id', '30');
		if(count($pegs) == 0) {
			$this->m_pegawai->reset_status();
			$num = 0;
			$this->load->model('m_log_tarik');
			$num = $this->m_log_tarik->count_total("tar_jenis = '".TARIK_1."' and tar_status = 'Selesai'")+1;
			$this->m_log_tarik->insert(TARIK_1, now(), "ke-".$num, "Selesai");
			$pegs = $this->m_pegawai->get("peg_google_schoolar != '' and peg_status_tarik != 1 and peg_status_aktivitas_pegawai in ('A','DP','TB','TI','DK','DT','MP') and peg_is_dosen=1 and peg_ikatan_kerja_pegawai in (1,2,3)", 'peg_id', '30');
		}
		
		foreach ($pegs as $peg) {
			$url_scholar = $peg->peg_google_schoolar;
			$url_scholar = explode("user=", $url_scholar);
			$peg_id = $peg->peg_id;
			if(count($url_scholar) > 1) {
				$url_scholar = $url_scholar[1];
				$id_url = explode("&", $url_scholar)[0];
				$num_pub=0;
				$nextstart = 0;
				do{
					$count = 1;
					$url_end = "&cstart=".$nextstart."&pagesize=100";
					echo $url_front.$id_url.$url_end."<br/>";
					$temp = $this->htmldomhelper->file_get_html($url_front.$id_url.$url_end);
					$counter++;
					foreach($temp->find('tr[class*=gsc_a_tr]') as $j){
						if($j->children(0)->find('a[class*=gsc_a_at]',0)){
							$title = $j->children(0)->find('a[class*=gsc_a_at]',0)->plaintext;
							$writer = $j->children(0)->children(1)->plaintext;
							$desc = $j->children(0)->children(2)->plaintext;
							$citation = $j->children(1)->plaintext;
							$year = $j->children(2)->plaintext;
							$url = $j->children(0)->find('a[class*=gsc_a_at]',0)->href;

							$url = str_replace('amp;', '', $url);
							if (strpos($url,'scholar') === false) {
							    $url = "http://scholar.google.co.id".$url;
							}

							if($title != '' && $writer!= '' && $desc!='' && $year!='' ) $validasi = 1;
							else $validasi = 0;

							$exist_pub = $this->m_publikasi_dosen->get("pub_judul like '".$title."'");
							$dkp_id = false;
							if( strpos($desc, 'journal') !== false || strpos($desc, 'jurnal') !== false || preg_match("/([0-9]-[0-9])/", $desc)) $dkp_id = KODE_JURNAL;
							else if( strpos($desc, 'conf') !== false || strpos($desc, 'konf') !== false || preg_match("/(19[5-9][0-9]|20([0-9][0-9]))/", $desc)) $dkp_id = KODE_SEMINAR;

							if(count($exist_pub)>0){
								$pub_id = $exist_pub[0]->pub_id;
								$this->m_publikasi_dosen->update($pub_id, $dkp_id, false, $url, false, false, false, false, false, false, false, false, $year, false, $title,
							false, false, false, false, false, false, false, false, false, false, false, $url, 0, preg_replace('/\s+/', '', str_replace(' ','',$citation)), now(), false );
							}else{
								$pub_id = $this->m_publikasi_dosen->insert($dkp_id, false, $url, false, false, false, false, $validasi, false, false, false, $year, false, $title,
							false, false, false, false, $writer, false, false, false, $desc, false, false, $url, 0, preg_replace('/\s+/', '', str_replace(' ','',$citation)), now(), now() );
							}
							$this->load->model('m_anggota');
							
							$exist_ang = $this->m_anggota->get("ang_pegawai = ".$peg_id." AND ang_publikasi = ".$pub_id);
							if(count($exist_ang)==0){
								$num_writer = $this->check_writer($writer,$peg->peg_name);
								$this->m_anggota->insert($peg_id,$pub_id,"".$num_writer+1);
							}

							$count++;
							$num_pub++;
						}
					}
					$nextstart+=100;
				} while($count>=100);
				$this->load->model('m_log_sistem');
				if($num_pub > 0){
					$this->m_log_sistem->insert(date("Y-m-d"), 'Tarik Data', $peg_id, $num_pub, 'Sukses');
				}
				else{
					$this->m_log_sistem->insert(date("Y-m-d"), 'Tarik Data', $peg_id, $num_pub, 'Sukses - Data Kosong');
				}
				
				
			}
			else{
				$this->load->model('m_log_sistem');
				$this->m_log_sistem->insert(date("Y-m-d"), 'Tarik Data', $peg->peg_id, 0, 'Gagal - ID Salah');
			}
			$this->m_pegawai->update_status($peg->peg_id, 1);
			if($counter > 30){
				break;	
			}
		}
		
	}

	public function pull_awal($peg){
		$url_front = "http://scholar.google.co.id/citations?hl=en&oe=ASCII&user=";
		$url_scholar = $peg->peg_google_schoolar;
		$url_scholar = explode("user=", $url_scholar);
		$peg_id = $peg->peg_id;
		if(count($url_scholar) > 1) {
			$url_scholar = $url_scholar[1];
			$id_url = explode("&", $url_scholar)[0];
			$num_pub=0;
			$nextstart = 0;
			do{
				$count = 1;
				$url_end = "&cstart=".$nextstart."&pagesize=100";
				
				$temp = $this->htmldomhelper->file_get_html($url_front.$id_url.$url_end);
				foreach($temp->find('tr[class*=gsc_a_tr]') as $j){
					if($j->children(0)->find('a[class*=gsc_a_at]',0)){
						$title = $j->children(0)->find('a[class*=gsc_a_at]',0)->plaintext;
						$writer = $j->children(0)->children(1)->plaintext;
						$desc = $j->children(0)->children(2)->plaintext;
						$citation = $j->children(1)->plaintext;
						$year = $j->children(2)->plaintext;
						$url = $j->children(0)->find('a[class*=gsc_a_at]',0)->href;

						$url = str_replace('amp;', '', $url);
						if (strpos($url,'scholar') === false) {
						    $url = "http://scholar.google.co.id".$url;
						}

						if($title != '' && $writer!= '' && $desc!='' && $year!='' ) $validasi = 1;
						else $validasi = 0;

						$exist_pub = $this->m_publikasi_dosen->get("pub_judul like '".$title."'");
						$dkp_id = false;
						if( strpos($desc, 'journal') !== false || strpos($desc, 'jurnal') !== false || preg_match("/([0-9]-[0-9])/", $desc)) $dkp_id = KODE_JURNAL;
						else if( strpos($desc, 'conf') !== false || strpos($desc, 'konf') !== false || preg_match("/(19[5-9][0-9]|20([0-9][0-9]))/", $desc)) $dkp_id = KODE_SEMINAR;

						if(count($exist_pub)>0){
							$pub_id = $exist_pub[0]->pub_id;
							$this->m_publikasi_dosen->update($pub_id, $dkp_id, false, $url, false, false, false, false, false, false, false, false, $year, false, $title,
							false, false, false, false, false, false, false, false, false, false, false, $url, 0, preg_replace('/\s+/', '', str_replace(' ','',$citation)), now(), false );
						}else{
							$pub_id = $this->m_publikasi_dosen->insert($dkp_id, false, $url, false, false, false, false, $validasi, false, false, false, $year, false, $title,
						false, false, false, false, $writer, false, false, false, $desc, false, false, $url, 0, preg_replace('/\s+/', '', str_replace(' ','',$citation)), now(), now() );
						}
						$this->load->model('m_anggota');
						
						$exist_ang = $this->m_anggota->get("ang_pegawai = ".$peg_id." AND ang_publikasi = ".$pub_id);
						if(count($exist_ang)==0){
							$num_writer = $this->check_writer($writer,$peg->peg_name);
							$this->m_anggota->insert($peg_id,$pub_id,"".$num_writer+1);
						}

						$count++;
						$num_pub++;
					}
				}
				$nextstart+=100;
			} while($count>=100);
			$this->load->model('m_log_sistem');
			if($num_pub > 0){
				$this->m_log_sistem->insert(date("Y-m-d"), 'Tarik Data', $peg_id, $num_pub, 'Sukses');
			}
			else{
				$this->m_log_sistem->insert(date("Y-m-d"), 'Tarik Data', $peg_id, $num_pub, 'Sukses - Data Kosong');
			}
			
			
		}
		else{
			$this->load->model('m_log_sistem');
			$this->m_log_sistem->insert(date("Y-m-d"), 'Tarik Data', $peg->peg_id, 0, 'Gagal - ID Salah');
		}
		$this->m_pegawai->update_status($peg->peg_id, 1);

	}

	public function tarik_data_pegawai(){
		$this->load->model('m_pelacakansetting');
		$this->load->model('m_pegawai');
		$pub_tahun = $this->input->post('filter_tahun');
		$peg_id = $this->input->post('peg_id');
		
		$peg = $this->m_pegawai->get_by_column($peg_id);
		$this->pull_awal($peg);
		
		$pubs = $this->m_publikasi_dosen->get_by_pegawai("ang_pegawai = ".$peg_id." AND pub_tahun = ".$pub_tahun, 'pub_tahun desc');

		foreach ($pubs as $pub) {
			# code...
			$this->pull_detail($pub->pub_id);
			$this->m_publikasi_dosen->update_status($pub->pub_id, 1);
		}
		$this->load->model("m_log_admin");
		$pegawai = $this->m_pegawai->get_by_column($peg_id);
		$this->m_log_admin->insert(1, now(), 'Tarik Data Pengarang', $pegawai->peg_name);
					
		ajax_response('ok');

		
	}

	public function pull_data2(){
		$this->load->model('m_pelacakansetting');
		$this->load->model('m_pegawai');

		$pubs = $this->m_publikasi_dosen->get("pub_status_tarik IS NULL OR pub_status_tarik != 1", 'pub_tahun desc', '30');
		if(count($pubs) == 0) {
			$this->m_publikasi_dosen->reset_status();
			$pubs = $this->m_publikasi_dosen->get("pub_status_tarik iS NULL OR pub_status_tarik != 1", 'pub_tahun desc', '30');
		}
		foreach ($pubs as $pub) {
			# code...
			$this->pull_detail($pub->pub_id);
			$this->m_publikasi_dosen->update_status($pub->pub_id, 1);
		}
		
	}

	public function pull_detail($pub_id = 0){
		$pub = $this->m_publikasi_dosen->get_by_column($pub_id);
		if($pub){
			$url = $pub->pub_url_scholar;
			$temp = $this->htmldomhelper->file_get_html($url);
			$data = array();
			$title = $temp->find('div[id*=gsc_title]',0)->plaintext;
			$pub_url_unduh = false;
			$pub_keterangan = "";
			$pub_abstraksi = false;
			$pub_pengarang = false;
			$pub_year = false;
			$pub_month = false;
			$pub_halaman = false;
			$pub_volume = false;
			$pub_issue = false;
			$date = array();
			$dkp_id = false;

			foreach($temp->find('div[id*=gsc_table]',0)->children() as $j){
				$field = $j->find('div[class*=gsc_field]',0)->plaintext;
				$value = $j->find('div[class*=gsc_value]',0)->plaintext;
				switch ($field) {
				    case 'Scholar articles':
				        $value = $j->find('a',0)->href;
				        $pub_url_unduh = $value;
				        break;
				    case 'Journal':
				        $dkp_id = KODE_JURNAL;
				        $pub_keterangan .= ' '.$value;
				        break;
				    case 'Volume':
				        $pub_volume = $value;
				        break;
				    case 'Description':
				        $pub_abstraksi = $value;
				        break;
				    case 'Publication date':
				        $date = explode('/', $value);
				        if(count($date)>0)
				        	$pub_year = $date[0];
				        if(count($date)>1)
				        	$pub_month = $date[1];
				        if(count($date)>2)
				        	$pub_date = $date[2];
				        break;
				    case 'Conference':
				        $dkp_id = KODE_SEMINAR;
				        $pub_keterangan = $value;
				        break;
				    case 'Pages':
				        $pub_halaman = $value;
				        break;
				    case 'Authors':
				        $pub_pengarang = $value;
				        break;
				    case 'Issue':
				        $pub_issue = $value;
				        break;
				    case 'Total citations':
				        $yearcit = $j->find('span[class*=gsc_g_t]');
				        $numcit = $j->find('span[class*=gsc_g_al]');
				        foreach ($yearcit as $key => $value) {
				        	$this->m_citations->insert($pub_id, $value->plaintext, $numcit[$key]->plaintext);
				        }
				        break;
				    
				}
			}
			$pub_id = $this->m_publikasi_dosen->update($pub_id, $dkp_id, false, false, false, false, false, false, false, false, false, false, $pub_year, $pub_month, false,
								false, false, false, $pub_abstraksi, $pub_pengarang, $pub_volume, $pub_halaman, $pub_issue, $pub_keterangan, false, false, $pub_url_unduh, 0, false, false, now() );

		}
		
	}
	public function check_writer($writer, $peg_name)
	{
		$writers = explode(",", $writer);
		$result = check_similarity($peg_name, $writers);
		return $result;
	}

	public function check_desc($desc)
	{
		if( strpos($desc, 'journal') !== false || strpos($desc, 'jurnal') !== false ) echo "jurnal";
		else if( strpos($desc, 'journal') !== false || strpos($desc, 'jurnal') !== false ) echo "seminar";
		else if(preg_match("/(19[5-9][0-9]|20([0-9][0-9]))/", $desc)) echo "seminar";
		else if(preg_match("/([0-9]-[0-9])/", $desc)) echo "jurnal";
		else echo "nothing";
	}

	public function reset(){
		$this->m_publikasi_dosen->reset_status();
	}
	
	
}