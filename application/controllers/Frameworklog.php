<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Controller for handling frameworklog module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Frameworklog extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('frameworklog');
		$this->auth->validate(true);

		//load this page model
		$this->load->model('m_frameworklog');

		//load foregin lang if exist


		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/frameworklog');
  	}

	/*
	 * method index controller frameworklog
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
		//$this->auth->validate(true);

		//set informasi halaman
		$this->site_info->set_page_title($this->lang->line('module_title'), $this->lang->line('module_subtitle'));
		//set breadcrumb
		$this->site_info->add_breadcrumb($this->lang->line('module_title'));

		$this->site_info->set_current_module('frameworklog');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('js/pages/frameworklog.js');

		$data = array();


		//load view
		$this->load->view('base/header');
		$this->load->view('frameworklog/master', $data);
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
		$this->m_frameworklog->get_datatable($where);
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
		$search_data = $this->input->post('flg_id');
		//if no search data exist, response error
		if($search_data === false)
			ajax_response('error');

		//activate if search data is integer
		//$search_data = intval($search_data);

		//get detail
		$detail = $this->m_frameworklog->get_by_column($search_data);

		//output return value
		if($detail !== null) ajax_response('ok', '', $detail);
		else ajax_response('error');
	}
}