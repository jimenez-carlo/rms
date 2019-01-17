<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Projected_fund_model extends CI_Model{

	public $status = array(
		0 => 'For Treasury Process',
		1 => 'For Deposit',
		2 => 'Deposited',
		3 => 'Liquidated',
	);

	public $sales_type = array(
		0 => 'Brand New (Cash)',
		1 => 'Brand New (Installment)'
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
				ifnull(sum(case when left(bcode, 1) = '1' and s.voucher = 0 then 1200 else 0 end), '0.00') as voucher_1,
				ifnull(sum(case when left(bcode, 1) = '1' and s.voucher > 0 then 1200 else 0 end), '0.00') as transfer_1,
				ifnull(sum(case when left(bcode, 1) = '3' and s.voucher = 0 then 1200 else 0 end), '0.00') as voucher_3,
				ifnull(sum(case when left(bcode, 1) = '3' and s.voucher > 0 then 1200 else 0 end), '0.00') as transfer_3,
				ifnull(sum(case when left(bcode, 1) = '6' and s.voucher = 0 then 1200 else 0 end), '0.00') as voucher_6,
				ifnull(sum(case when left(bcode, 1) = '6' and s.voucher > 0 then 1200 else 0 end), '0.00') as transfer_6
			from tbl_fund f
			left join tbl_sales s
				on s.region = f.region
				and s.fund = 0
				and registration_type != 'Self Registration'
			where fid < 11
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
	public function create_voucher($fid, $cid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();

		$region = $this->reg_code[$fund->region];
		$company = ($fund->company == 2) ? 6 : $fund->company;
		$company = ($fund->company == 6) ? 8 : $fund->company;
		$fund->reference = 'CA-'.$region.'-'.date('ymd');

		$ref_code = $this->db->query("select count(*) as c from tbl_voucher
			where reference like '".$fund->reference."%'")->row()->c;
		$fund->reference .= ($ref_code == 0) ? '' : '-'.($ref_code++);
		
		$fund->transmittal = $this->db->query("select t.*,
				left(t.date, 10) as date,
				sum(1200) as amount,
				count(*) as sales
			from tbl_lto_transmittal t
			inner join tbl_sales s on lto_transmittal = ltid
			where t.region = ".$fund->region."
			and left(s.bcode, 1) = '".$cid."'
			and voucher = 0
			and registration_type != 'Self Registration'
			group by t.date, t.company")->result_object();
		
		return $fund;
	}

	public function print_projected($fid, $ltid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		
		$region = $this->reg_code[$fund->region];
		$company = ($fund->company == 2) ? 6 : $fund->company;
		$fund->reference = 'CA-'.$region.'-'.date('ymd');

		$ref_code = $this->db->query("select count(*) as c from tbl_voucher
			where reference like '".$fund->reference."%'")->row()->c;
		$fund->reference .= ($ref_code == 0) ? '' : '-'.($ref_code++);

		$fund->sales = $this->db->query("select bcode, bname, count(*) as units
			from tbl_sales
			inner join tbl_engine on eid = engine
			inner join tbl_customer on cid = customer
			where lto_transmittal in (".$ltid.")
			and voucher = 0
			and registration_type != 'Self Registration'
			group by bcode")->result_object();
		// foreach ($fund->sales as $key => $sales)
		// {
		// 	$sales->date_sold = substr($sales->date_sold, 0, 10);
		// 	$sales->sales_type = $this->sales_type[$sales->sales_type];
		// 	$fund->sales[$key] = $sales;
		// }

		$fund->region = $this->region[$fund->region];
		$fund->company = $this->company[$fund->company];
		$fund->company = '';
		return $fund;
	}

	public function save_voucher($voucher, $ltid)
	{
		$company = $this->db->query("select company from tbl_lto_transmittal where ltid in (".$ltid.")")->row();
                if (!empty($company)) {
                  $voucher->company = $company->company;
                }

		$this->db->insert('tbl_voucher', $voucher);
		$voucher->vid = $this->db->insert_id();

		$this->db->query("update tbl_sales 
			set voucher = ".$voucher->vid."
			where lto_transmittal in (".$ltid.")");

		$fund = $this->db->query("select * from tbl_fund where fid = ".$voucher->fund)->row();
		$voucher->region = $this->region[$fund->region];
		$voucher->company = $this->company[$fund->company];
		return $voucher;
	}

  /**
   * Accounting to view list of Voucher
   */
	public function list_voucher($param)
	{
		$status = (is_numeric($param->status))
			? ' and status = '.$param->status : '';
		$region = (is_numeric($param->region))
			? ' and region = '.$param->region : '';

		$result = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where date between '".$param->date_from." 00:00:00' and '".$param->date_to." 23:59:59'
			".$status.$region)->result_object();
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