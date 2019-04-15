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

<<<<<<< HEAD
	public function create_voucher($fid, $cid)
	{
		$data['fund'] = $this->projected_fund->create_voucher($fid, $cid);
		$data['company'] = $this->projected_fund->company;
		$view = $this->load->view('projected_fund/create_voucher', $data, TRUE);
		echo json_encode($view);
=======
	public function create_voucher() {
          // $fid = $this->input->get('fid'); //FOR DEBUGGING
          // $cid = $this->input->get('cid');
          $fid = $this->input->post('fid');
          $cid = $this->input->post('cid');
          $data['fid'] = $fid;
          $data['fund'] = $this->projected_fund->create_voucher($fid, $cid);
          $data['company'] = ($_SESSION['company'] != 8) ? $this->projected_fund->company : $this->projected_fund->mdi;
          $view = $this->load->view('projected_fund/create_voucher', $data, TRUE);
          print $view;
>>>>>>> production.50
	}

	public function sprint()
	{
		$fid = $this->input->post('fid');
		$ltid = $this->input->post('ltid');
		$ltid = implode(',', array_keys($ltid));

		$data['fund'] = $this->projected_fund->print_projected($fid, $ltid);
		$this->load->view('projected_fund/print_projected', $data);
	}

<<<<<<< HEAD
	public function save_voucher($fid)
	{
		$ltid = $this->input->post('ltid');
		$err_msg = array();

		$this->form_validation->set_rules('voucher_no', 'Document #', 'required');

    if ($this->form_validation->run() == FALSE) {
    	$err_msg[] = validation_errors();
    }
    if (empty($ltid)) {
    	$err_msg[] = 'Please select at least one transmittal to proceed.';
    }
    
		if (!empty($err_msg)) echo json_encode(array("status" => FALSE, "message" => $err_msg));
		else $this->true_save_voucher($fid);
	}

	public function true_save_voucher($fid)
	{
		$ltid = $this->input->post('ltid');
		$ltid = implode(',', array_keys($ltid));

  	$voucher = new Stdclass();
		$voucher->fund = $fid;
		$voucher->reference = $this->input->post('reference');
		$voucher->voucher_no = $this->input->post('voucher_no');
		$voucher->amount = $this->input->post('amount');
		$voucher = $this->projected_fund->save_voucher($voucher, $ltid);
		
		$_SESSION['messages'][] = 'Created Document # '.$voucher->voucher_no.' for '.$voucher->region.'.';
		echo json_encode(array("status" => TRUE));
	}
=======
        public function save_voucher() {
          $fid  = $this->input->post('fid');
          $ltid = $this->input->post('ltid');
          $err_msg = array();

          $this->form_validation->set_rules('voucher_no', 'Document #', 'required');

          if ($this->form_validation->run() == FALSE) {
            $err_msg[] = validation_errors();
          }
          if (empty($ltid)) {
            $err_msg[] = 'Please select at least one transmittal to proceed.';
          }

          if (!empty($err_msg)) {
            echo json_encode(array("status" => FALSE, "message" => $err_msg));
          } else {
            $this->true_save_voucher($fid);
          }
        }

        public function true_save_voucher($fid) {
          $ltid = $this->input->post('ltid');
          $ltid = implode(',', array_keys($ltid));

          $voucher = new Stdclass();
          $voucher->fund = $fid;
          $voucher->reference = $this->input->post('reference');
          $voucher->voucher_no = $this->input->post('voucher_no');
          $voucher->amount = $this->input->post('amount');
          $voucher = $this->projected_fund->save_voucher($voucher, $ltid);

          $_SESSION['messages'][] = 'Created Document # '.$voucher->voucher_no.' for '.$voucher->region.'.';
          echo json_encode(array("status" => TRUE));
        }
>>>>>>> production.50

  /**
   * Accounting to view list of Voucher
   */
	public function ca_list()
	{
		$nav = ($_SESSION['position'] == 34) ? 'deposited_fund' : 'projected_fund';
		$data['def_stat'] = ($_SESSION['position'] == 34) ? 2 : '';

		$this->access(1);
		$this->header_data('title', 'CA List');
		$this->header_data('nav', $nav);
		$this->header_data('dir', './../');
			$this->footer_data('script', '
	      <script src="./../assets/js/voucher_list.js"></script>');

		$param = new Stdclass;
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->status = $this->input->post('status');
		$param->region = $this->input->post('region');

		$param->date_from = (isset($param->date_from)) ? $param->date_from : date('Y-m-d');
		$param->date_to = (isset($param->date_to)) ? $param->date_to : date('Y-m-d');
		$param->status = (isset($param->status)) ? $param->status : $data['def_stat'];

		$data['status'] = $this->projected_fund->status;
		$data['table'] = $this->projected_fund->list_voucher($param);
		$this->template('projected_fund/list_voucher', $data);
	}
}
