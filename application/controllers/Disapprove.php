<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disapprove extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Disapprove_model', 'disapprove');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Disapprove List');
		$this->header_data('nav', 'disapprove');
		$this->header_data('dir', './');

		$param = new Stdclass();
		$param->region = $_SESSION['region'];
		$param->branch = $this->input->post('branch');

		$data['branch'] = $this->disapprove->branch_list($param);
		$data['table'] = $this->disapprove->load_list($param);
		$data['da_reason'] = $this->disapprove->da_reason;
		$this->template('disapprove/list', $data);
	}

	public function sales()
	{
		$sales = new Stdclass();
		$sales->sid = $this->input->post('sid');
		$sales->da_reason = $this->input->post('da_reason');
		$this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));

		$sales->da_reason = $this->disapprove->da_reason[$sales->da_reason];
		echo json_encode($sales->da_reason);
	}
}
