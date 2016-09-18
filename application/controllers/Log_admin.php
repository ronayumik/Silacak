<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Controller for handling log_admin module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Log_admin extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('log_admin');
		//enable dibawah ini jika divalidasi semua
		//$this->auth->validate(TRUE);
		//enable dibawah ini jika divalidasi user telah login saja
		//$this->auth->validate(TRUE, TRUE);

		//load this page model
		$this->load->model('m_log_admin');

		//load foregin lang if exist
		$this->lang->load('module/pengguna');

		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/log_admin');
  	}

	/*
	 * method index controller log_admin
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
		$this->site_info->set_current_submodule('log_admin');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('js/pages/log_admin.js');

		$data = array();
		$this->load->model('m_pengguna');
		$data['pengguna'] = $this->m_pengguna->get(/*'usr_deleted = 0', 'usr_name asc'*/);

		//load view
		$this->load->view('base/header');
		$this->load->view('log_admin/master', $data);
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

		$filter_cols = array('adm_pengguna' => /*uintval(*/$this->input->post('adm_pengguna')/*, TRUE)*/);
		//$add_where = '';
		//set default where query
		$where = build_masterpage_filter($filter_cols /*, $add_where */);

		//get data
		$this->m_log_admin->get_datatable($where);
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
		$search_data = $this->input->post('adm_id');
		//if no search data exist, response error
		if($search_data === NULL)
			ajax_response('error');

		//activate if search data is integer
		//$search_data = uintval($search_data);

		//get detail
		$detail = $this->m_log_admin->get_by_column($search_data);

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
		$this->form_validation->set_rules('adm_id'        , 'lang:adm_id'        , 'integer');
		$this->form_validation->set_rules('adm_pengguna'  , 'lang:adm_pengguna'  , 'integer');
		$this->form_validation->set_rules('adm_tanggal'   , 'lang:adm_tanggal'   , 'valid_datetime');
		$this->form_validation->set_rules('adm_aktivitas' , 'lang:adm_aktivitas' , 'trim|max_length[100]');
		$this->form_validation->set_rules('adm_keterangan', 'lang:adm_keterangan', 'trim');

		if ($this->form_validation->run())
		{
			// insert
			if($this->input->post('adm_id') == '')
			{
				$adm_id = $this->m_log_admin->insert(
					$this->input->post('adm_pengguna'),
					$this->input->post('adm_tanggal'),
					$this->input->post('adm_aktivitas'),
					$this->input->post('adm_keterangan')
				);
				write_log('log_admin', 'insert', "PK = $adm_id");
				ajax_response();
			}
			// update
			else
			{
				$pk_id = $this->input->post('adm_id');
				//check if pk integer
				//$pk_id = uintval($pk_id);
				$this->m_log_admin->update(
					$pk_id,
					$this->input->post('adm_pengguna'),
					$this->input->post('adm_tanggal'),
					$this->input->post('adm_aktivitas'),
					$this->input->post('adm_keterangan')
				);
				write_log('log_admin', 'update', "PK = $pk_id");
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

		if($this->input->post('adm_id') === NULL) ajax_response();

		$all_deleted = array();
		foreach($this->input->post('adm_id') as $row){
			//$row = uintval($row);
			//permanent delete row, check MY_Model, you can set flag with ->update_single_column
			$this->m_log_admin->permanent_delete($row);

			//this is sample code if you cannot delete, but you must update status
			//$this->m_log_admin->update_single_column('adm_deleted', 1, $row);
			$all_deleted[] = $row;
		}
		write_log('log_admin', 'delete', 'PK = ' . implode(",", $all_deleted));

		ajax_response();
	}
}