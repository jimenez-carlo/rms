<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Nru_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function load_sales($region)
	{
		$this->load->model('Cmc_model', 'cmc');
		$branches = $this->cmc->get_region_branches($region);

		$result = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where status = 2
			and branch in (".$branches.")
			order by pending_date asc
			limit 1000")->result_object();
		foreach ($result as $key => $row)
		{
			$row->branch = $this->cmc->get_branch($row->branch);
			$row->pending_date = substr($row->pending_date, 0, 10);
			$result[$key] = $row;
		}

		return $result;
	}
}