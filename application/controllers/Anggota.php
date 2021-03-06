<?php defined('BASEPATH') OR exit('No direct script access allowed');
   
/*
 * Controller for handling anggota module
 * @author : Doni Setio Pambudi (donisp06@gmail.com)
 */
class Anggota extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//set module, enable for authentication
		//$this->auth->set_default_module('anggota');
		//enable dibawah ini jika divalidasi semua
		//$this->auth->validate(TRUE);
		//enable dibawah ini jika divalidasi user telah login saja
		//$this->auth->validate(TRUE, TRUE);

		//load this page model
		$this->load->model('m_anggota');

		//load foregin lang if exist
		$this->lang->load('module/pegawai');
		$this->lang->load('module/publikasi_dosen');
		$this->lang->load('module/citation');

		//load lang, place this module after foreign lang, so module_ not overriden by foreign lang
		$this->lang->load('module/anggota');
  	}

	/*
	 * method index controller anggota
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
		$this->site_info->set_current_module('anggota');

		//add masterpage script
		$this->asset_library->add_masterpage_script();
		//add page javascript
		$this->asset_library->add_js('plugins/jquery-inputmask/jquery.inputmask.bundle.js');
		$this->asset_library->add_js('js/pages/anggota.js');

		$data = array();
		$this->load->model('m_pegawai');
		$this->load->model('m_publikasi_dosen');
		$this->load->model('m_fakultas');
		$this->load->model('m_jurusan');
		$this->load->model('m_detil_kode_publikasi');

		$data['detil_kode_publikasi'] = $this->m_detil_kode_publikasi->get('dkp_isactive=1', 'dkp_urutan asc');
		$data['fakultas'] = $this->m_fakultas->get('ISNUMERIC(fak_id) = 1 AND fak_singkatan is not NULL', 'fak_id asc');
		$data['jurusan'] = $this->m_jurusan->get('jur_nama_inggris is not NULL', 'jur_id asc');
		$data['list_jurusan'] = json_encode($data['jurusan']);
		// $data['pegawai'] = '0';
		// $data['pegawai_name'] = '';
		$data['pub_view'] = $this->load->view('publikasi_dosen/master', array('pegawai' => '0', 'pegawai_name' => '', 'detil_kode_publikasi' => $data['detil_kode_publikasi'], 'status_tarik' => -1), TRUE);
		
		//load view
		$this->load->view('base/header');
		$this->load->view('anggota/master', $data);
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

		$filter_cols = array('ang_pegawai' => $this->input->post('ang_pegawai'),
					'peg_fakultas' => $this->input->post('peg_fakultas'),
					'peg_jurusan' => $this->input->post('peg_jurusan'),
					'pub_detilkodepub' => $this->input->post('pub_detilkodepub'));

		$add_where = 'pub_judul LIKE \'%' . $this->input->post('pub_judul') . '%\'';
		
		if ($this->input->post('pub_startyear') != "")
			$add_where .= ' AND pub_tahun >= ' . $this->input->post('pub_startyear');
		if ($this->input->post('pub_endyear') != "")
			$add_where .= ' AND pub_tahun <= ' . $this->input->post('pub_endyear');


		//set default where query
		$where = build_masterpage_filter($filter_cols, $add_where);

		//get data
		$this->m_anggota->get_datatable($where);
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
		$search_data = $this->input->post('ang_id');
		//if no search data exist, response error
		if($search_data === NULL)
			ajax_response('error');

		//activate if search data is integer
		//$search_data = uintval($search_data);

		//get detail
		$detail = $this->m_anggota->get_by_column($search_data);

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
		$this->form_validation->set_rules('ang_id'       , 'lang:ang_id'       , 'integer');
		$this->form_validation->set_rules('ang_pegawai'  , 'lang:ang_pegawai'  , 'integer');
		$this->form_validation->set_rules('ang_publikasi', 'lang:ang_publikasi', 'integer');
		$this->form_validation->set_rules('ang_sebagai'  , 'lang:ang_sebagai'  , 'trim|max_length[25]');

		if ($this->form_validation->run())
		{
			// insert
			if($this->input->post('ang_id') == '')
			{
				$ang_id = $this->m_anggota->insert(
					$this->input->post('ang_pegawai'),
					$this->input->post('ang_publikasi'),
					$this->input->post('ang_sebagai')
				);
				write_log('anggota', 'insert', "PK = $ang_id");
				ajax_response();
			}
			// update
			else
			{
				$pk_id = $this->input->post('ang_id');
				//check if pk integer
				//$pk_id = uintval($pk_id);
				$this->m_anggota->update(
					$pk_id,
					$this->input->post('ang_pegawai'),
					$this->input->post('ang_publikasi'),
					$this->input->post('ang_sebagai')
				);
				write_log('anggota', 'update', "PK = $pk_id");
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

		if($this->input->post('ang_id') === NULL) ajax_response();

		$all_deleted = array();
		foreach($this->input->post('ang_id') as $row){
			//$row = uintval($row);
			//permanent delete row, check MY_Model, you can set flag with ->update_single_column
			$this->m_anggota->permanent_delete($row);

			//this is sample code if you cannot delete, but you must update status
			//$this->m_anggota->update_single_column('ang_deleted', 1, $row);
			$all_deleted[] = $row;
		}
		write_log('anggota', 'delete', 'PK = ' . implode(",", $all_deleted));

		ajax_response();
	}
}