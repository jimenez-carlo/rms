<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Fund_transfer_model extends CI_Model{

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

	public function __construct()
	{
		parent::__construct();
	}

	public function get_for_transfer()
	{
		$result = $this->db->query("select f.*,
				ifnull(sum(case when voucher = 0 then amount else 0 end), '0.00') as voucher,
				ifnull(sum(case when voucher > 0 then amount else 0 end), '0.00') as transfer
			from tbl_fund f
			left join tbl_fund_projected fp on fp.fund = fid and fund_transfer = 0
			group by fid")->result_object();

		foreach ($result as $key => $fund)
		{
			$fund->region = $this->region[$fund->region];
			$fund->company = $this->company[$fund->company];
			$result[$key] = $fund;
		}

		return $result;
	}

	public function get_rrt_fund2($fid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid=".$fid)->row();
		$fund->region = $this->region[$fund->region];
		$fund->company = $this->company[$fund->company];

		return $fund;
	}

	public function get_projected($fid)
	{
		$result = $this->db->query("select * from tbl_fund_projected
			where fund_transfer = 0 and fund = ".$fid)->result_object();
		return $result;
	}

	public function save_fund($fid,$fund)
	{
		$this->db->update('tbl_fund', $fund, array('fid' => $fid));
	}

	public function save($fund_transfer)
	{
		$this->load->model('Cmc_model', 'cmc');
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fund_transfer->fund)->row();
		
		$bcode = ($fund->company == 2) ? 6 : $fund->company;
		$branches = $this->cmc->get_company_branches($fund->region, $bcode);

		$this->db->insert('tbl_fund_transfer', $fund_transfer);
		$fund_transfer->ftid = $this->db->insert_id();

		$fpid = $this->input->post('fpid');
		foreach ($fpid as $key => $val)
		{
			$projected = new Stdclass();
			$projected->fund_transfer = $fund_transfer->ftid;
			$this->db->update('tbl_fund_projected', $projected, array('fpid' => $key));

			$projected = $this->db->query("select date from tbl_fund_projected
				where fpid = ".$key)->row();
			$this->db->query("update tbl_sales set fund = ".$fund_transfer->ftid."
				where left(transmittal_date, 10) = '".$projected->date."'
				and branch in (".$branches.")");
		}

		// update fund
		$new_fund = new Stdclass();
		$new_fund->fund = $fund->fund + $fund_transfer->amount;
		$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

		$history = new Stdclass();
		$history->fund = $fund->fid;
		$history->in_amount = $fund_transfer->amount;
		$history->new_fund = $new_fund->fund;
		$history->new_hand = $fund->cash_on_hand;
		$history->new_check = $fund->cash_on_check;
		$history->type = 1;
		$this->db->insert('tbl_fund_history', $history);

		// for report
		$fund_transfer->region = $this->region[$fund->region];
		$fund_transfer->company = $this->region[$fund->company];
		return $fund_transfer;
	}

	public function get_ft_row($ftid)
	{
		return $this->db->query("select * from tbl_fund_transfer where ftid = ".$ftid)->row();
	}

	public function get_fund_projected($ftid)
	{
		return $this->db->query("select * from tbl_fund_projected where fund_transfer = ".$ftid)->result_object();
	}

	public function get_fund_transfer($region, $company)
	{
		$fund = $this->db->query("select * from tbl_fund
			where region = ".$region."
			and company = ".$company)->row();

		$result = $this->db->query("select * from tbl_fund_transfer ft
			inner join tbl_fund on fid = ft.fund
			where fid = ".$fund->fid)->result_object();

		foreach ($result as $key => $fund_transfer) {
			$fund_transfer->date = substr($fund_transfer->date, 0, 10);

			$fund_transfer->projected = $this->db->query("select *
				from tbl_fund_projected
				where fund_transfer = ".$fund_transfer->ftid)->result_object();
			foreach ($fund_transfer->projected as $key => $row)
			{
				$row->date = substr($row->date, 0, 10);
				$fund_transfer->projected[$key] = $row;
			}

			$result[$key] = $fund_transfer;
		}

		return $result;
	}
}