<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_access extends MY_Controller {
	
  public function __construct() { 
     parent::__construct();
     $this->load->helper('url');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Page Access');
		$this->header_data('nav', 'settings');
		$this->header_data('dir', './');

		$save = $this->input->post('save');
		$add = $this->input->post('add');

		if(!empty($save)) {
			$this->load->model('Page_access_model','acc');
			$this->acc->delete(array("1" => "1"));

			foreach ($this->input->post('position') as $row) {
				$pid = explode("-", $row);

				$this->acc->reset();
				$this->acc->page = $pid[0];
				$this->acc->position = $pid[1];
				$this->acc->save();
			}
		}

		if(!empty($add)) {
			$this->load->model('Page_model','page');
			$this->page->name = $this->input->post('page_name');
			$this->page->save();
		}

		$data['pages'] = $this->db->get('tbl_pages')->result_object();
		$data['access'] = $this->db->get('tbl_page_access')->result_object();
		$global = $this->load->database('global', TRUE);
		$positions = $global->query("SELECT a.*,b.name,b.pid
			FROM tbl_system_access a
			LEFT JOIN tbl_positions b ON position=pid
			WHERE system=20 "); // system id
		$data['positions'] = $positions->result_object();

		$this->template('page_access_view', $data);
	}

}


