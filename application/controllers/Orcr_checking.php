<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking extends MY_Controller {
	
	public function __construct() { 
		parent::__construct();
		$this->load->helper('url');
    $this->load->model('Orcr_checking_model', 'orcr_checking');
	}
 
	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'For Checking');
		$this->header_data('nav', 'orcr_checking');
		$this->header_data('dir', './');

		$data['tid'] = $this->input->post('tid');
		$data['sid'] = $this->input->post('sid');
		$data['mid'] = $this->input->post('mid');
		$data['summary'] = $this->input->post('summary');
		$data['submit_all'] = $this->input->post('submit_all');
		$data['back'] = $this->input->post('back');

		if (!empty($data['submit_all'])) {
			$this->save($data);
		}

		if (!empty($data['back'])) {
			$data['sid'] = null;
			$data['mid'] = null;
		}

		if (!empty($data['tid'])) {
			$topsheet = $this->orcr_checking->load_topsheet($data);
	    $data['topsheet'] = $topsheet;

	    $view = (!empty($data['summary'])) ? 'orcr_checking/summary' : 'orcr_checking/topsheet';
	    $data['view'] = $this->load->view($view, $data, TRUE);
	  }
		
		$data['table'] = $this->orcr_checking->get_list_for_checking("");
		$this->template('orcr_checking/list', $data);
	}
 
	public function attachment()
	{
		$id = $this->input->post('id');
		$type = $this->input->post('type');
		
		switch ($type)
		{
			case 1:
				$data['sales'] = $this->orcr_checking->sales_attachment($id);
				$view = $this->load->view('orcr_checking/sales_attachment', $data, TRUE);
				break;
			case 2:
				$data['misc'] = $this->orcr_checking->misc_attachment($id);
				$view = $this->load->view('orcr_checking/misc_attachment', $data, TRUE);
				break;
		}

		echo json_encode($view);
	}

	public function save($data)
	{
		foreach ($data['sid'] as $sid) {
			$this->orcr_checking->check_sales($sid);
		}

		foreach ($data['mid'] as $mid) {
			$this->orcr_checking->check_misc($mid, $data['tid']);
		}

  	// update topsheet status
  	$this->load->model('Topsheet_model', 'topsheet');
  	$topsheet = $this->topsheet->check_sales($data['tid']);

  	$_SESSION['messages'][] = 'Transaction # '.$topsheet->trans_no.' updated successfully.';
  	redirect('orcr_checking');
	}
}
