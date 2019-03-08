<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking_model extends CI_Model{

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
                if ($_SESSION['company'] != 8) {
                  $this->companyQry = ' AND t.company != 8';
                } else {
                  $this->region  = $this->mdi_region;
                  $this->company = $this->mdi;
                  $this->companyQry = ' AND t.company = 8';
                }
	}

	public function get_list_for_checking($date)
	{
		if($date != "") $date = " and left(post_date,10) = '".date('Y-m-d')."' ";
		$result = $this->db->query("select t.* from tbl_topsheet t
				inner join tbl_sales s on topsheet = tid and batch = 0
				where t.status < 3
				and da_reason <= 0
				".$date."
                                ".$this->companyQry."
				group by tid
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

	public function load_topsheet($data)
	{
		$sid = (!empty($data['sid'])) ? ' and sid in ('.implode(',', $data['sid']).')' : '';
		$mid = (!empty($data['mid'])) ? ' and mid in ('.implode(',', $data['mid']).')' : '';
		$mid = (!empty($data['summary']) && empty($mid)) ? ' and 1 = 2' : $mid;

		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$data['tid'])->row();
		$topsheet->region  = $this->region[$topsheet->region];
		$topsheet->company = $this->company[$topsheet->company];
		$topsheet->date = substr($topsheet->date, 0, 10);

		$topsheet->total_expense = 0;
		$topsheet->total_credit = 0;
		$topsheet->check = 0;

                $topsheet->sales = $this->db->query("
                        select *,
			case when registration_type = 'Free Registration' then si_no
				when registration_type = 'With Regn. Subsidy' then concat(si_no, '<br>', ar_no)
				else ar_no end as ar_no
			from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where topsheet = ".$topsheet->tid."
			and batch = 0
			and da_reason <= 0
			".$sid."
			order by bcode")->result_object();
		foreach ($topsheet->sales as $key => $sales)
		{
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$sales->sales_type = $this->sales_type[$sales->sales_type];
			$topsheet->sales[$key] = $sales;

			$topsheet->total_expense += ($sales->registration + $sales->tip);
			$topsheet->total_credit += $sales->amount;
		}

		$this->load->model('Expense_model', 'misc');
		$topsheet->misc = $this->db->query("select *,
				left(or_date, 10) as or_date
			from tbl_misc
			where topsheet = ".$topsheet->tid."
			and batch = 0
			".$mid)->result_object();
		foreach ($topsheet->misc as $misc)
		{
			$misc->or_date = substr($misc->or_date, 0, 10);
			$misc->type = $this->misc->type[$misc->type];
		}

		// batch for miscellaneous
		$topsheet->batch = $this->db->query("select * from tbl_batch
			where status = 0 and topsheet = ".$topsheet->tid."
			and left(post_date, 10) = '".date('Y-m-d')."'")->row();

		return $topsheet;
	}

	public function sales_attachment($sid)
	{
		$sales = $this->db->query("select *,
			case when registration_type = 'Free Registration' then si_no
				when registration_type = 'With Regn. Subsidy' then concat(si_no, '<br>', ar_no)
				else ar_no end as ar_no
			from tbl_sales
			inner join tbl_customer on cid = customer
			inner join tbl_engine on eid = engine
			where sid = ".$sid)->row();

		$this->load->helper('directory');
		$folder = './rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/';

		if (is_dir($folder)) {
			$sales->files = directory_map($folder, 1);
		}
		else {
			$sales->files = null;
		}

		return $sales;
	}

	public function misc_attachment($mid)
	{
		$misc = $this->db->query("select * from tbl_misc where mid = ".$mid)->row();

		$this->load->helper('directory');
		$folder = './rms_dir/misc/'.$misc->mid.'/';

		if (is_dir($folder)) {
			$misc->files = directory_map($folder, 1);
		}
		else {
			$misc->files = null;
		}

		return $misc;
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
		// $sales->trans_no = $batch->trans_no;
		// return $sales;
	}

	public function check_misc($mid, $tid)
	{
		$batch = $this->db->query("select * from tbl_batch
					where status = 0 and topsheet = ".$tid."
					and left(post_date, 10) = '".date('Y-m-d')."'")->row();
		if (!empty($batch))
		{
			// save to batch, remove alert
			$this->db->query("update tbl_misc set batch = ".$batch->bid." where mid = ".$mid);
		}

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
