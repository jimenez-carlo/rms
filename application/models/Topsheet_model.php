<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Topsheet_model extends CI_Model{

	public $status = array(
		0 => 'For Review',
		1 => 'For Checking',
		2 => 'Incomplete',
		3 => 'For SAP Upload',
		4 => 'Liquidated',
	);

	public $sales_type = array(
		0 => 'Brand New (Cash)',
		1 => 'Brand New (Installment)',
	);

        public function __construct() {
          parent::__construct();
          $this->load->model('Cmc_model', 'cmc');
          $this->load->model('Rerfo_model', 'rerfo');
          $this->load->model('Sales_model', 'sale');
          $this->load->model('Fund_model', 'fund');
          if ($_SESSION['company'] == 8) {
            $this->company = $this->mdi;
          }
        }

	public function list_topsheet($param)
	{
		$date_from = (empty($param->date_from)) ? date('Y-m-d', strtotime('-3 days')) : $param->date_from;
		$date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
		$status = (empty($param->status)) ? 0 : $param->status;

		$company = (empty($param->company) && is_numeric($param->company))
			? ' and company = '.$param->company : '';

		$status = '';
		if (!empty($param->status))
		{
			if (is_numeric($param->status)) {
				$status = ' and status = '.$status;
			}
			else if ($param->status == '_t') {
				$status = ' and transmittal_date is null';
			}
		}

		$result = $this->db->query("select *
			from tbl_topsheet
			where region = ".$param->region."
			and date between '".$date_from."' and '".$date_to."'
			".$company.$status."
			limit 1000")->result_object();

		foreach ($result as $key => $topsheet)
		{
			$topsheet->company = $this->company[$topsheet->company];
			$topsheet->status = $this->status[$topsheet->status];
			$topsheet->date = substr($topsheet->date, 0, 10);
			$topsheet->print_date = substr($topsheet->print_date, 0, 10);
			$topsheet->transmittal_date = substr($topsheet->transmittal_date, 0, 10);
			$result[$key] = $topsheet;
		}

		return $result;
	}

	public function pre_load($data)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$data['tid'])->row();
		$topsheet->fund = $this->fund->get_company_cash($topsheet->region, $topsheet->company);
		$topsheet->region = $this->region[$topsheet->region];
		$topsheet->company = $this->company[$topsheet->company];
		$topsheet->date = substr($topsheet->date, 0, 10);

		$sales = $this->db->query("select
				sum(amount) as amount,
				sum(registration + tip) as expense
			from tbl_sales
			where topsheet = ".$topsheet->tid)->row();

		$topsheet->total_amount = $sales->amount;
		$topsheet->total_expense = $sales->expense;

		// users info
		$topsheet->user = $this->cmc->get_user_info($topsheet->user);
		return $topsheet;
	}

	public function list_miscs($data)
	{
		$this->load->model('Expense_model', 'misc');
		$type = $this->misc->type;

		$mids = (!empty($data['mid']))
			? ' and mid in ('.implode(',', array_keys($data['mid'])).')' : '';

		$result = $this->db->query("select *
			from tbl_misc
			where region = ".$data['region']."
			".$mids."
			and status = 2")->result_object();
		foreach ($result as $key => $misc)
		{
			$misc->or_date = substr($misc->or_date, 0, 10);
			$misc->type = $type[$misc->type];
			$result[$key] = $misc;
		}
		return $result;
	}

	public function load_topsheet($data)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$data['tid'])->row();
		$topsheet->fund = $this->fund->get_company_cash($topsheet->region, $topsheet->company);
		$topsheet->region = $this->region[$topsheet->region];
		$topsheet->company = $this->company[$topsheet->company];
		$topsheet->date = substr($topsheet->date, 0, 10);

		$topsheet->sales = $this->db->query("select
				tbl_rerfo.bcode,
			 	tbl_rerfo.bname,
				tbl_rerfo.date,
				sum(amount) as amount,
				sum(registration + tip) as expense
			from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			inner join tbl_rerfo on rerfo = rid
			where topsheet = ".$topsheet->tid."
			group by rerfo")->result_object();

		$topsheet->total_expense = 0;
		$topsheet->total_credit = 0;

		foreach ($topsheet->sales as $key => $sales)
		{
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

	public function load_print($data)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$data['tid'])->row();
		$topsheet->fund = $this->fund->get_company_cash($topsheet->region, $topsheet->company);
		$topsheet->region = $this->region[$topsheet->region];
		$topsheet->company = $this->company[$topsheet->company];
		$topsheet->date = substr($topsheet->date, 0, 10);

		$topsheet->sales = $this->db->query("select
				tbl_rerfo.bcode,
				tbl_rerfo.bname,
				tbl_rerfo.date,
				sum(amount) as amount,
				sum(registration + tip) as expense
			from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			inner join tbl_rerfo on rerfo = rid
			where topsheet = ".$topsheet->tid."
			group by rerfo")->result_object();

		$topsheet->total_expense = 0;
		$topsheet->total_credit = 0;

		foreach ($topsheet->sales as $key => $sales)
		{
			$sales->date = substr($sales->date, 0, 10);
			$topsheet->sales[$key] = $sales;

			$topsheet->total_expense += $sales->expense;
			$topsheet->total_credit += $sales->amount;
		}

		if (!empty($data['mid'])) {
			$mids = implode(',', array_keys($data['mid']));
			$topsheet->misc = $this->db->query("select
					sum(case type when 1 then amount else 0 end) as meal,
					sum(case type when 2 then amount else 0 end) as photocopy,
					sum(case type when 3 then amount else 0 end) as transportation,
					sum(case type when 4 then amount else 0 end) as others,
					sum(amount) as total
				from tbl_misc
				where mid in (".$mids.")")->row();
		}
		else {
			$topsheet->misc = new Stdclass();
			$topsheet->misc->meal = 0;
			$topsheet->misc->photocopy = 0;
			$topsheet->misc->transportation = 0;
			$topsheet->misc->others = 0;
			$topsheet->misc->total = 0;
		}

		$topsheet->total_balance = $topsheet->total_credit -
			($topsheet->total_expense + $topsheet->misc->total);

		// users info
		$topsheet->user = $this->cmc->get_user_info($_SESSION['uid']);
		return $topsheet;
	}

	public function transmit_topsheet($tid)
	{
		$this->db->query("update tbl_topsheet set transmittal_date = '".date('Y-m-d')."' where tid = ".$tid);

		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$tid)->row();
		$topsheet->region = $this->region[$topsheet->region];
		$topsheet->branch = $this->db->query("select bcode, bname, sales_type
			from tbl_sales
			inner join tbl_customer on customer = cid
			where topsheet = ".$tid."
			group by bcode, bname, sales_type")->result_object();
		foreach ($topsheet->branch as $key => $branch)
		{
			$branch->sales = $this->db->query("select * from tbl_sales
				inner join tbl_customer on customer = cid
				where topsheet = ".$tid."
				and bcode = '".$branch->bcode."'
				and sales_type = ".$branch->sales_type)->result_object();
			$topsheet->branch[$key] = $branch;
		}
		return $topsheet;
	}

	public function view_topsheet($tid)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$tid)->row();
		$topsheet->date = substr($topsheet->date, 0, 10);
		// $topsheet->region = $this->region[$topsheet->region];
		// $topsheet->company = $this->company[$topsheet->company];

		// $topsheet->total_expense = 0;
		// $topsheet->total_credit = 0;
		// $topsheet->check = 0;
		// $topsheet->sales = $this->db->query("select * from tbl_sales
		// 	inner join tbl_engine on engine = eid
		// 	inner join tbl_customer on customer = cid
		// 	where topsheet = ".$topsheet->tid."
		// 	order by bcode")->result_object();
		// foreach ($topsheet->sales as $key => $sales)
		// {
		// 	$sales->date_sold = substr($sales->date_sold, 0, 10);
		// 	$sales->sales_type = $this->sales_type[$sales->sales_type];
		// 	$topsheet->sales[$key] = $sales;
		// 	$topsheet->total_expense += ($sales->registration + $sales->tip);
		// 	$topsheet->total_credit += $sales->amount;
		// }

		$topsheet->sales = $this->db->query("select rid, r.region, r.bcode, r.bname, trans_no
				, left(date, 10) as date
				, sum(amount) as amount
				, sum(registration) as registration
			from tbl_rerfo r
			inner join tbl_sales on rerfo = rid
			where topsheet = ".$tid."
			group by r.rid
			order by r.bcode")->result_object();

		$topsheet->tot_meal = $topsheet->tot_transpo = $topsheet->tot_photo = $topsheet->tot_other = 0;

		$this->load->model('Expense_model', 'misc');
		$topsheet->misc = $this->db->query("select *,
				left(or_date, 10) as or_date
			from tbl_misc
			where topsheet = ".$topsheet->tid)->result_object();
		foreach ($topsheet->misc as $misc)
		{
			switch ($misc->type) {
				case 1: $topsheet->tot_meal += $misc->amount; break;
				case 2: $topsheet->tot_photo += $misc->amount; break;
				case 3: $topsheet->tot_transpo += $misc->amount; break;
				case 4: $topsheet->tot_other += $misc->amount; break;
			}

			$misc->or_date = substr($misc->or_date, 0, 10);
			$misc->type = $this->misc->type[$misc->type];
		}

		// batch for miscellaneous
		// $topsheet->batch = $this->db->query("select * from tbl_batch
		// 	where status = 0 and topsheet = ".$topsheet->tid."
		// 	and left(post_date, 10) = '".date('Y-m-d')."'")->row();
		return $topsheet;
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

	public function hyper_save($data)
	{
		if (!empty($data['mid']))
		{
			$mids = implode(',', array_keys($data['mid']));
			$this->db->query("update tbl_misc
				set status = 3,
				topsheet = ".$data['tid']."
				where mid in (".$mids.")");
		}

		$this->db->query("update tbl_topsheet set print = 1, user = ".$_SESSION['uid'].", print_date = '".date('Y-m-d')."' where tid = ".$data['tid']);
		$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$data['tid'])->row();

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('printed topsheet '.$topsheet->trans_no);

		$_SESSION['messages'][] = "Topsheet # ".$topsheet->trans_no." saved successfully.";
		return $topsheet;
	}

	public function list_rerfo_for_topsheet()
	{
		$trans_no = 'T-'.$this->reg_code[$_SESSION['region']].'-'.date('ymd');
		$row = $this->db->query("select tid from tbl_topsheet where trans_no = '".$trans_no."'")->row();
		$tid = (!empty($row)) ? $row->tid : -2;

		$result = $this->db->query("select rid, r.region, r.bcode, r.bname, trans_no
				, left(date, 10) as date
				, sum(1500) as amount
				, sum(registration) as registration
				, sum(case when topsheet = 0 then 1 else 0 end) as c
				, sum(case when topsheet = ".$tid." then 1 else 0 end) as t
			from tbl_rerfo r
			inner join tbl_sales on rerfo = rid
				and (topsheet < 1 or topsheet = ".$tid.")
			where r.region = ".$_SESSION['region']."
			group by rid
			having c = 0 or t > 0
			order by r.bcode")->result_object();
		return $result;
	}

	public function list_misc_for_topsheet($data)
	{
		$this->load->model('Expense_model', 'misc');
		$type = $this->misc->type;

		$trans_no = 'T-'.$this->reg_code[$_SESSION['region']].'-'.date('ymd');
		$row = $this->db->query("select tid from tbl_topsheet where trans_no = '".$trans_no."'")->row();
		$tid = (!empty($row)) ? $row->tid : -2;

		$result = $this->db->query("select *
			from tbl_misc
			where topsheet = ".$tid."
			or (region = ".$data['region']."
			and topsheet = 0
			and status = 2)")->result_object();
		foreach ($result as $key => $misc)
		{
			$misc->or_date = substr($misc->or_date, 0, 10);
			$misc->type = $type[$misc->type];
			$result[$key] = $misc;
		}
		return $result;
	}

	public function list_summary_for_topsheet($data)
	{
		$this->load->model('Expense_model', 'misc');
		$type = $this->misc->type;

		$summary = new Stdclass();
		$summary->table = $this->db->query("select rid, r.region, r.bcode, r.bname, trans_no
				, left(date, 10) as date
				, sum(1500) as amount
				, sum(registration) as registration
			from tbl_rerfo r
			inner join tbl_sales on rerfo = rid
			where r.rid in (".implode(',', $data['rid']).")
			group by r.rid
			order by r.bcode")->result_object();

		$summary->tot_meal = $summary->tot_transpo = $summary->tot_photo = $summary->tot_other = 0;

		if (empty($data['mid'])) $summary->misc = array();
		else {
			$result = $this->db->query("select *
				from tbl_misc
				where mid in (".implode(',', array_keys($data['mid'])).")")->result_object();
			foreach ($result as $key => $misc)
			{
				switch ($misc->type) {
					case 1: $summary->tot_meal += $misc->amount; break;
					case 2: $summary->tot_photo += $misc->amount; break;
					case 3: $summary->tot_transpo += $misc->amount; break;
					case 4: $summary->tot_other += $misc->amount; break;
				}

				$misc->or_date = substr($misc->or_date, 0, 10);
				$misc->type = $type[$misc->type];
				$result[$key] = $misc;
			}
			$summary->misc = $result;
		}
		return $summary;
	}

	public function hyper_create($data)
	{
		$trans_no = 'T-'.$this->reg_code[$_SESSION['region']].'-'.date('ymd');
		$row = $this->db->query("select tid from tbl_topsheet where trans_no = '".$trans_no."'")->row();
		$tid = (!empty($row)) ? $row->tid : 0;

		if (empty($tid))
		{
			$topsheet = new Stdclass();
			$topsheet->region = $_SESSION['region'];
			$topsheet->company = ($_SESSION['company'] != 8) ? 1 : 8;
			$topsheet->date = date('Y-m-d');
			$topsheet->trans_no = $trans_no;
			$this->db->insert('tbl_topsheet', $topsheet);
			$tid = $this->db->insert_id();
		}

		foreach ($data['rid'] as $rid) {
			$this->db->query("update tbl_sales
				set topsheet = ".$tid."
				where rerfo = ".$rid);
		}

		if (!empty($data['mid'])) {
			foreach ($data['mid'] as $mid) {
				$this->db->query("update tbl_misc
					set topsheet = ".$tid."
					where mid = ".$mid);
			}
		}

		$_SESSION['messages'][] = $trans_no.' saved successfully';
	}
}
