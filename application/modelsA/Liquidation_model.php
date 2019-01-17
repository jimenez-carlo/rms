<?php
defined ('BASEPATH') OR exit('No direct script access allowed'); 

class Liquidation_model extends CI_Model{

	public $status = array(
		0 => 'For Transmittal',
		1 => 'LTO Rejected',
		2 => 'LTO Pending',
		3 => 'LTO Pending',
		4 => 'Registered',
		5 => 'Liquidated',
	);

	public function __construct()
	{
		parent::__construct();
	}
	
	public function load($region, $date_transferred)
	{ 
		$fund = $this->db->query("select * from tbl_fund where region = ".$region)->result_object();

		$fids = array();
		foreach ($fund as $row)
		{
			$fids[] = $row->fid;
		}
		$fids = implode(',', $fids);

		$result = $this->db->query("select *,
				(select sum(registration + tip) from tbl_sales
				 	where fund = ftid and status = 5) as liquidated,
				(select sum(registration + tip) from tbl_sales
				 	where fund = ftid and status = 4) as for_liquidation,
				(select sum(registration + tip) from tbl_sales
				 	where fund = ftid and status = 3) as lto_pending
			from tbl_fund_transfer
			where fund in (".$fids.") and
			left(date,10) like '%".$date_transferred."%'
			order by date desc
			limit 1000")->result_object();
		return $result;
	}
	
	public function load_sales($ftid)
	{
		$this->load->model('Cmc_model', 'cmc');

		$result = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where fund = ".$ftid)->result_object();

		foreach ($result as $key => $sales)
		{
			$sales->branch = $this->cmc->get_branch($sales->branch);
			$sales->status = $this->status[$sales->status];
			$result[$key] = $sales;
		}

		return $result;
	}
}