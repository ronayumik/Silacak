<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Controller for handling pengguna module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Pengguna extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('pengguna');
		//enable dibawah ini jika divalidasi semua
		//$this->auth->validate(TRUE);
		//enable dibawah ini jika divalidasi user telah login saja
		//$this->auth->validate(TRUE, TRUE);

		//load this page model
		$this->load->model('m_pengguna');

		//load foregin lang if exist


		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/pengguna');
  	}

	/*
	 * method index controller pengguna
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
		$this->site_info->set_page_title($this->lang->line('module_title'), $this->lang->line('module_subtitle'));
		//set breadcrumb
		$this->site_info->add_breadcrumb($this->lang->line('module_title'));
		//add menu highlight
		$this->site_info->set_current_module('pengguna');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('js/pages/pengguna.js');

		$data = array();

		//load view
		$this->load->view('base/header');
		$this->load->view('pengguna/master', $data);
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
		$where = build_masterpage_filter($filter_cols, "usr_deleted_at is null" /*, $add_where */);

		//get data
		$this->m_pengguna->get_datatable($where);
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
		$search_data = $this->input->post('usr_id');
		//if no search data exist, response error
		if($search_data === NULL)
			ajax_response('error');

		//activate if search data is integer
		//$search_data = uintval($search_data);

		//get detail
		$detail = $this->m_pengguna->get_by_column($search_data);

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
		$this->form_validation->set_rules('usr_id'          , 'lang:usr_id'          , 'integer');
		$this->form_validation->set_rules('usr_username'    , 'lang:usr_username'    , 'trim|max_length[60]');
		$this->form_validation->set_rules('usr_password'    , 'lang:usr_password'    , 'trim|max_length[255]');
		$this->form_validation->set_rules('usr_email'       , 'lang:usr_email'       , 'trim|max_length[255]');
		$this->form_validation->set_rules('usr_name'        , 'lang:usr_name'        , 'trim|max_length[255]');
		$this->form_validation->set_rules('usr_fullname'    , 'lang:usr_fullname'    , 'trim|max_length[255]');

		if ($this->form_validation->run())
		{
			// insert
			if($this->input->post('usr_id') == '')
			{
				$usr_id = $this->m_pengguna->insert(
					$this->input->post('usr_username'),
					md5($this->input->post('usr_password')),
					$this->input->post('usr_email'),
					$this->input->post('usr_name'),
					$this->input->post('usr_fullname'),
					false,
					false,
					"1",
					$this->input->post('usr_issa') ? 1 : 0,
					false,
					date("Y-m-d"),
					false
				);
				//write_log('pengguna', 'insert', "PK = $usr_id");
				ajax_response();
			}
			// update
			else
			{
				$pk_id = $this->input->post('usr_id');
				//check if pk integer
				//$pk_id = uintval($pk_id);
				$this->m_pengguna->update(
					$pk_id,
					$this->input->post('usr_username'),
					false,
					$this->input->post('usr_email'),
					$this->input->post('usr_name'),
					$this->input->post('usr_fullname'),
					false,
					false,
					false,
					$this->input->post('usr_issa') ? 1 : 0,
					false,
					false,
					false
				);
				//write_log('pengguna', 'update', "PK = $pk_id");
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

		if($this->input->post('usr_id') === NULL) ajax_response();

		$all_deleted = array();
		foreach($this->input->post('usr_id') as $row){
			//$row = uintval($row);
			//permanent delete row, check MY_Model, you can set flag with ->update_single_column
			//$this->m_pengguna->permanent_delete($row);
			$this->m_pengguna->update_single_column('usr_deleted_at', date("Y-m-d"), $row);

			//this is sample code if you cannot delete, but you must update status
			//$this->m_pengguna->update_single_column('usr_deleted', 1, $row);
			$all_deleted[] = $row;
		}
		//write_log('pengguna', 'delete', 'PK = ' . implode(",", $all_deleted));

		ajax_response();
	}
}