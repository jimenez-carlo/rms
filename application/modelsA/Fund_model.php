<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Fund_model extends CI_Model{

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

	public $history_type = array(
		1 => 'Transfer',
		2 => 'Cash Withdrawal',
		3 => 'Check Withdrawal',
		4 => 'Deposit',
		5 => 'NRU',
		6 => 'Registration',
		7 => 'Miscellaneous'
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function load($fid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		$fund->region_name = $this->region[$fund->region];
		$fund->company_name = $this->company[$fund->company];
		return $fund;
	}

	public function load2($region)
	{
		return $this->db->query("select * from tbl_fund where region = ".$region)->result_object();
	}

	public function get_fund_transfer($fid)
	{
		return $this->db->query("select * from tbl_fund_transfer where amount > 0 and fund = ".$fid)->result_object();
	}

	public function get_company_region($fid)
	{
		$fund = $this->db->query("select region,company from tbl_fund where fid=".$fid)->row();
		$fund->region = $this->region[$fund->region];
		$fund->company = $this->company[$fund->company];
		return $fund;
	}

	public function load_rrt_fund($region)
	{
		$this->load->model('Cmc_model', 'cmc');

		$result = $this->db->query("select * from tbl_fund
			where region = ".$region)->result_object();
		foreach ($result as $key => $fund)
		{
			$bcode = ($fund->company == 2) ? 6 : $fund->company;
			$branches = $this->cmc->get_company_branches($region, $bcode);

			if (!empty($branches))
			{
				$row = $this->db->query("select
						ifnull(sum(
							case when status = 3 then registration else 0 end
						), 0) as lto_pending,
						ifnull(sum(
							case when status > 3 then registration else 0 end
						), 0) as for_liquidation
					from tbl_sales
					where status > 2 and status < 7
					and branch in (".$branches.")")->row();
				$fund->lto_pending = $row->lto_pending;
				$fund->for_liquidation = $row->for_liquidation;
			}
			else
			{
				$fund->lto_pending = 0;
				$fund->for_liquidation = 0;
			}

			$fund->region = $this->region[$fund->region];
			$fund->company_cid = $fund->company;
			$fund->company = $this->company[$fund->company];
			$result[$key] = $fund;
		}
		return $result;
	}

	public function save_rrt_transaction($transaction)
	{
		$this->db->insert('tbl_fund_transaction', $transaction);
		$fund = $this->db->query("select * from tbl_fund
			where fid = ".$transaction->fund)->row();

		switch ($transaction->type)
		{
			case 1:
				$new_fund = new Stdclass();
				$new_fund->fund = $fund->fund - $transaction->amount;
				$new_fund->cash_on_hand = $fund->cash_on_hand + $transaction->amount;
				$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

				$history = new Stdclass();
				$history->out_amount = $transaction->amount;
				$history->new_fund = $new_fund->fund;
				$history->new_hand = $new_fund->cash_on_hand;
				$history->new_check = $fund->cash_on_check;
				$history->type = 2;
				$this->db->insert('tbl_fund_history', $history);
				break;
			case 2:
				$new_fund = new Stdclass();
				$new_fund->fund = $fund->fund - $transaction->amount;
				$new_fund->cash_on_check = $fund->cash_on_check + $transaction->amount;
				$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

				$history = new Stdclass();
				$history->out_amount = $transaction->amount;
				$history->new_fund = $new_fund->fund;
				$history->new_hand = $fund->cash_on_hand;
				$history->new_check = $new_fund->cash_on_check;
				$history->type = 3;
				$this->db->insert('tbl_fund_history', $history);
				break;
			case 3:
				$new_fund = new Stdclass();
				$new_fund->fund = $fund->fund + $transaction->amount;
				$new_fund->cash_on_hand = $fund->cash_on_hand - $transaction->amount;
				$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));
				
				$history = new Stdclass();
				$history->in_amount = $transaction->amount;
				$history->new_fund = $new_fund->fund;
				$history->new_hand = $new_fund->cash_on_hand;
				$history->new_check = $fund->cash_on_check;
				$history->type = 4;
				$this->db->insert('tbl_fund_history', $history);
				break;
		}
	}

	public function save_rrt_check($check)
	{
		$this->db->insert('tbl_check', $check);
	}

	public function save_nru($region, $cash_a, $check_a)
	{
		$result = $this->db->query("select * from tbl_fund
			where region = ".$region)->result_object();

		foreach ($result as $fund)
		{
			$cash = $cash_a[$fund->company];
			$check = $check_a[$fund->company];

			$new_fund = new Stdclass();
			$new_fund->fund = $fund->fund + ($fund->cash_on_check - $check);
			$new_fund->cash_on_hand = $fund->cash_on_hand - $cash;
			$new_fund->cash_on_check = 0;
			$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

			$history = new Stdclass();
			$history->fund = $fund->fid;
			$history->in_amount = ($fund->cash_on_check - $check);
			$history->out_amount = $cash + $check;
			$history->new_fund = $new_fund->fund;
			$history->new_hand = $new_fund->cash_on_hand;
			$history->new_check = $new_fund->cash_on_check;
			$history->type = 5;
			$this->db->insert('tbl_fund_history', $history);
		}
	}

	public function save_registration($branch, $new_hand)
	{
		$fund = $this->db->query("select * from tbl_fund
			where region = ".$branch->region."
			and company = ".$branch->company)->row();

		$new_fund = new Stdclass();
		$new_fund->cash_on_hand = $new_hand;
		$this->db->update("tbl_fund", $new_fund, array("fid" => $fund->fid));

		$history = new Stdclass();
		$history->new_fund = $fund->fund;
		$history->new_hand = $new_fund->cash_on_hand;
		$history->new_check = $fund->cash_on_check;
		$history->type = 6;

		if ($fund->cash_on_hand > $new_fund->cash_on_hand)
			$history->out_amount = $fund->cash_on_hand - $new_fund->cash_on_hand;
		else
			$history->in_amount = $new_fund->cash_on_hand - $fund->cash_on_hand;
		$this->db->insert('tbl_fund_history', $history);
	}

	public function save_misc($topsheet, $new_hand)
	{
		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$topsheet->tid)->row();
		$fund = $this->db->query("select * from tbl_fund
			where region = ".$topsheet->region."
			and company = ".$topsheet->company)->row();

		$new_fund = new Stdclass();
		$new_fund->cash_on_hand = $new_hand;
		$this->db->update("tbl_fund", $new_fund, array("fid" => $fund->fid));

		$history = new Stdclass();
		$history->new_fund = $fund->fund;
		$history->new_hand = $new_fund->cash_on_hand;
		$history->new_check = $fund->cash_on_check;
		$history->type = 7;

		if ($fund->cash_on_hand > $new_fund->cash_on_hand)
			$history->out_amount = $fund->cash_on_hand - $new_fund->cash_on_hand;
		else
			$history->in_amount = $new_fund->cash_on_hand - $fund->cash_on_hand;
		$this->db->insert('tbl_fund_history', $history);
	}

	public function get_name($fid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		return $this->region[$fund->region].' '.$this->company[$fund->company];
	}

	public function get_m_balance($fid)
	{
		return $this->db->query("select m_balance from tbl_fund where fid=".$fid)->row()->m_balance;
	}

	public function get_cash_in_bank($fid)
	{
		return $this->db->query("select fund from tbl_fund
			where fid = ".$fid)->row()->fund;
	}

	public function get_cash_on_hand($fid)
	{
		return $this->db->query("select cash_on_hand from tbl_fund
			where fid = ".$fid)->row()->cash_on_hand;
	}

	public function get_cash_on($type)
	{
		$cash = array();

		$result = $this->db->query("select * from tbl_fund
			where region = ".$_SESSION['region'])->result_object();
		foreach ($result as $fund) {
			if($type == "check") $cash[$fund->company] = $fund->cash_on_check;
			else $cash[$fund->company] = $fund->cash_on_hand;
		}

		return $cash;
	}

	public function get_total_cash_on_hand()
	{
		return $this->db->query("select sum(cash_on_hand) as cash_on_hand from tbl_fund where region = ".$_SESSION['region']."
			group by region")->row()->cash_on_hand;
	}

	public function get_company_cash($region, $company)
	{
		return $this->db->query("select cash_on_hand from tbl_fund
			where region = ".$region."
			and company = ".$company)->row()->cash_on_hand;
	}

	public function get_rrt_funds()
	{
		$result = $this->db->query("select * from tbl_fund")->result_object();
		foreach ($result as $key => $row)
		{
			$row->region = $this->region[$row->region];
			$row->company = $this->company[$row->company];
			$result[$key] = $row;
		}
		return $result;
	}
}