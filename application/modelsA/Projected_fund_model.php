<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Projected_fund_model extends CI_Model{

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
		0 => 'For Transfer',
		1 => 'Transferred',
		2 => 'Liquidated',
	);

	public function __construct()
	{
		parent::__construct();
	}

  /**
   * View RRT Funds with Projected Funds
   */
	public function get_projected_funds()
	{
		$result = $this->db->query("select f.*,
				ifnull(sum(case when voucher = 0 then amount else 0 end), '0.00') as voucher,
				ifnull(sum(case when voucher > 0 then amount else 0 end), '0.00') as transfer
			from tbl_fund f
			left join tbl_fund_projected fp on fp.fund = fid and transfer = 0
			group by fid")->result_object();

		foreach ($result as $key => $fund)
		{
			$fund->region = $this->region[$fund->region];
			$fund->company = $this->company[$fund->company];
			$result[$key] = $fund;
		}

		return $result;
	}

  /**
   * Accounting to Create Voucher
   */
	public function create_voucher($fid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		$fund->projected = $this->db->query("select * from tbl_fund_projected
			where voucher = 0 and fund = ".$fid)->result_object();
		return $fund;
	}

	public function print_projected($fid, $fpids)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		$fund->region = $this->region[$fund->region];
		$fund->company = $this->company[$fund->company];
		$fund->projected = $this->db->query("select * from tbl_fund_projected
			where fpid in (".$fpids.")")->result_object();
		return $fund;
	}

	public function save_voucher($voucher, $fpids)
	{
		$this->db->insert('tbl_voucher', $voucher);
		$voucher->vid = $this->db->insert_id();

		foreach ($fpids as $fpid => $val)
		{
			$this->db->query("update tbl_fund_projected 
				set voucher = ".$voucher->vid." 
				where fpid = ".$fpid);
		}

		$fund = $this->db->query("select * from tbl_fund where fid = ".$voucher->fund)->row();
		$voucher->region = $this->region[$fund->region];
		$voucher->company = $this->company[$fund->company];
		return $voucher;
	}

  /**
   * Treasury to Transfer Fund
   */
	public function get_for_transfer()
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

	public function transfer_fund($vid)
	{
		$voucher = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where vid = ".$vid)->row();
		return $voucher;
	}

	public function save_transfer($voucher)
	{
		$this->load->model('Cmc_model', 'cmc');
		$this->db->update('tbl_voucher', $voucher, array('vid' => $voucher->vid));
		$voucher = $this->db->query("select * from tbl_voucher where vid = ".$voucher->vid)->row();

		// get company branches
		$fund = $this->db->query("select * from tbl_fund where fid = ".$voucher->fund)->row();
		$bcode = ($fund->company == 2) ? 6 : $fund->company;
		$branches = $this->cmc->get_company_branches($fund->region, $bcode);

		// update projected
		$this->db->query("update tbl_fund_projected set transfer = 1 
			where voucher = ".$voucher->vid);

		// update sales
		$voucher->projected = $this->db->query("select * from tbl_fund_projected
			where voucher = ".$voucher->vid)->result_object();
		foreach ($voucher->projected as $projected)
		{
			$this->db->query("update tbl_sales set fund = ".$voucher->vid."
				where projected = ".$projected->fpid);
		}

		// update fund
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

  /**
   * Accounting to view list of Voucher
   */
	public function get_vouchers($param)
	{
		$result = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where date between '".$param->date_from." 00:00:00' and '".$param->date_to." 23:59:59'")->result_object();
		foreach ($result as $key => $row)
		{
			$row->date = substr($row->date, 0, 10);
			$row->transfer_date = substr($row->transfer_date, 0, 10);
			$row->region = $this->region[$row->region];
			$row->company = $this->company[$row->company];
			$row->status = $this->status[$row->status];
			$result[$key] = $row;
		}
		return $result;
	}

  /**
   * Treasury to view list of Transferred Funds
   */
	public function get_transferred_funds($param)
	{
		$result = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where status = 1
			and transfer_date between '".$param->date_from." 00:00:00' and '".$param->date_to." 23:59:59'")->result_object();
		foreach ($result as $key => $row)
		{
			$row->date = substr($row->date, 0, 10);
			$row->transfer_date = substr($row->transfer_date, 0, 10);
			$row->region = $this->region[$row->region];
			$row->company = $this->company[$row->company];
			$row->status = $this->status[$row->status];
			$result[$key] = $row;
		}
		return $result;
	}
}