<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Fund_model extends CI_Model{

	public $history_type = array(
		1 => 'Transfer',
		2 => 'Cash Withdrawal',
		3 => 'Check Withdrawal',
		4 => 'Deposit',
		5 => 'NRU',
		6 => 'Registration',
		7 => 'Miscellaneous',
		8 => 'Maintaining Balance',
		9 => 'Check Hold/Unhold',
	);

	public function __construct()
	{
		parent::__construct();
                if ($_SESSION['company'] != 8) {
                  $this->companyQry = ' company != 8';
                } else {
                  $this->companyQry = ' company = 8';
                  $this->region  = $this->mdi_region;
                  $this->company = $this->mdi;
                }
	}

	public function load($fid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		$fund->region_name = $this->region[$fund->region];
		$fund->company_name = $this->company[$fund->company];
		return $fund;
	}

	public function load_all()
	{
		$result = $this->db->query("SELECT * FROM tbl_fund WHERE $this->companyQry")->result_object();
		foreach ($result as $fund)
		{
			$fund->region_name  = $this->region[$fund->region];
			$fund->company_name = $this->company[$fund->company];
		}
		return $result;
	}

	public function update_fund_dtls($funds, $new_funds)
	{
		foreach ($funds as $fund)
		{
			$new_fund = $new_funds[$fund->fid];
			$balance = $new_fund->m_balance - $fund->m_balance;
			if ($balance != 0) $new_fund->fund = $fund->fund + $balance;
			$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

			$history = new Stdclass();
			$history->fund = $fund->fid;
			$history->in_amount = $new_fund->m_balance;
			$history->out_amount = $fund->m_balance;
			$history->new_fund = $new_fund->fund;
			$history->new_hand = $fund->cash_on_hand;
			$history->new_check = $fund->cash_on_check;
			$history->type = 8;
			$this->db->insert('tbl_fund_history', $history);
		}
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

		$result = $this->db->query("SELECT * FROM tbl_fund WHERE region = ".$region)->result_object();
                $row = array(
                  'lto_pending' => 0,
                  'for_liquidation' => 0,
                  'misc_expense_amount' => 0
                );

		foreach ($result as $key => $fund)
		{
                        $sql = <<<QRY
			  SELECT
                            IFNULL(
			      SUM(
				CASE
				  WHEN s.status = 3 THEN s.registration
				  ELSE 0
				END
			      ), 0
		            ) AS lto_pending,
                            IFNULL(
                              SUM(
                                CASE
                                  WHEN s.status = 4 THEN s.registration + s.tip
                                  ELSE 0
                                END), 0
                            ) AS for_liquidation
		            ,IFNULL(CASE
			      WHEN mxh1.status IN (2,3) THEN m.amount
                              ELSE 0
		            END, 0) AS misc_expense_amount
                          FROM
                            tbl_sales s
                          LEFT JOIN
                            tbl_voucher v ON v.vid = s.voucher
                          LEFT JOIN
                            tbl_misc m ON m.ca_ref = v.vid
                          LEFT JOIN
                            tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
                          LEFT JOIN
                            tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
                          WHERE
                            s.status > 2 AND s.status < 7 AND s.voucher > 0 AND s.region = $region AND mxh2.id IS NULL
                          GROUP BY
                            m.mid, misc_expense_amount
QRY;

                        //OLD QUERY
                        //SELECT
			//    IFNULL(SUM(
			//    	CASE WHEN status = 3 THEN registration ELSE 0 end
			//    ), 0) as lto_pending,
			//    IFNULL(SUM(
			//    	CASE WHEN status > 3 THEN registration+tip ELSE 0 end
			//    ), 0) AS for_liquidation
			//FROM tbl_sales
			//WHERE status > 2 AND status < 7
                        //AND region = ".$region
                        $get_result = $this->db->query($sql)->row_array();

                        if (!empty($get_result)) {
                          $row = $get_result;
                        }

			$fund->lto_pending = $row['lto_pending'];
			$fund->for_liquidation = $row['for_liquidation'] + $row['misc_expense_amount'];
			$fund->region = ($_SESSION['company'] != 8) ? $this->region[$fund->region] : $this->mdi_region[$fund->region];
			$fund->company_cid = $fund->company;
                        $fund->company = ($_SESSION['company'] != 8) ? $this->company : $this->mdi;
                        $result[$key] = $fund;
		}
		return $result;
	}

	public function save_rrt_transaction($transaction, $check)
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
				$history->fund = $fund->fid;
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

				$this->db->insert('tbl_check', $check);

				$history = new Stdclass();
				$history->fund = $fund->fid;
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
				$history->fund = $fund->fid;
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

	public function save_registration($branch, $new_hand)
	{
		$fund = $this->db->query("select * from tbl_fund
			where region = ".$branch->region."
			and company = ".$branch->company)->row();

		$new_fund = new Stdclass();
		$new_fund->cash_on_hand = $new_hand;
		$this->db->update("tbl_fund", $new_fund, array("fid" => $fund->fid));

		$history = new Stdclass();
		$history->fund = $fund->fid;
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
		$history->fund = $fund->fid;
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
			where region = ".$region)->row()->cash_on_hand;
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
