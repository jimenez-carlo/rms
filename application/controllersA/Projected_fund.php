<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projected_fund extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Projected_fund_model', 'projected_fund');
  }

  /**
   * Accounting to Create Voucher
   */
	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Projected Funds');
		$this->header_data('nav', 'projected_fund');
		$this->header_data('dir', './');
		$this->footer_data('script', '
      <script src="assets/modal/projected_fund.js"></script>');

		$data['position'] = $_SESSION['position'];

		$data['table'] = $this->projected_fund->get_projected_funds();
		$this->template('projected_fund/list_projected', $data);
	}

	public function create_voucher($fid)
	{
		$data['fund'] = $this->projected_fund->create_voucher($fid);
		$view = $this->load->view('projected_fund/create_voucher', $data, TRUE);
		echo json_encode($view);
	}

	public function sprint($fid)
	{
		$fpids = $this->input->post('fpid');
		$fpids = array_keys($fpids);
		$fpids = implode(',', $fpids);
		$data['fund'] = $this->projected_fund->print_projected($fid, $fpids);
		$this->load->view('projected_fund/print_projected', $data);
	}

	public function save_voucher($fid)
	{
		$fpids = $this->input->post('fpid');
		$err_msg = array();

		$this->form_validation->set_rules('voucher_no', 'Voucher #', 'required');
		$this->form_validation->set_rules('dm_no', 'Debit Memo #', 'required');

    if ($this->form_validation->run() == FALSE) {
    	$err_msg[] = validation_errors();
    }
    if (empty($fpids)) {
    	$err_msg[] = 'Please select at least one Projected Cost.';
    }
    
		if (!empty($err_msg)) echo json_encode(array("status" => FALSE, "message" => $err_msg));
		else $this->true_save_voucher($fid);
	}

	public function true_save_voucher($fid)
	{
		$fpids = $this->input->post('fpid');

  	$voucher = new Stdclass();
		$voucher->fund = $fid;
		$voucher->voucher_no = $this->input->post('voucher_no');
		$voucher->dm_no = $this->input->post('dm_no');
		$voucher->amount = $this->input->post('amount');
		$voucher = $this->projected_fund->save_voucher($voucher, $fpids);
		
		$_SESSION['messages'][] = 'Created Voucher # '.$voucher->voucher_no.' for '.$voucher->region.' '.$voucher->company.'.';
		echo json_encode(array("status" => TRUE));
	}

  /**
   * Treasury to Transfer Fund
   */
	public function for_transfer()
	{
		$this->access(1);
		$this->header_data('title', 'Fund Transfer');
		$this->header_data('nav', 'projected_fund');
		$this->header_data('dir', './../');
		$this->footer_data('script', '
      <script src="../assets/modal/for_transfer.js"></script>');

		$data['table'] = $this->projected_fund->get_for_transfer();
		$this->template('projected_fund/for_transfer', $data);
	}

	public function transfer_fund($vid)
	{
		$data['voucher'] = $this->projected_fund->transfer_fund($vid);
		$view = $this->load->view('projected_fund/transfer_fund', $data, TRUE);
		echo json_encode($view);
	}

	public function save_transfer($vid)
	{
		$err_msg = array();
		$this->form_validation->set_rules('transfer_date', 'Date Transferred', 'required');

    if ($this->form_validation->run() == FALSE) {
    	$err_msg[] = validation_errors();
    }
    
		if (!empty($err_msg)) echo json_encode(array("status" => FALSE, "message" => $err_msg));
		else $this->true_save_transfer($vid);
	}

	public function true_save_transfer($vid)
	{
		$offline = $this->input->post('offline');

  	$voucher = new Stdclass();
		$voucher->vid = $vid;
		$voucher->transfer_date = $this->input->post('transfer_date');
		$voucher->offline = (isset($offline));
		$voucher->status = 1;
		$voucher = $this->projected_fund->save_transfer($voucher);
		
		$_SESSION['messages'][] = 'Transferred fund of Debit Memo # '.$voucher->dm_no.' for '.$voucher->region.' '.$voucher->company.'.';
		echo json_encode(array("status" => TRUE));
	}

  /**
   * Accounting to view list of Voucher
   */
	public function voucher()
	{
		$this->access(1);
		$this->header_data('title', 'Voucher');
		$this->header_data('nav', 'projected_fund');
		$this->header_data('dir', './../');

		$param = new Stdclass;
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');

		$param->date_from = (isset($param->date_from)) ? $param->date_from : date('Y-m-d');
		$param->date_to = (isset($param->date_to)) ? $param->date_to : date('Y-m-d');

		$data['table'] = $this->projected_fund->get_vouchers($param);
		$this->template('projected_fund/list_voucher', $data);
	}

  /**
   * Treasury to view list of Transferred Funds
   */
	public function transferred()
	{
		$this->access(1);
		$this->header_data('title', 'Transferred Fund');
		$this->header_data('nav', 'projected_fund');
		$this->header_data('dir', './../');

		$param = new Stdclass;
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');

		$param->date_from = (isset($param->date_from)) ? $param->date_from : date('Y-m-d');
		$param->date_to = (isset($param->date_to)) ? $param->date_to : date('Y-m-d');

		$data['table'] = $this->projected_fund->get_transferred_funds($param);
		$this->template('projected_fund/list_transferred', $data);
	}
}
