<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Topsheet_model extends CI_Model{

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
		0 => 'For Checking',
		1 => 'Incomplete',
		2 => 'For SAP Upload',
		3 => 'Liquidated',
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Cmc_model', 'cmc');
		$this->load->model('Rerfo_model', 'rerfo');
		$this->load->model('Sales_model', 'sale');
		$this->load->model('Fund_model', 'fund');
	}

	public function load($tid)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$tid)->row();
		$topsheet->fund = $this->fund->get_company_cash($topsheet->region, $topsheet->company);
		$topsheet->region = $this->region[$topsheet->region];
		$topsheet->company = $this->company[$topsheet->company];
		$topsheet->date = substr($topsheet->date, 0, 10);

		$topsheet->sales = $this->db->query("select tbl_rerfo.branch,
				tbl_rerfo.date,
				sum(amount) as amount,
				sum(registration + tip) as expense
			from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			inner join tbl_rerfo on rerfo = rid
			where topsheet = ".$tid."
			group by rerfo")->result_object();

		$topsheet->total_expense = 0;
		$topsheet->total_credit = 0;

		foreach ($topsheet->sales as $key => $sales)
		{
			$sales->branch = $this->cmc->get_branch($sales->branch);
			$sales->date = substr($sales->date, 0, 10);
			$topsheet->sales[$key] = $sales;

			$topsheet->total_expense += $sales->expense;
			$topsheet->total_credit += $sales->amount;
		}

		$topsheet->total_misc = ($topsheet->meal
			+ $topsheet->photocopy
			+ $topsheet->transportation
			+ $topsheet->others);
		$topsheet->total_balance = $topsheet->total_credit - 
			($topsheet->total_expense + $topsheet->total_misc);

		// users info
		$topsheet->user = $this->cmc->get_user_info($topsheet->user);

		// load files
		$this->load->helper('directory');
		$topsheet->files = directory_map('./rms_dir/misc/'.$topsheet->tid.'_'.$topsheet->trans_no.'/', 1);

		return $topsheet;
	}

	public function get_list()
	{
		$result = $this->db->query("select * from tbl_topsheet where region =".$_SESSION['region']. " and  date >= ( CURDATE() - INTERVAL 2 DAY ) limit 1000")->result_object();
		foreach ($result as $key => $topsheet)
		{
			$topsheet->company = $this->company[$topsheet->company];
			$topsheet->status = $this->status[$topsheet->status];
			$topsheet->date = substr($topsheet->date, 0, 10);
			$topsheet->print_date = substr($topsheet->print_date, 0, 10);
			$result[$key] = $topsheet;
		}

		return $result;
	}

	public function save_misc($topsheet, $misc)
	{
		$this->db->update('tbl_topsheet', $misc, array('tid' => $topsheet->tid));

		$total_misc = $misc->meal + $misc->photocopy + $misc->transportation + $misc->others;
		$total_expense = $topsheet->total_expense + $total_misc;
		$balance = $topsheet->total_credit - $total_expense;

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('saved topsheet '.$topsheet->trans_no.' for '.$topsheet->company->name.' with details:\r\nTotal Amount Given - '.$topsheet->total_credit.'\r\nTotal Expense - '.$topsheet->total_expense.'\r\nLess Total Expense - '.$total_expense.'\r\nBalance - '.$balance.'\r\nTotal Miscellaneous - '.$total_misc);
	}

	public function print_topsheet($tid)
	{
		$this->db->query("update tbl_topsheet set print = 1, user = ".$_SESSION['uid'].", print_date = '".date('Y-m-d')."' where tid = ".$tid);
		$topsheet = $this->load($tid);

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('printed topsheet '.$topsheet->trans_no.' for '.$topsheet->region.' '.$topsheet->company.' printed by '.$topsheet->user->firstname.' '.$topsheet->user->lastname);

		return $topsheet;
	}

	public function request_reprint($tid)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$tid)->row();

		// request already sent
		if ($topsheet->print == 2) return false;

		$this->db->query("update tbl_topsheet set print = 2 where tid = ".$tid);
		$this->load->model('Login_model', 'login');
    $this->login->saveLog('requested reprinting of rerfo '.$topsheet->trans_no.' to Manager');
    return true;
	}

	public function approve_printing($key)
	{
		$this->db->query("update tbl_topsheet set print = 0 where tid = ".$key);

		$topsheet = $this->db->get_where('tbl_topsheet', array('tid' => $key))->row();
		$_SESSION['messages'][] = 'Approve request for reprinting of topsheet '.$topsheet->trans_no;

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('approved reprinting request ['.$topsheet->trans_no.']');
	}

	public function get_topsheet_request()
	{
		return $this->db->query("select * from tbl_topsheet where print = 2")->result_object();
	}

	public function search_topsheet($region, $company)
	{
		return $this->db->query("select * from tbl_topsheet
			where region = ".$region."
			and company = ".$company."
			and left(date, 10) = '".date('Y-m-d')."'")->row();
	}

	public function check_sales($tid)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$tid)->row();

		$topsheet->sales = $this->db->query("select count(*) as count from tbl_sales
			where batch = 0 and topsheet = ".$tid)->row()->count;

		if ($topsheet->sales == 0 && $topsheet->misc_status == 3)
		{
			// complete, for sap upload
			$this->db->query("update tbl_topsheet set status = 2 where tid = ".$tid);
			$topsheet->status = 2;
		}
		else
		{
			// incomplete
			$this->db->query("update tbl_topsheet set status = 1 where tid = ".$tid);
			$topsheet->status = 1;
		}

		return $topsheet;
	}

	public function get_topsheet_sales($sid)
	{
		return $this->db->query("select * from tbl_topsheet_sales where sales = ".$sid)->row();
	}

	public function get_sales_on_hold_with_remarks()
	{
		return $this->db->query("select * from tbl_topsheet_sales
			inner join tbl_topsheet on tid = topsheet
			where region = ".$_SESSION['region']."
			and hold = 1")->result_object();
	}

	public function get_sales_on_hold_with_remarks_sid($sid)
	{
		return $this->db->query("select * from tbl_topsheet_sales
			inner join tbl_topsheet on tid = topsheet
			where sales = ".$sid."
			and hold = 1")->row();
	}

	public function get_misc_on_hold_with_remarks()
	{
		return $this->db->query("select * from tbl_topsheet
			where region = ".$_SESSION['region']."
			and (select count(*) from tbl_batch
			where misc = 1 and topsheet = tid) = 0
			and (select count(*) from tbl_topsheet_misc_remarks
			where tid = topsheet) > 0")->result_object();
	}

	public function hold_topsheet_sale($sid)
	{
		$this->db->query("update tbl_topsheet_sales set hold = 1, alert = 0
	    		where sales = ".$sid);
	}

	public function update_topsheet_sales_transmittal($ttid,$sid)
	{
		return $this->db->query("update tbl_topsheet_sales
							set transmittal = ".$ttid."
							where sales in (".$sid.")");
	}

	public function get_topsheet_misc_row($tid)
	{
		return $this->db->query("select * from tbl_topsheet_misc
			where topsheet = ".$tid)->row();
	}

	public function get_misc_sum($tid)
	{
		return $this->db->query("select meal + photocopy + transportation + others as x from tbl_topsheet_misc where topsheet = ".$tid)->row()->x;
	}

	public function topsheet_status($trans_no,$print_date)
	{
		$result = $this->db->query('select *,
				(select sum(registration) + sum(tip) from tbl_sales
				where topsheet = tid) as total_expense
			from tbl_topsheet
			where region = '.$_SESSION['region'].' and
			trans_no like "%'.$trans_no.'%" and
			print_date like "%'.$print_date.'%"
			order by print_date desc, status')->result_object();

		foreach ($result as $key => $topsheet)
		{
			$topsheet->company = $this->company[$topsheet->company];
			$topsheet->date = substr($topsheet->date, 0, 10);
			$topsheet->status = $this->status[$topsheet->status];
			$topsheet->total_misc = $topsheet->meal + $topsheet->photocopy + $topsheet->transportation + $topsheet->others;

			$result[$key] = $topsheet;
		}

		return $result;
	}

	public function check_misc_records($tid)
	{
		$c = $this->db->query("select count(*) as c from tbl_topsheet_sales
			where topsheet = ".$tid." and batch = 0")->row()->c; // # of sales
		$m = $this->db->query("select count(*) as c from tbl_batch
			where topsheet = ".$tid." and misc = 1")->row()->c; // misc checked
		$t = $this->db->query("select (meal+photocopy+transportation+others) as c
			from tbl_topsheet_misc where topsheet = ".$tid)->row()->c; // misc exists
		if ($c == 0 && ($m > 0 || $t == 0)) return 1; // redirect('orcr_checking');
		else if ($m == 0 && $t > 0) return 0; // $data['set_misc'] = 1;
	}

	public function get_transmittal_row($trans_no)
	{
		return $this->db->query("select *
				from tbl_transmittal 
				where trans_no = '".$trans_no."'")->row();
	}

	public function update_transmittal($tid)
	{
		$this->db->query("update tbl_transmittal
						set status = 2,
						receive_date = '".date('Y-m-d H:i:s')."'						
						where tid = ".$tid);	
	}

	public function get_topsheet_transmittal_row($ttid)
	{
		return $this->db->query("select * from tbl_topsheet_transmittal where ttid = ".$ttid)->row();
	}

	public function get_tbl_topsheet_transmittal()
	{
		if($_SESSION['position'] == 158)
			$data['table'] = $this->db->query("select * from tbl_transmittal
				where type=2 and receive_date is null")->result_object();
		else $data['table'] = $this->db->query("select *
				from tbl_transmittal
				where branch = ".$_SESSION['branch']."
				and receive_date is null")->result_object();
		return $data['table'];
	}

	public function get_trans_no($tid)
	{
		return $this->db->query("select trans_no from tbl_topsheet where tid = ".$tid)->row()->trans_no;
	}

	public function get_trans_no_by_array($trans_no)
	{
		return $this->db->get_where('tbl_topsheet', array('trans_no' => $trans_no))->row();
	}

	public function get_batch_count($tid)
	{
		return $this->db->query("select count(*) as c from tbl_topsheet_sales
			where batch = 0 and tsid = ".$tid)->row()->c;
	}

	public function update_topsheet_status($tid)
	{
		$this->db->query("update tbl_topsheet set status = 2 where tid = ".$tid);
	}

}