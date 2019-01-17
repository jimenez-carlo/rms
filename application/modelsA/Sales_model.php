<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model{
	
	public $sales_type = array(
		0 => 'Brand New (Cash)',
		1 => 'Brand New (Installment)'
	);
	
	public $status = array(
		0 => 'New',
		1 => 'LTO Rejected',
		2 => 'LTO Pending',
		3 => 'NRU',
		4 => 'Registered',
		5 => 'Closed',
	);

	public $lto_reason = array(
		0 => 'N/A',
		1 => 'Affidavit of Change Body Type',
		2 => 'Closed Item',
		3 => 'COC Does Not Exist',
		4 => 'DIY Reject',
		5 => 'Expired Accre',
		6 => 'Expired Insurance',
		7 => 'Lost Docs',
		8 => 'Need Affidavit of Lost Docs',
		9 => 'No Date on SI',
		10 => 'No Sales Report',
		11 => 'No TIN #',
		12 => 'Self Registration',
		13 => 'Unreadable SI',
		14 => 'Wrong CSR Attached',
	);

	public $topsheet_region = array(
		1 => 'NCR',
		2 => 'R1',
		3 => 'R2',
		4 => 'R3',
		5 => 'R4A',
		6 => 'R4B',
		7 => 'R5',
		8 => 'R6',
		9 => 'R7',
		10 => 'R8',
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('directory');
		$this->load->model('Login_model', 'login');
		$this->load->model('Transmittal_model', 'transmittal');
		$this->load->model('Fund_model', 'fund');
	}

	public function get_id_by_engine($engine_no)
	{
		$id = $this->db->query("select sid from tbl_sales
			left join tbl_engine on engine=eid
			where engine_no='".$engine_no."'")->row();
		if(!empty($id)) $id = $id->sid;
		return $id;
	}

	public function get_sales($param)
	{
		$result = $this->db->get_where('tbl_sales', $param);
		return $result->result_object();
	}

	public function get_sales_by_transmittal($tid)
	{
		$sales = $this->db->query("select * from tbl_sales
				inner join tbl_transmittal_sales on sales=sid
				where transmittal= ".$tid)->result_object();
		foreach ($sales as $key => $sale) {
			$sales[$key]->remarks = $this->db->query("select * from tbl_transmittal_remarks
				where transmittal = ".$tid." and sales = ".$sale->sid)->result_object();
		}
		return $sales;
	}

	public function get_sales_by_topsheet($tid)
	{
		return $this->db->query("select * from tbl_sales
				where topsheet = ".$tid)->result_object();
	}

	public function get_sales_by_branch_type($tid)
	{
		return $this->db->query("select branch, sales_type, group_concat(sid) as sid from tbl_sales
					where topsheet = ".$tid."
					group by branch, sales_type")->result_object();
	}

	public function get_sr_with($date_sold)
	{
		$branches = $this->cmc->get_region_branches($_SESSION['region']);
		$sales = $this->db->query("select * from tbl_sales
			left join tbl_engine on engine = eid
			left join tbl_customer on customer = cid
			where registration_type = 'Self Registration' and 
			transmittal_date IS NULL and 
			branch in (".$branches.") and
			LEFT(date_sold,10) like '$date_sold%'
			limit 1000")->result_object();
		
		if (!empty($sales))
		{
			foreach ($sales as $key => $sale) {
				$sales[$key]->status = $this->status[$sale->status];
				$sales[$key]->branch = $this->cmc->get_branch($sale->branch);
			}
		}

		return $sales;
	}

	public function get_sr_without($date_sold)
	{
		$branches = $this->cmc->get_region_branches($_SESSION['region']);
		$sales = $this->db->query("select * from tbl_sales
			left join tbl_engine on engine = eid
			left join tbl_customer on customer = cid
			where registration_type = 'Self Registration' and 
			transmittal_date IS NOT NULL and
			branch in (".$branches.") and
			LEFT(date_sold,10) like '$date_sold%'
			limit 1000")->result_object();
		
		if (!empty($sales))
		{
			foreach ($sales as $key => $sale) {
				$sales[$key]->status = $this->status[$sale->status];
				$sales[$key]->branch = $this->cmc->get_branch($sale->branch);
			}
		}

		return $sales;
	}

	// -- START HERE -- //

	public function load_sales($sid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$this->load->model('Fund_model', 'fund');

		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where sid = ".$sid)->row();
		$sales->branch = $this->cmc->get_branch($sales->branch);
		$sales->fund = $this->fund->get_company_cash($sales->branch->region, $sales->branch->company);
		$sales->sales_type = $this->sales_type[$sales->sales_type];
		return $sales;
	}

	public function load_sales_by_engine($engine_no)
	{
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where engine_no = '".$engine_no."'
			limit 1000")->row();
		
		if (!empty($sales))
		{
			$sales->edit = ($_SESSION['position'] == 108 
				&& $sales->status == 3 
				&& substr($sales->registration_date, 0, 10) == date('Y-m-d'));
			$sales->status = $this->status[$sales->status];
		}

		return $sales;
	}

	public function save_lto_pending($sales)
	{
		$engine = $this->get_engine($sales->sid);

		if ($sales->status == 2)
		{
			$sales->lto_reason = 0;
			$sales->pending_date = date('Y-m-d H:i:s');

			$this->login->saveLog('Marked sale ['.$sales->sid.'] with Engine # '.$engine.' as PENDING at LTO');
		}
		else
		{
			$this->login->saveLog('Marked sale ['.$sales->sid.'] with Engine # '.$engine.' as REJECTED at LTO with reason: '.$this->lto_reason[$sales->lto_reason]);
		}

		$this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));
	}

	public function save_status($sid,$status)
	{
		$this->db->query("update tbl_sales set status = '".$status."' where sid = ".$sid);
	}

	public function save_nru($sales)
	{
		$engine = $this->get_engine($sales->sid);
		$this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));
		$this->login->saveLog('Saved Registration Expense [Php '.$sales->registration.'] for Engine # '.$engine.' ['.$sales->sid.']');
	}

	public function save_registration($sales)
	{
		$this->load->model('Cmc_model', 'cmc');
		$sales->user = $_SESSION['uid'];
		$this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));

		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			where sid = ".$sales->sid)->row();
		$sales->registration_date = substr($sales->registration_date, 0, 10);
		$sales->branch = $this->cmc->get_branch($sales->branch);

		// rerfo
		$rerfo = $this->db->query("select * from tbl_rerfo
			where branch = ".$sales->branch->bid."
			and date = '".$sales->registration_date."'")->row();
		if (empty($rerfo))
		{
			$rerfo = new Stdclass();
			$rerfo->branch = $sales->branch->bid;
			$rerfo->date = $sales->registration_date;
			$rerfo->trans_no = 'R-'.$sales->branch->b_code.'-'
				.substr($rerfo->date, 2, 2)
				.substr($rerfo->date, 5, 2)
				.substr($rerfo->date, 8, 2);
			$this->db->insert('tbl_rerfo', $rerfo);
			$rerfo->rid = $this->db->insert_id();
		}

		// topsheet
		$topsheet = $this->db->query("select * from tbl_topsheet
			where region = ".$sales->branch->ph_region."
			and company = ".$sales->branch->company."
			and date = '".substr($sales->registration_date, 0, 10)."'")->row();
		if (empty($topsheet))
		{
			$topsheet = new Stdclass();
			$topsheet->region = $sales->branch->ph_region;
			$topsheet->company = $sales->branch->company;
			$topsheet->date = $sales->registration_date;
			$topsheet->trans_no = 'T-'.$this->topsheet_region[$sales->branch->ph_region].'-'
				.$sales->branch->company.'0'
				.substr($topsheet->date, 2, 2)
				.substr($topsheet->date, 5, 2)
				.substr($topsheet->date, 8, 2);
			$this->db->insert('tbl_topsheet', $topsheet);
			$topsheet->tid = $this->db->insert_id();
		}

		$this->db->query("update tbl_sales
			set rerfo = ".$rerfo->rid.",
			topsheet = ".$topsheet->tid."
			where sid = ".$sales->sid);
		$this->login->saveLog('Saved Registration Expense [Php '.$sales->registration.'] for Engine # '.$sales->engine_no.' ['.$sales->sid.']');
	}

	public function get_engine($sid)
	{
		return $this->db->query("select engine_no from tbl_engine
			inner join tbl_sales on engine = eid
			where sid = ".$sid)->row()->engine_no;
	}

	public function search_engine($engine_no, $region)
	{
		$this->load->model('Cmc_model', 'cmc');
		$this->load->model('Fund_model', 'fund');
		$branches = $this->cmc->get_region_branches($region);

		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			where engine_no = '".$engine_no."'
			and branch in (".$branches.")
			and status = 3")->row();

		if (empty($sales)) return null;
		else return $sales->sid;
	}

	public function get_cr_no($sid)
	{
		return $this->db->query("select cr_no from tbl_sales where sid = ".$sid)->row()->cr_no;
	}

	public function update_acct_status($sid,$status)
	{
		$this->db->query("update tbl_sales set acct_status = $status where sid = $sid");
	}

	public function get_orcr_by_engine($engine_no)
	{
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			where engine_no = '".$engine_no."'")->row();

		// load files
		$this->load->helper('directory');
		$sales->files = directory_map('./rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/', 1);

		return $sales;
	}

	public function print_orcr($sid)
	{
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			where sid = ".$sid)->row();

		// load files
		$this->load->helper('directory');
		$sales->files = directory_map('./../../rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/', 1);

		return $sales;
	}
}
