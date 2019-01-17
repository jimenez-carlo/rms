<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Lto_pending_model extends CI_Model{

	public $cust_type = array(
		0 => 'Individual',
		1 => 'Organizational'
	);

	public $status = array(
		0 => 'New',
		1 => 'Incomplete',
		2 => 'Done'
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function load_list($region)
	{
		$this->load->model('Cmc_model', 'cmc');
		$branches = $this->cmc->get_region_branches($region);

		$result = $this->db->query("select *,
				(select count(*) from tbl_sales
				where status < 2 and lto_transmittal = ltid) as sales_count,
				(select count(*) from tbl_sales
				where registration_type = 'Self Registration'
				and lto_transmittal = ltid) as self_reg
			from tbl_lto_transmittal
			where status < 2
			and branch in (".$branches.")
			order by date asc
			limit 1000")->result_object();
		foreach ($result as $key => $row)
		{
			$row->date = substr($row->date, 0, 10);
			$row->branch = $this->cmc->get_branch($row->branch);
			$row->cust_type = $this->cust_type[$row->cust_type];
			$row->status = $this->status[$row->status];

			if ($row->sales_count == $row->self_reg)
				$row->sales_count .= ' (Self Registration)';

			$result[$key] = $row;
		}

		return $result;
	}

	public function load_transmittal($ltid)
	{
		$this->load->model('Cmc_model', 'cmc');

		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$ltid)->row();
		$transmittal->date = substr($transmittal->date, 0, 10);
		$transmittal->branch = $this->cmc->get_branch($transmittal->branch);
		$transmittal->cust_type = $this->cust_type[$transmittal->cust_type];

		$transmittal->sales = $this->db->query("select *
			from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where status < 2
			and lto_transmittal = ".$ltid)->result_object();
		foreach ($transmittal->sales as $key => $sales)
		{
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$transmittal->sales[$key] = $sales;
		}

		return $transmittal;
	}

	public function save_transmittal($ltid)
	{
		$sales = $this->db->query("select count(*) as count from tbl_sales
			where status < 2
			and lto_transmittal = ".$ltid)->row()->count;
		$status = ($sales == 0) ? 2 : 1;

		$this->db->query("update tbl_lto_transmittal
			set status = ".$status."
			where ltid = ".$ltid);

		// message
		$this->load->model('Cmc_model', 'cmc');
		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$ltid)->row();
		$branch = $this->cmc->get_branch($transmittal->branch);
		$_SESSION['messages'][] = "LTO Transmittal for ".$branch->b_code." ".$branch->name." was updated successfully.";
	}

	public function get_reasons()
	{
		$this->load->model('Sales_model', 'sales');
		return $this->sales->lto_reason;
	}
}