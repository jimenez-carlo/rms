<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Check_model extends CI_Model{

	public $status = array(
		0 => 'New/unused check',
		1 => 'On hold check',
		2 => 'Used check',
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function is_check_no_exists($check_no, $fid)
	{
		return $this->db->query("select * from tbl_check
			where fund = ".$fid."
			and check_no = '".$check_no."'")->num_rows();
	}

	public function list_checks($param)
	{
		$date_from = (empty($param->date_from)) ? date('Y-m-d') : $param->date_from;
		$date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
		$status = (is_numeric($param->status))
			? " and status = ".$param->status : '';

		$result = $this->db->query("select * from tbl_check c
			inner join tbl_fund on fid = c.fund
			where region = ".$_SESSION['region_id']."
			and left(check_date, 10) between '".$date_from."' and '".$date_to."'
			".$status."
			limit 1000")->result_object();

		foreach ($result as $key => $check) {
			$check->company = $this->company[$check->company];
			$check->region = $this->region[$check->region];
			$check->status = $this->status[$check->status];
			$check->check_date = substr($check->check_date, 0, 10);
			$check->hold_date = substr($check->hold_date, 0, 10);
			$check->used_date = substr($check->used_date, 0, 10);
			$result[$key] = $check;
		}

		return $result;
	}

	public function hold($check)
	{
		$check->hold_date = date('Y-m-d H:i:s');
		$check->status = 1;
		$this->db->update('tbl_check', $check, array('cid' => $check->cid));

		$check = $this->db->query("select * from tbl_check c
			inner join tbl_fund on fid = c.fund
			where cid = ".$check->cid)->row();

		$new_fund = new Stdclass();
		$new_fund->cash_on_check = $check->cash_on_check - $check->amount;
		$new_fund->check_on_hold = $check->check_on_hold + $check->amount;
		$this->db->update('tbl_fund', $new_fund, array('fid' => $check->fid));

		$history = new Stdclass();
		$history->fund = $check->fid;
		$history->out_amount = $check->amount;
		$history->new_fund = $check->fund;
		$history->new_hand = $check->cash_on_hand;
		$history->new_check = $new_fund->cash_on_check;
		$history->type = 9;
		$this->db->insert('tbl_fund_history', $history);

		// for message
		return $check;
	}

	public function unhold($cid)
	{
		$this->db->query("update tbl_check set status = 0 where cid = ".$cid);

		$check = $this->db->query("select * from tbl_check c
			inner join tbl_fund on fid = c.fund
			where cid = ".$cid)->row();

		$new_fund = new Stdclass();
		$new_fund->cash_on_check = $check->cash_on_check + $check->amount;
		$new_fund->check_on_hold = $check->check_on_hold - $check->amount;
		$this->db->update('tbl_fund', $new_fund, array('fid' => $check->fid));

		$history = new Stdclass();
		$history->fund = $check->fid;
		$history->in_amount = $check->amount;
		$history->new_fund = $check->fund;
		$history->new_hand = $check->cash_on_hand;
		$history->new_check = $new_fund->cash_on_check;
		$history->type = 9;
		$this->db->insert('tbl_fund_history', $history);

		// for message
		return $check;
	}
}
