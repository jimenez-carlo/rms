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
          $fund = $this->db->query("SELECT * FROM tbl_fund WHERE fid = {$_SESSION['fund_id']}")->row_array();

          $result = array(
            'lto_pending' => 0,
            'liquidated' => 0,
            'for_liquidation' => 0,
          );

          $sql = <<<QRY
            SELECT
            	'lto_pending' AS label,
            	IFNULL(SUM(IF(status = 3, registration, 0)), 0) AS amount
            FROM
              tbl_sales
            WHERE
            	region = {$region}
            UNION
	    SELECT
	      'for_liquidation' AS label,
              IFNULL(SUM(registration+tip), 0)
              + ANY_VALUE(misc_for_liq_amount)
              + ANY_VALUE(return_fund_for_liq_amount) AS amount
	    FROM
	      tbl_sales s
            JOIN (
              SELECT
	        IFNULL(SUM(m.amount), 0) AS misc_for_liq_amount
	      FROM
	        tbl_voucher v
	      LEFT JOIN
	        tbl_misc m ON m.ca_ref = v.vid
	      INNER JOIN
	        tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
	      INNER JOIN
	        tbl_status st ON st.status_id = mxh1.status AND st.status_type = 'MISC_EXP'
	      LEFT JOIN
	        tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
	      WHERE
	        mxh2.id IS NULL AND v.fund = {$_SESSION['fund_id']} AND st.status_name IN ('Approved', 'Resolved', 'For Liquidation')
	    ) AS misc_exp
            JOIN (
              SELECT
	        SUM(rf.amount) AS return_fund_for_liq_amount
	      FROM
	        tbl_voucher v
	      INNER JOIN
	        tbl_return_fund rf ON v.vid = rf.fund
	      INNER JOIN
	        tbl_return_fund_history rfh_1 ON rfh_1.rfid = rf.rfid
	      INNER JOIN
	        tbl_status st ON st.status_id = rfh_1.status_id AND st.status_type = 'RETURN_FUND'
	      LEFT JOIN
	        tbl_return_fund_history rfh_2 ON rfh_1.rfid = rfh_2.rfid AND rfh_1.return_fund_history_id < rfh_2.return_fund_history_id
	      WHERE
	        rfh_2.return_fund_history_id IS NULL AND v.fund = {$_SESSION['fund_id']} AND st.status_name = 'For Liquidation'
	    ) AS return_fund
            WHERE
                s.region = {$region} AND s.status = 4
            UNION
	    SELECT
	      'liquidated' AS label,
              IFNULL(SUM(registration+tip), 0)
              + ANY_VALUE(misc_liq_amount)
              + ANY_VALUE(return_fund_liq_amount) AS amount
	    FROM
	      tbl_sales s
            JOIN (
              SELECT
	        IFNULL(SUM(m.amount), 0) AS misc_liq_amount
	      FROM
	        tbl_voucher v
	      LEFT JOIN
	        tbl_misc m ON m.ca_ref = v.vid
	      INNER JOIN
	        tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
	      INNER JOIN
	        tbl_status st ON st.status_id = mxh1.status AND st.status_type = 'MISC_EXP'
	      LEFT JOIN
	        tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
	      WHERE
	        mxh2.id IS NULL AND v.fund = {$_SESSION['fund_id']} AND st.status_name IN ('Liquidated')
	    ) AS misc_exp
            JOIN (
              SELECT
	        SUM(rf.amount) AS return_fund_liq_amount
	      FROM
	        tbl_voucher v
	      INNER JOIN
	        tbl_return_fund rf ON v.vid = rf.fund
	      INNER JOIN
	        tbl_return_fund_history rfh_1 ON rfh_1.rfid = rf.rfid
	      INNER JOIN
	        tbl_status st ON st.status_id = rfh_1.status_id AND st.status_type = 'RETURN_FUND'
	      LEFT JOIN
	        tbl_return_fund_history rfh_2 ON rfh_1.rfid = rfh_2.rfid AND rfh_1.return_fund_history_id < rfh_2.return_fund_history_id
	      WHERE
	        rfh_2.return_fund_history_id IS NULL AND v.fund = {$_SESSION['fund_id']} AND st.status_name = 'Liquidated'
	    ) AS return_fund
            WHERE
                s.region = {$region} AND s.status = 5
QRY;
          $get_result = $this->db->query($sql)->result_array();

          foreach ($get_result as $amount) {
            $result[$amount['label']] = $amount['amount'];
          }

          $final_fund = array_merge($result, $fund);

          return $final_fund;
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
			where region = ".$_SESSION['region_id'])->result_object();
		foreach ($result as $fund) {
			if($type == "check") $cash[$fund->company] = $fund->cash_on_check;
			else $cash[$fund->company] = $fund->cash_on_hand;
		}

		return $cash;
	}

	public function get_total_cash_on_hand()
	{
		return $this->db->query("select sum(cash_on_hand) as cash_on_hand from tbl_fund where region = ".$_SESSION['region_id']."
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
