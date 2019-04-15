<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_accounts extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Fund_transfer_model', 'fund_transfer');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'RRT Fund');
		$this->header_data('nav', 'rrt_fund');
		$this->header_data('dir', './');
		$this->header_data('link', '
			<link href="assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="assets/DT_bootstrap.js"></script>
      <script src="assets/modal/bank_accounts.js"></script>');

		$data['table'] = $this->fund_transfer->get_rrt_fund();
		$this->template('bank_accounts/view', $data);
	}

	public function manage($fid)
	{
		$data['fund'] = $this->fund_transfer->get_rrt_fund2($fid);
		$view = $this->load->view('bank_accounts/manage', $data, TRUE);
		echo json_encode($view);
	}

	public function save($fid)
	{
		$m_balance = $this->input->post('m_balance');
		$acct_number = $this->input->post('acct_number');
		$sign_1 = strtoupper($this->input->post('sign_1'));
		$sign_2 = strtoupper($this->input->post('sign_2'));
		$sign_3 = strtoupper($this->input->post('sign_3'));

		// rules / validation
		$this->form_validation->set_rules('m_balance', 'Maintaining Balance', 'required|is_numeric');
		$this->form_validation->set_rules('acct_number', 'Account Number', 'required|is_numeric');
		$this->form_validation->set_rules('sign_1', 'Signatory #1', 'required');
		$this->form_validation->set_rules('sign_2', 'Signatory #2', 'required');
		$this->form_validation->set_rules('sign_3', 'Signatory #3', 'required');

    if ($this->form_validation->run() == TRUE)
    {
    	$fund = new Stdclass();
    	$fund->m_balance = $m_balance;
    	$fund->acct_number = $acct_number;
    	$fund->sign_1 = $sign_1;
    	$fund->sign_2 = $sign_2;
    	$fund->sign_3 = $sign_3;
    	$this->fund_transfer->save_fund($fid, $fund);

			$_SESSION['messages'][] = 'Changes has been successfully saved.';
			echo json_encode(array("status" => TRUE));
		}
		else
		{
			echo json_encode(array("status" => FALSE, "message" => validation_errors()));
		}
	}

	public function transfer($fid)
	{
		$fpid = $this->input->post('fpid');
		$amount = $this->input->post('amount');
		$err_msg = array();

		$this->form_validation->set_rules('amount', 'Amount', 'required|is_numeric|non_zero');
		$this->form_validation->set_rules('dm_no', 'Debit Memo #', 'required');
		$this->form_validation->set_rules('date', 'Date Transferred', 'required');

		$total = 0;
    foreach ($fpid as $key => $val) $total += $val;

    if ($this->form_validation->run() == FALSE) {
    	$err_msg[] = validation_errors();
    }
    if (empty($fpid)) {
    	$err_msg[] = 'Please select at least one Projected Cost.';
    }
		if ($amount < $total) {
			$err_msg[] = 'Amount must be greater than Total Projected Cost.';
		}
    
		if (!empty($err_msg)) echo json_encode(array("status" => FALSE, "message" => $err_msg));
		else $this->save_transfer($fid);
	}

	public function save_transfer($fid)
	{
		$offline = $this->input->post('offline');
  	$fund_transfer = new Stdclass();
		$fund_transfer->fund = $fid;
		$fund_transfer->dm_no = $this->input->post('dm_no');
		$fund_transfer->amount = $this->input->post('amount');
		$fund_transfer->date = $this->input->post('date');
		$fund_transfer->offline = (!empty($offline));
		$fund_transfer = $this->fund_transfer->save($fund_transfer);
		
		$_SESSION['messages'][] = 'Added fund for '.$fund_transfer->region.' '.$fund_transfer->company.'.';
		echo json_encode(array("status" => TRUE, "ftid" => $fund_transfer->ftid));
	}

	public function sprint($ftid)
	{
		$data['fund_transfer'] = $this->db->query("select * from tbl_fund_transfer where ftid = ".$ftid)->row();
		$data['table'] = $this->db->query("select * from tbl_fund_projected
			where fund_transfer = ".$ftid)->result_object();

		$this->load->view('fund_transfer/print', $data);
	}

	public function audit()
	{
		$this->access(1);
		$this->header_data('title', 'Fund Transfer Audit');
		$this->header_data('nav', 'audit');
		$this->header_data('dir', './../');
		$this->header_data('link', '
			<link href="../assets/DT_bootstrap.css" rel="stylesheet" media="screen">
      <link href="../vendors/chosen.min.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
      <script src="../assets/modal/fund_transfer_audit.js"></script>
			<script src="../vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="../assets/scripts.js"></script>
      <script src="../assets/DT_bootstrap.js"></script>');

		$data = array();
		$region = $this->input->post('region');
		$company = $this->input->post('company');

		if(!empty($region) && !empty($company)) {
			$data['table'] = $this->fund_transfer->get_fund_transfer($region, $company);
		}

		$this->template('fund_transfer/audit', $data);
	}

	public function edit_details($ftid)
	{
		$row = $this->db->query("select * from tbl_fund_transfer where ftid=".$ftid)->row();
		echo json_encode(substr($row->date, 0, 10));
	}

	public function save_date($ftid)
	{
		$this->form_validation->set_rules('date', 'Date Transferred', 'required');

    if ($this->form_validation->run() == TRUE)
    {
    	$date = $this->input->post('date');

    	$this->db->query("update tbl_fund_transfer set date = '".$date."'
    		where ftid = ".$ftid);

			echo json_encode(array("status" => TRUE));
		}
		else
		{
			echo json_encode(array("status" => FALSE, "message" => validation_errors()));
		}
	}
}
