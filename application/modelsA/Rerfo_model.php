<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Rerfo_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function load($rid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$rerfo = $this->db->get_where('tbl_rerfo', array('rid' => $rid))->row();
		$rerfo->branch = $this->cmc->get_branch($rerfo->branch);
		$rerfo->date = substr($rerfo->date, 0, 10);
		$rerfo->sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where rerfo = ".$rid)->result_object();
		$rerfo->users = $this->db->query("select distinct user from tbl_sales
			where rerfo = ".$rid)->result_object();

		$rerfo->total_registration = 0;
		$rerfo->total_tip = 0;
		$rerfo->total_expense = 0;
		$rerfo->total_credit = 0;
		$rerfo->total_balance = 0;

		foreach ($rerfo->sales as $key => $sales)
		{
			$sales->branch = $this->cmc->get_branch($sales->branch);
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$sales->total = ($sales->registration + $sales->tip);
			if($sales->sales_type) $sales->sales_type = "Installment"; else $sales->sales_type = "Cash";
			$rerfo->sales[$key] = $sales;

			$rerfo->total_registration += $sales->registration;
			$rerfo->total_tip += $sales->tip;
			$rerfo->total_credit += $sales->amount;
		}

		// computations
		$rerfo->total_expense = ($rerfo->total_registration + $rerfo->total_tip);
		$rerfo->total_balance = ($rerfo->total_credit - $rerfo->total_expense);

		// users info
		$rerfo->user = $this->cmc->get_user_info($rerfo->user);
		foreach ($rerfo->users as $key => $row)
		{
			$row = $this->cmc->get_user_info($row->user);
			$rerfo->users[$key] = $row;
		}

		return $rerfo;
	}

	public function get_list($region)
	{
		$this->load->model("Cmc_model", "cmc");
		$branches = $this->cmc->get_region_branches($region);

		$result = $this->db->query("select * from tbl_rerfo
			where branch in (".$branches.")
			and date >= (CURDATE() - INTERVAL 2 DAY)
			order by date desc, print asc
			limit 1000")->result_object();

		foreach ($result as $key => $rerfo) {
			$rerfo->branch = $this->cmc->get_branch($rerfo->branch);
			$rerfo->date = substr($rerfo->date, 0, 10);
			$rerfo->print_date = substr($rerfo->print_date, 0, 10);
			$result[$key] = $rerfo;
		}

		return $result;
	}

	public function search_rerfo($branch)
	{
		return $this->db->query("select * from tbl_rerfo
			where branch = ".$branch."
			and left(date, 10) = '".date('Y-m-d')."'")->row();
	}

	public function print_rerfo($rid)
	{
		$this->db->query("update tbl_rerfo set print = 1, user = ".$_SESSION['uid'].", print_date = '".date('Y-m-d')."' where rid = ".$rid);
		$rerfo = $this->load($rid);

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('Printed rerfo '.$rerfo->trans_no.' for branch '.$rerfo->branch->b_code.' '.$rerfo->branch->name.' with details:\r\nTotal Amount Given - '.$rerfo->total_credit.'\r\nTotal LTO Registration - '.$rerfo->total_registration.'\r\nTotal LTO Tip - '.$rerfo->total_tip.'\r\nPrinted by - '.$rerfo->user->firstname.' '.$rerfo->user->lastname);

		return $rerfo;
	}

	public function request_reprint($rid)
	{
		$rerfo = $this->db->query("select * from tbl_rerfo where rid = ".$rid)->row();

		// request already sent
		if ($rerfo->print == 2) return false;

		$this->db->query("update tbl_rerfo set print = 2 where rid = ".$rid);
		$this->load->model('Login_model', 'login');
    $this->login->saveLog('requested reprinting of rerfo '.$rerfo->trans_no.' to Manager');
    return true;
	}

	public function approve_printing($key)
	{
		$this->db->query("update tbl_rerfo set print = 0 where rid = ".$key);

		$rerfo = $this->db->get_where('tbl_rerfo', array('rid' => $key))->row();
		$_SESSION['messages'][] = 'Approve request for reprinting of rerfo '.$rerfo->trans_no;

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('approved reprinting request ['.$rerfo->trans_no.']');
	}

	public function get_rerfo_request()
	{
		return $this->db->query("select * from tbl_rerfo where print = 2")->result_object();
	}
}
