<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Controller for handling log_sistem module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Log_sistem extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('log_sistem');
		//enable dibawah ini jika divalidasi semua
		//$this->auth->validate(TRUE);
		//enable dibawah ini jika divalidasi user telah login saja
		//$this->auth->validate(TRUE, TRUE);

		//load this page model
		$this->load->model('m_log_sistem');

		//load foregin lang if exist
		$this->lang->load('module/pegawai');


		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/log_sistem');
  	}

	/*
	 * method index controller log_sistem
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
		$this->auth->validate(TRUE, TRUE);

		//set informasi halaman
		$this->site_info->set_page_title($this->lang->line('module_title'));
		//set breadcrumb
		$this->site_info->add_breadcrumb($this->lang->line('module_title'));
		//add menu highlight
		$this->site_info->set_current_module('log');
		$this->site_info->set_current_submodule('log_sistem');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('js/pages/log_sistem.js');
		$this->asset_library->add_js('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
		$this->asset_library->add_css('plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css', 'core-style');

		$data = array();
		$data['keterangan'] = array("Sukses" => "Sukses", "Sukses - Data Kosong" => "Sukses - Data Kosong", "Gagal - ID Salah" => "Gagal - ID Salah", "Gagal - Koneksi Error" => "Gagal - Koneksi Error");

		$this->load->model("m_log_tarik");
		$data["periode"] = $this->m_log_tarik->get("tar_jenis = '" . TARIK_1 . "'", "tar_id desc");

		//load view
		$this->load->view('base/header');
		$this->load->view('log_sistem/master', $data);
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

		$filter_cols = array('log_keterangan' => $this->input->post('log_keterangan'));
		$add_where = '';

		if ($this->input->post('log_startdate') != '') {
			$startdate = date("Y-m-d", strtotime($this->input->post('log_startdate')));
			$enddate = date("Y-m-d", strtotime($this->input->post('log_enddate')));
			$add_where .= 'log_tanggal >= \'' . $startdate . '\'';
			$add_where .= ' AND log_tanggal <= \'' . $enddate . '\'';
		}
		//set default where query
		$where = build_masterpage_filter($filter_cols , $add_where );

		//get data
		$this->m_log_sistem->get_datatable($where);
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

		//search data with pk for default
		$search_data = $this->input->post('log_id');
		//if no search data exist, response error
		if($search_data === NULL)
			ajax_response('error');

		//get detail
		$detail = $this->m_log_sistem->get_by_column($search_data);

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
		$this->form_validation->set_rules('log_id'        , 'lang:log_id'        , 'integer');
		$this->form_validation->set_rules('log_tanggal'   , 'lang:log_tanggal'   , 'required|valid_datetime');
		$this->form_validation->set_rules('log_aktivitas' , 'lang:log_aktivitas' , 'trim|required|max_length[255]');
		$this->form_validation->set_rules('log_pegawai'      , 'lang:log_pegawai' , 'trim|required|max_length[255]');
		$this->form_validation->set_rules('log_data'      , 'lang:log_data'      , 'integer');
		$this->form_validation->set_rules('log_keterangan', 'lang:log_keterangan', 'trim|max_length[1000]');

		if ($this->form_validation->run())
		{
			// insert
			if($this->input->post('log_id') == '')
			{
				$log_id = $this->m_log_sistem->insert(
					$this->input->post('log_tanggal'),
					$this->input->post('log_aktivitas'),
					$this->input->post('log_pegawai'),
					$this->input->post('log_data'),
					$this->input->post('log_keterangan')
				);
				write_log('log_sistem', 'insert', "PK = $log_id");
				ajax_response();
			}
			// update
			else
			{
				$pk_id = $this->input->post('log_id');
				//check if pk integer
				//$pk_id = uintval($pk_id);
				$this->m_log_sistem->update(
					$pk_id,
					$this->input->post('log_tanggal'),
					$this->input->post('log_aktivitas'),
					$this->input->post('log_pegawai'),
					$this->input->post('log_data'),
					$this->input->post('log_keterangan')
				);
				write_log('log_sistem', 'update', "PK = $pk_id");
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

		if($this->input->post('log_id') === NULL) ajax_response();

		$all_deleted = array();
		foreach($this->input->post('log_id') as $row){
			//$row = uintval($row);
			//permanent delete row, check MY_Model, you can set flag with ->update_single_column
			$this->m_log_sistem->permanent_delete($row);

			//this is sample code if you cannot delete, but you must update status
			//$this->m_log_sistem->update_single_column('log_deleted', 1, $row);
			$all_deleted[] = $row;
		}
		write_log('log_sistem', 'delete', 'PK = ' . implode(",", $all_deleted));

		ajax_response();
	}

	public function download_sukses($tar_id){
		$this->auth->validate(TRUE, TRUE);
		$data = array();

		$data['result'] = $this->m_log_sistem->get_sukses_tarik_data($tar_id);
		
		$title = array("No", "Kode Jurusan", "Jurusan", "NIP", "Pengarang", "Link Google Scholar", "Jumlah Data");
		$type = array("number","number","string","string","string","string","number");
		$result = array();
		foreach ($data['result'] as $key => $value) {
			$row = array();
			$row[] = $key + 1;
			foreach ($value as $key2 => $value2) {
				// if($key2 == "peg_nip_baru")
				// 	$row[] = "'".$value2;
				// else
					$row[] = $value2;
			}
			$result[] = $row;
		}
		//var_dump($result);exit();
		$datenow = date("d-m-Y");
		$file_name = "Daftar Pengarang Sukses Unduh Data " . $datenow;
		
		download_excel($file_name, $title, $result,$type);
	}

	public function download_gagal_tarik($tar_id){
		$this->auth->validate(TRUE, TRUE);
		$data = array();

		$data['result'] = $this->m_log_sistem->get_gagal_tarik_data($tar_id);
		
		$title = array("No", "Kode Jurusan", "Jurusan", "NIP", "Pengarang", "Link Google Scholar");
		$type = array("number","number","string","string","string","string");
		$result = array();
		foreach ($data['result'] as $key => $value) {
			$row = array();
			$row[] = $key + 1;
			foreach ($value as $key2 => $value2) {
				// if($key2 == "peg_nip_baru")
				// 	$row[] = "'".$value2;
				// else
					$row[] = $value2;
			}
			$result[] = $row;
		}
		$datenow = date("d-m-Y");
		$file_name = "Daftar Pengarang Gagal Unduh Data " . $datenow;
		
		download_excel($file_name, $title, $result,$type);
	}

	public function download_kosong($tar_id){
		$this->auth->validate(TRUE, TRUE);
		$data = array();

		$data['result'] = $this->m_log_sistem->get_kosong_tarik_data($tar_id);
		
		$title = array("No", "Kode Jurusan", "Jurusan", "NIP", "Pengarang", "Link Google Scholar");
		$type = array("number","number","string","string","string","string");
		$result = array();
		foreach ($data['result'] as $key => $value) {
			$row = array();
			$row[] = $key + 1;
			foreach ($value as $key2 => $value2) {
				// if($key2 == "peg_nip_baru")
				// 	$row[] = "'".$value2;
				// else
					$row[] = $value2;
			}
			$result[] = $row;
		}
		$datenow = date("d-m-Y");
		$file_name = "Daftar Pengarang Tarik Unduh Kosong " . $datenow;
		
		download_excel($file_name, $title, $result,$type);
	}

	public function download_url_kosong(){
		$this->auth->validate(TRUE, TRUE);
		$data = array();
		$this->load->model("m_pegawai");
		$data['result'] = $this->m_pegawai->get_url_kosong();
		
		$title = array("No", "Kode Jurusan", "Jurusan", "NIP", "Pengarang", "Link Google Scholar");
		$type = array("number","number","string","string","string","string");
		$result = array();
		foreach ($data['result'] as $key => $value) {
			$row = array();
			$row[] = $key + 1;
			foreach ($value as $key2 => $value2) {
				// if($key2 == "peg_nip_baru")
				// 	$row[] = "'".$value2;
				// else
					$row[] = $value2;
			}
			$result[] = $row;
		}
		$datenow = date("d-m-Y");
		$file_name = "Daftar Pengarang URL Kosong " . $datenow;
		
		download_excel($file_name, $title, $result,$type);
	}
}