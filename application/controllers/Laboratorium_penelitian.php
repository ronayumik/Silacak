<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Controller for handling laboratorium_penelitian module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Laboratorium_penelitian extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('laboratorium_penelitian');
		//enable dibawah ini jika divalidasi semua
		//$this->auth->validate(TRUE);
		//enable dibawah ini jika divalidasi user telah login saja
		//$this->auth->validate(TRUE, TRUE);

		//load this page model
		$this->load->model('m_laboratorium_penelitian');

		//load foregin lang if exist


		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/laboratorium_penelitian');
  	}

	/*
	 * method index controller laboratorium_penelitian
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
		$this->site_info->set_current_module('laboratorium_penelitian');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('js/pages/laboratorium_penelitian.js');

		$data = array();


		//load view
		$this->load->view('base/header');
		$this->load->view('laboratorium_penelitian/master', $data);
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

		$filter_cols = array();
		//$add_where = '';
		//set default where query
		$where = build_masterpage_filter($filter_cols /*, $add_where */);

		//get data
		$this->m_laboratorium_penelitian->get_datatable($where);
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
		$search_data = $this->input->post('lab_id');
		//if no search data exist, response error
		if($search_data === NULL)
			ajax_response('error');

		//activate if search data is integer
		//$search_data = uintval($search_data);

		//get detail
		$detail = $this->m_laboratorium_penelitian->get_by_column($search_data);

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
		$this->form_validation->set_rules('lab_id'               , 'lang:lab_id'               , 'integer');
		$this->form_validation->set_rules('lab_kode'             , 'lang:lab_kode'             , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_perguruan_tinggi' , 'lang:lab_perguruan_tinggi' , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_fakultas'         , 'lang:lab_fakultas'         , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_jurusan'          , 'lang:lab_jurusan'          , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_program_studi'    , 'lang:lab_program_studi'    , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_validasi'         , 'lang:lab_validasi'         , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_log_audit'        , 'lang:lab_log_audit'        , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_nama_indonesia'   , 'lang:lab_nama_indonesia'   , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_nama_inggris'     , 'lang:lab_nama_inggris'     , 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_periode_pelaporan', 'lang:lab_periode_pelaporan', 'trim|max_length[255]');
		$this->form_validation->set_rules('lab_jumlah_aktifitas' , 'lang:lab_jumlah_aktifitas' , 'integer');
		$this->form_validation->set_rules('lab_created_at'       , 'lang:lab_created_at'       , 'required');
		$this->form_validation->set_rules('lab_updated_at'       , 'lang:lab_updated_at'       , 'required');
		$this->form_validation->set_rules('lab_deleted_at'       , 'lang:lab_deleted_at'       , 'required');

		if ($this->form_validation->run())
		{
			// insert
			if($this->input->post('lab_id') == '')
			{
				$lab_id = $this->m_laboratorium_penelitian->insert(
					$this->input->post('lab_kode'),
					$this->input->post('lab_perguruan_tinggi'),
					$this->input->post('lab_fakultas'),
					$this->input->post('lab_jurusan'),
					$this->input->post('lab_program_studi'),
					$this->input->post('lab_validasi'),
					$this->input->post('lab_log_audit'),
					$this->input->post('lab_nama_indonesia'),
					$this->input->post('lab_nama_inggris'),
					$this->input->post('lab_periode_pelaporan'),
					$this->input->post('lab_jumlah_aktifitas'),
					$this->input->post('lab_created_at'),
					$this->input->post('lab_updated_at'),
					$this->input->post('lab_deleted_at')
				);
				write_log('laboratorium_penelitian', 'insert', "PK = $lab_id");
				ajax_response();
			}
			// update
			else
			{
				$pk_id = $this->input->post('lab_id');
				//check if pk integer
				//$pk_id = uintval($pk_id);
				$this->m_laboratorium_penelitian->update(
					$pk_id,
					$this->input->post('lab_kode'),
					$this->input->post('lab_perguruan_tinggi'),
					$this->input->post('lab_fakultas'),
					$this->input->post('lab_jurusan'),
					$this->input->post('lab_program_studi'),
					$this->input->post('lab_validasi'),
					$this->input->post('lab_log_audit'),
					$this->input->post('lab_nama_indonesia'),
					$this->input->post('lab_nama_inggris'),
					$this->input->post('lab_periode_pelaporan'),
					$this->input->post('lab_jumlah_aktifitas'),
					$this->input->post('lab_created_at'),
					$this->input->post('lab_updated_at'),
					$this->input->post('lab_deleted_at')
				);
				write_log('laboratorium_penelitian', 'update', "PK = $pk_id");
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

		if($this->input->post('lab_id') === NULL) ajax_response();

		$all_deleted = array();
		foreach($this->input->post('lab_id') as $row){
			//$row = uintval($row);
			//permanent delete row, check MY_Model, you can set flag with ->update_single_column
			$this->m_laboratorium_penelitian->permanent_delete($row);

			//this is sample code if you cannot delete, but you must update status
			//$this->m_laboratorium_penelitian->update_single_column('lab_deleted', 1, $row);
			$all_deleted[] = $row;
		}
		write_log('laboratorium_penelitian', 'delete', 'PK = ' . implode(",", $all_deleted));

		ajax_response();
	}
}