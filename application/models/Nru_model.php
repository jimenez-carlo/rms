<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Nru_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
                if ($_SESSION['company'] = 8) {
                  $this->region  = $this->mdi_region;
                  $this->company = $this->mdi;
                }
	}

	public function load_list($data)
	{
		$result = $this->db->query("select t.*,
				left(date, 10) as date,
				count(*) as sales
			from tbl_lto_transmittal t
			inner join tbl_sales on lto_transmittal = ltid
			where t.region = ".$data['region']."
			and status = 2
			group by ltid
			order by date desc")->result_object();
		return $result;
	}

	public function load_sales($data)
	{
		$transmittal = $this->db->query("select *
			from tbl_lto_transmittal
			where ltid = ".$data['ltid'])->row();
		$transmittal->sales = $this->db->query("select *
			from tbl_sales
			inner join tbl_engine on eid = engine
			inner join tbl_customer on cid = customer
			where lto_transmittal = ".$data['ltid']."
			and status = 2
			order by bcode")->result_object();
		return $transmittal;
	}

	public function get_cash($data)
	{
		return $this->db->query("select cash_on_hand from tbl_fund
			where region = ".$data['region'])->row()->cash_on_hand;
	}

	public function list_check($data)
	{
		$result = $this->db->query("select c.* from tbl_check c
			inner join tbl_fund on fid = c.fund
			where region = ".$data['region']."
			and status = 0")->result_object();
		return $result;
	}

	public function load_check($data)
	{
		$cids = implode(',', $data['check']);
		$result = $this->db->query("select * from tbl_check
			where cid in (".$cids.")")->result_object();
		return $result;
	}

	public function get_account($data)
	{
		$fund = $this->db->query("select * from tbl_fund
			where region = ".$data['region'])->row();
		$fund->region_name = $this->region[$fund->region];
		$fund->company_name = $this->company[$fund->company];
		return $fund;
	}

	public function save_nru($data)
	{
		$this->load->model('Login_model', 'login');

		foreach ($data['registration'] as $sid => $registration)
		{
			$sales = new Stdclass();
		  $sales->sid = $sid;
		  $sales->registration = $registration;
		  $sales->status = 3;
			$this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));

			$engine_no = $this->db->query("select engine_no from tbl_engine
				inner join tbl_sales on eid = engine
				where sid = ".$sales->sid)->row()->engine_no;
			$this->login->saveLog('Saved Registration Expense [Php '.$registration.'] for Engine # '.$engine_no.' ['.$sid.']');
		}

		$check_tot = 0;
		if (!empty($data['check']))
		{
			$checks = $this->load_check($data);
			foreach ($checks as $check)
			{
				$this->db->query("update tbl_check set
					used_date = current_timestamp,
					status = 2
					where cid = ".$check->cid);
      	$check_tot += $check->amount;
			}
		}

		$fund = $this->get_account($data);
		$comp_cash = $fund->cash_on_hand - $data['total_regn'];
		// $comp_check = $check_tot - $data['total_regn_check'];
		$comp_check = 0;

		$new_fund = new Stdclass();
		$new_fund->fund = $fund->fund + $comp_check;
		$new_fund->cash_on_hand = $comp_cash;
		$new_fund->cash_on_check = $fund->cash_on_check - $check_tot;
		$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

		$history = new Stdclass();
		$history->fund = $fund->fid;
		$history->in_amount = $comp_check;
		$history->out_amount = $data['total_regn']; // + $data['total_regn_check'];
		$history->new_fund = $new_fund->fund;
		$history->new_hand = $new_fund->cash_on_hand;
		$history->new_check = $new_fund->cash_on_check;
		$history->type = 5;
		$this->db->insert('tbl_fund_history', $history);

		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$data['ltid'])->row();
		$_SESSION['messages'][] = "Transmittal # ".$transmittal->code." updated successfully.";
		redirect('nru');
	}
}
