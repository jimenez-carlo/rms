<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking_model extends CI_Model{

	public $region = array(
		1 => 'NCR',
		2 => 'Region 1',
		3 => 'Region 2',
		4 => 'Region 3',
		5 => 'Region 4A',
		6 => 'Region 4B',
		7 => 'Region 5',
		8 => 'Region 6',
		9 => 'Region 7',
		10 => 'Region 8',
	);

	public $company = array(
		1 => 'MNC',
		2 => 'MTI',
		3 => 'HPTI',
	);

	public $status = array(
		0 => 'New',
		1 => 'Incomplete',
		2 => 'Done',
	);

	public $sales_type = array(
		0 => 'Brand New (Cash)',
		1 => 'Brand New (Installment)',
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function get_list_for_checking($date)
	{
		if($date != "") $date = " and left(post_date,10) = '".date('Y-m-d')."' ";
		$result = $this->db->query("select * from tbl_topsheet
				where status < 3 and print > 0".$date."
				order by date desc
				limit 1000")->result_object();

		foreach ($result as $key => $topsheet)
		{
			$topsheet->region = $this->region[$topsheet->region];
			$topsheet->company = $this->company[$topsheet->company];
			$topsheet->status = $this->status[$topsheet->status];
			$topsheet->date = substr($topsheet->date, 0, 10);
			$topsheet->alert = $this->db->query("select count(*) as count from tbl_sales
				where acct_status = 2 and topsheet = ".$topsheet->tid)->row()->count;
			$result[$key] = $topsheet;
		}

		return $result;
	}

	public function load_topsheet($tid)
	{
		$this->load->helper('directory');
		$this->load->model('Cmc_model', 'cmc');

		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$tid)->row();
		$topsheet->region = $this->region[$topsheet->region];
		$topsheet->company = $this->company[$topsheet->company];
		$topsheet->date = substr($topsheet->date, 0, 10);
		$topsheet->sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where topsheet = ".$tid)->result_object();

		$topsheet->total_expense = 0;
		$topsheet->total_credit = 0;
		$topsheet->check = 0;

		foreach ($topsheet->sales as $key => $sales)
		{
			$sales->branch = $this->cmc->get_branch($sales->branch);
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$sales->sales_type = $this->sales_type[$sales->sales_type];
			$sales->files = directory_map('./rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/', 1);
			$topsheet->sales[$key] = $sales;

			$topsheet->total_expense += ($sales->registration + $sales->tip);
			$topsheet->total_credit += $sales->amount;
		}

		$topsheet->total_misc = ($topsheet->meal
			+ $topsheet->photocopy
			+ $topsheet->transportation
			+ $topsheet->others);

		// load files
		$this->load->helper('directory');
		$topsheet->files = directory_map('./rms_dir/misc/'.$topsheet->tid.'_'.$topsheet->trans_no.'/', 1);

		// batch for miscellaneous
		$topsheet->batch = $this->db->query("select * from tbl_batch
					where status = 0 and topsheet = ".$tid."
					and left(post_date, 10) = '".date('Y-m-d')."'")->row();

		return $topsheet;
	}

	public function check_sales($sid)
	{
		$sales = $this->db->query('select * from tbl_sales
					inner join tbl_engine on engine = eid
					where sid = '.$sid)->row();

		$batch = $this->db->query("select * from tbl_batch
					where status = 0 and topsheet = ".$sales->topsheet."
					and left(post_date, 10) = '".date('Y-m-d')."'")->row();
		if (empty($batch))
		{
			// generate batch
			$topsheet = $this->db->query("select * from tbl_topsheet
				where tid = ".$sales->topsheet)->row();
			$count = $this->db->query("select count(*)+1 as count from tbl_batch
				where topsheet = ".$sales->topsheet)->row()->count;

			$batch = new Stdclass();
			$batch->topsheet = $sales->topsheet;
			$batch->trans_no = $topsheet->trans_no.'-B'.$count;

			$this->db->insert('tbl_batch', $batch);
			$batch->bid = $this->db->insert_id();
		}

		// save to batch, remove alert
		$this->db->query("update tbl_sales set batch = ".$batch->bid.", acct_status = 3 where sid = ".$sid);

  	$this->load->model('Login_model', 'login');
		$this->login->saveLog('marked ORCR ['.$sid.'] with Engine # '.$sales->engine_no.' as checked');

		// for message
		$sales->trans_no = $batch->trans_no;
		return $sales;
	}

	public function check_misc($tid)
	{
		$batch = $this->db->query("select * from tbl_batch
					where status = 0 and topsheet = ".$tid."
					and left(post_date, 10) = '".date('Y-m-d')."'")->row();

		// save to batch, remove alert
		$this->db->query("update tbl_batch set misc = 1 where bid = ".$batch->bid);
		$this->db->query("update tbl_topsheet set misc_status = 3 where tid = ".$tid);

		// for log
		$topsheet = $this->db->query('select * from tbl_topsheet where tid = '.$tid)->row();

  	$this->load->model('Login_model', 'login');
		$this->login->saveLog('marked miscellaneous expense for Transaction # '.$topsheet->trans_no.' as checked');

		return $topsheet;
	}

	public function hold_sales($param)
	{
		$sales = $this->db->query('select * from tbl_sales
					inner join tbl_engine on engine = eid
					inner join tbl_customer on customer = cid
					where sid = '.$param->sid)->row();
		$sales->reason = $param->reason;
		$sales->remarks = $param->remarks;

		$this->db->query("update tbl_sales set acct_status = 1 where sid = ".$sales->sid);

		foreach ($sales->reason as $value)
		{
			$reason = new Stdclass();
			$reason->topsheet = $sales->topsheet;
			$reason->sales = $sales->sid;
			$reason->reason = $value;
			$this->db->insert('tbl_topsheet_reason', $reason);
		}

  	if (in_array('0', $sales->reason))
  	{
			$remarks = new Stdclass();
			$remarks->topsheet = $sales->topsheet;
			$remarks->sales = $sales->sid;
			$remarks->user = $_SESSION['uid'];
			$remarks->remarks = $sales->remarks;
			$this->db->insert('tbl_topsheet_remarks', $remarks);
  	}

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('marked ORCR ['.$sales->sid.'] with Engine # '.$sales->engine_no.' as hold with remarks ['.$sales->remarks.']');

		return $sales;
	}

	public function hold_misc($param)
	{
		$topsheet = $this->db->query('select * from tbl_topsheet where tid = '.$param->tid)->row();
		$topsheet->reason = $param->reason;
		$topsheet->remarks = $param->remarks;

		$this->db->query("update tbl_topsheet set misc_status = 1 where tid = ".$topsheet->tid);

		foreach ($topsheet->reason as $value)
		{
			$reason = new Stdclass();
			$reason->topsheet = $topsheet->tid;
			$reason->sales = 0;
			$reason->reason = $value;
			$this->db->insert('tbl_topsheet_reason', $reason);
		}

  	if (in_array('0', $topsheet->reason))
  	{
			$remarks = new Stdclass();
			$remarks->topsheet = $topsheet->tid;
			$remarks->sales = 0;
			$remarks->user = $_SESSION['uid'];
			$remarks->remarks = $topsheet->remarks;
			$this->db->insert('tbl_topsheet_remarks', $remarks);
  	}

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('marked ORCR ['.$sales->sid.'] with Engine # '.$sales->engine_no.' as hold with remarks ['.$sales->remarks.']');

		return $topsheet;
	}
}
