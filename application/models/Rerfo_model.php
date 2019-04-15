<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Rerfo_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function list_rerfo($param)
	{
		$date_from = (empty($param->date_from)) ? date('Y-m-d', strtotime('-3 days')) : $param->date_from;
		$date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
		$branch = (!empty($param->branch) && is_numeric($param->branch))
			? " and r.bcode = '".$param->branch."'" : '';

		$print = '';
		if (!empty($param->print) && is_numeric($param->print)) {
			$print = ($param->print)
				? ' and r.print_date is not null'
				: ' and r.print_date is null';
		}

		$having_status = '';
		if (!empty($param->status)) {
			switch ($param->status) {
				case 1: $having_status = ' having max(s.topsheet) = 0 '; break;
				case 2: $having_status = ' having max(s.topsheet) = -1 '; break;
				case 3: $having_status = ' having max(s.topsheet) <> 0 and max(s.topsheet) <> -1'; break;
			}
		}

		$result = $this->db->query("select r.*,
				case max(s.topsheet) when 0 then 'Pending validation'
					when -1 then 'Pending topsheet'
					else (select trans_no from tbl_topsheet where tid = max(s.topsheet))
				end as topsheet
			from tbl_rerfo r
			inner join tbl_sales s on s.rerfo = r.rid
			where r.region = ".$param->region."
			and r.date between '".$date_from."' and '".$date_to."'
			".$branch." ".$print."
			group by r.rid
			".$having_status."
			order by r.date desc, r.print asc
			limit 1000")->result_object();

		foreach ($result as $key => $rerfo) {
			$rerfo->date = substr($rerfo->date, 0, 10);
			$rerfo->print_date = substr($rerfo->print_date, 0, 10);
			$result[$key] = $rerfo;
		}

		return $result;
	}

	public function dd_branches($region)
	{
		$result = $this->db->query('select distinct bcode, bname from tbl_sales
			where region = '.$region.'
			order by bcode')->result_object();

		$branches = array();
		foreach ($result as $branch) {
			$branches[$branch->bcode] = $branch->bcode.' '.$branch->bname;
		}
		return $branches;
	}

	public function load($rid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$rerfo = $this->db->get_where('tbl_rerfo', array('rid' => $rid))->row();
		$rerfo->date = substr($rerfo->date, 0, 10);

		$rerfo->sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where rerfo = ".$rid)->result_object();
		foreach ($rerfo->sales as $key => $sales)
		{
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$sales->total = ($sales->registration + $sales->tip);
			$sales->sales_type = ($sales->sales_type) ? 'Brand New (Installment)' : 'Brand New (Cash)';
			$rerfo->sales[$key] = $sales;
		}

		// users info
		$rerfo->users = $this->db->query("select distinct user from tbl_sales
			where rerfo = ".$rid)->result_object();
		$rerfo->user = $this->cmc->get_user_info($rerfo->user);
		foreach ($rerfo->users as $key => $row)
		{
			$row = $this->cmc->get_user_info($row->user);
			$rerfo->users[$key] = $row;
		}

		return $rerfo;
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
		$this->login->saveLog('Printed rerfo '.$rerfo->trans_no.' for branch '.$rerfo->bcode.' '.$rerfo->bname.' printed by - '.$rerfo->user->firstname.' '.$rerfo->user->lastname);

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

	public function save_validated($rid, $check)
	{
		if (empty($check)) $check = array();
		$result = $this->db->query("select sid from tbl_sales where rerfo = ".$rid)->result_object();
		foreach ($result as $row)
		{
			if (in_array($row->sid, $check)) {
				$this->db->query("update tbl_sales set topsheet = -1 where sid = ".$row->sid);
			} else {
				$this->db->query("update tbl_sales set topsheet = 0 where sid = ".$row->sid);
			}
		}
	}
}
