<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fund_transfer extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Fund_transfer_model', 'fund_transfer');
    $this->load->model('Fund_model', 'fund');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Fund Transfer');
		$this->header_data('nav', 'fund_transfer');
		$this->header_data('dir', './');
		$this->footer_data('script', '
      <script src="assets/modal/fund_transfer.js"></script>');

		$data['table'] = $this->fund_transfer->get_for_transfer();
		$this->template('fund_transfer/view', $data);
	}

	public function projected($fid)
	{
		$data['table'] = $this->fund_transfer->get_projected($fid);
		$view = $this->load->view('fund_transfer/transfer', $data, TRUE);
		echo json_encode($view);
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
		$data['fund_transfer'] = $this->fund_transfer->get_ft_row($ftid);
		$data['fund'] = $this->fund->get_company_region($data['fund_transfer']->fund);
		$data['table'] = $this->fund_transfer->get_fund_projected($ftid);

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
