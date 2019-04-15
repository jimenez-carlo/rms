<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Fund_transfer_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_voucher($vid)
	{
		$voucher = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where vid = ".$vid)->row();
		return $voucher;
	}

  /**
   * Treasury to Process Transfer
   */
	public function get_for_process()
	{
		$result = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where status = 0")->result_object();
		foreach ($result as $key => $row)
		{
			$row->date = substr($row->date, 0, 10);
			$row->region = $this->region[$row->region];
			$row->company = $this->company[$row->company];
			$result[$key] = $row;
		}
		return $result;
	}

  /**
   * Treasury to Transfer Fund
   */
	public function get_for_transfer()
	{
		$result = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where status = 1")->result_object();
		foreach ($result as $key => $row)
		{
			$row->date = substr($row->date, 0, 10);
			$row->region = $this->region[$row->region];
			$row->company = $this->company[$row->company];
			$result[$key] = $row;
		}
		return $result;
	}

	public function save_transfer($voucher)
	{
		$this->load->model('Cmc_model', 'cmc');
		$this->db->update('tbl_voucher', $voucher, array('vid' => $voucher->vid));
		$voucher = $this->db->query("select * from tbl_voucher where vid = ".$voucher->vid)->row();

		// update sales
		$this->db->query("update tbl_sales
			set fund = ".$voucher->vid."
			where voucher = ".$voucher->vid);

		// update fund
		$fund = $this->db->query("select * from tbl_fund where fid = ".$voucher->fund)->row();
		$new_fund = new Stdclass();
		$new_fund->fund = $fund->fund + $voucher->amount;
		$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

		$history = new Stdclass();
		$history->fund = $fund->fid;
		$history->in_amount = $voucher->amount;
		$history->new_fund = $new_fund->fund;
		$history->new_hand = $fund->cash_on_hand;
		$history->new_check = $fund->cash_on_check;
		$history->type = 1;
		$this->db->insert('tbl_fund_history', $history);

		// for message
		$voucher->region = $this->region[$fund->region];
		$voucher->company = $this->company[$fund->company];
		return $voucher;
	}
}