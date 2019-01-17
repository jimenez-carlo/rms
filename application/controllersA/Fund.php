<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fund extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Fund_model', 'fund');
    $this->load->model('Check_model', 'check');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'RRT Fund');
		$this->header_data('nav', 'fund');
		$this->header_data('dir', './');
		$this->footer_data('script', '
      <script src="./assets/modal/rrt_fund.js"></script>');

		$data['table'] = $this->fund->load_rrt_fund($_SESSION['region']);
		$this->template('fund/view', $data); 
	}

	public function transaction($fid, $company, $type)
	{
		$name = $this->fund->get_name($fid);
		$err_msg = array();

		$this->form_validation->set_rules('amount', 'Amount for '.$name, 'required|is_numeric|non_zero');
		$this->form_validation->set_rules('check_no', 'Check # for '.$name, 'required');

		if ($this->form_validation->run() == FALSE) $err_msg[] = validation_errors();

    $amount = $this->input->post('amount');
    if (!empty($amount))
    {
    	switch ($type)
    	{
    		case 1:
    		case 2:
	    		$fund = $this->fund->get_cash_in_bank($fid);
	    		$m_balance = $this->fund->get_m_balance($fid);
	  			if ($fund - $amount < $m_balance) $err_msg[] = 'Invalid amount. You must have a maintaining balance of at least Php '.$m_balance;
    			break;
    		case 3:
    			$fund = $this->fund->get_cash_on_hand($fid);
	  			if ($fund < $amount) $err_msg[] = 'Amount is greater than Cash on Hand.';
	  			break;
    	}
		}

		$check_date = $this->input->post('check_date');
		$check_no = $this->input->post('check_no');
		$check_no_exists = $this->check->is_check_no_exists($check_no, $company);

		if($check_no_exists > 0)
		{
			$err_msg[] = 'Check # already exists. There must be no duplicate Check # per Company and Region.';
		}

		if (!empty($err_msg)) echo json_encode(array("status" => FALSE, "message" => $err_msg));
		else $this->save_transaction($fid, $company, $type, $amount, $check_no, $check_date);
	}

	public function save_transaction($fid, $company, $type, $amount, $check_no, $check_date)
	{
		$transaction = new Stdclass();
		$transaction->fund = $fid;
		$transaction->type = $type;
		$transaction->amount = $amount;
		$transaction->check_no = $check_no;
		$this->fund->save_rrt_transaction($transaction);

		$check = new Stdclass();
		$check->region = $_SESSION['region'];
		$check->company = $company;
		$check->check_no = $check_no;
		$check->check_date = $check_date;
		$this->check->save($check);

		$name = $this->fund->get_name($fid);
		switch ($type) {
			case 1: $_SESSION['messages'][] = 'Cash withdrawal for '.$name.' saved successfully.'; break;
			case 2: $_SESSION['messages'][] = 'Check withdrawal for '.$name.' saved successfully.'; break;
			case 3: $_SESSION['messages'][] = 'Deposit for '.$name.' saved successfully.'; break;
		}
		echo json_encode(array("status" => TRUE));
	}

	public function audit()
	{
		$this->access(1);
		$this->header_data('title', 'Fund Audit');
		$this->header_data('nav', 'audit');
		$this->header_data('dir', './../');
		$this->header_data('link', '
			<link href="./../assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./../vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="./../assets/scripts.js"></script>
      <script src="./../assets/DT_bootstrap.js"></script>
			<script>
			$(function(){
				$(".table").dataTable({
					"sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
					"sPaginationType": "bootstrap",
					"oLanguage": {
						"sLengthMenu": "_MENU_ records per page"
					},
					"bFilter": false,
					"bSort": false,
					"iDisplayLength": 6,
					"aLengthMenu": [[6, 12, -1], [6, 12, "All"]]
				});
			});
			</script>');

		$data['table'] = $this->fund->get_rrt_funds();
		$this->template('fund/audit', $data);  
	}

	public function passbook($region = null)
	{
		$this->access(1);
		$this->header_data('title', 'Fund Audit');
		$this->header_data('nav', 'withdraw');
		$this->header_data('dir', './../');
		$this->header_data('link', '
			<link href="./../assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./../vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="./../assets/scripts.js"></script>
      <script src="./../assets/DT_bootstrap.js"></script>
			<script>
			$(function(){
				$(".table").dataTable({
					"sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
					"sPaginationType": "bootstrap",
					"oLanguage": {
						"sLengthMenu": "_MENU_ records per page"
					},
					"bFilter": false,
					"bSort": false,
					"iDisplayLength": 5,
					"aLengthMenu": [[5, 10, 25, 100, -1], [5, 10, 25, 100, "All"]]
				});
			});
			</script>');

		if (empty($region))
		{
			$region = $_SESSION['region'];
			$this->header_data('dir', './../');
		}
		else
		{
			$this->header_data('dir', './../../');
		}

		$data['table'] = $this->db->query("select fh.*, company
			from tbl_fund_history fh
			inner join tbl_fund on fh.fund = fid
			where region = ".$region."
			order by date desc
			limit 1000")->result_object();

		$data['mnc'] = $this->db->query("select fh.*, company
			from tbl_fund_history fh
			inner join tbl_fund on fh.fund = fid
			where region = ".$region."
			and company = 1
			order by date
			limit 1000")->result_object();
		$data['mti'] = $this->db->query("select fh.*, company
			from tbl_fund_history fh
			inner join tbl_fund on fh.fund = fid
			where region = ".$region."
			and company = 2
			order by date
			limit 1000")->result_object();
		$data['hpti'] = $this->db->query("select fh.*, company
			from tbl_fund_history fh
			inner join tbl_fund on fh.fund = fid
			where region = ".$region."
			and company = 3
			order by date
			limit 1000")->result_object();

		$this->template('fund/passbook', $data); 
	}

	public function bank()
	{
		$this->access(1);
		$this->header_data('title', 'Fund Bank');
		$this->header_data('nav', 'bank');
		$this->header_data('dir', './../');

		$submit = $this->input->post('submit');
		if (!empty($submit))
		{
			$bank = $this->input->post('bank');

			foreach ($bank as $key => $val)
			{
				$this->db->query("update tbl_fund set bank = '".$val."'
					where fid = ".$key);
			}

			$_SESSION['messages'][] = 'Saved successfully.';
		}

		$table = $this->db->query("select * from tbl_fund")->result_object();

		foreach ($table as $key => $row)
		{
			switch ($row->company)
			{
				case 1: $row->company_name = 'MNC'; break;
				case 2: $row->company_name = 'MTI'; break;
				case 3: $row->company_name = 'HPTI'; break;
			}
			
			$table[$key] = $row;
		}

		$data['table'] = $table;
		$this->template('fund/bank', $data);
	}
}
