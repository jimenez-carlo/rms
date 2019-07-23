<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Disapprove_model extends CI_Model{

	public $da_reason = array(
		1  => 'Wrong Amount',
		2  => 'No (AR/SI) reference',
		3  => 'Invalid (AR/SI) reference',
		4  => 'Unreadable attachment',
		5  => 'Missing OR attachment',
		6  => 'Mismatch Customer Name',
		7  => 'Mismatch Engine #',
		8  => 'Mismatch CR #',
		9  => 'Wrong Tagging',
                10 => 'Wrong Regn Type'
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function branch_list($param)
	{
		$result = $this->db->query("select distinct bcode, bname from tbl_sales
			where region = ".$param->region."
			and da_reason > 0
			order by bcode")->result_object();

		$branches = array();
		foreach($result as $row) {
			$branches[$row->bcode] = $row->bcode.' '.$row->bname;
		}

		return $branches;
	}

	public function load_list($param)
	{
		if (empty($param->branch)) $branch = "";
		else $branch = " and s.bcode = '".$param->branch."'";

		return $this->db->query("select s.*, e.*, c.*, t.trans_no
			from tbl_sales s
			inner join tbl_engine e on engine = eid
			inner join tbl_customer c on customer = cid
			inner join tbl_topsheet t on topsheet = tid
			where s.region = ".$param->region."
			".$branch."
			and s.da_reason > 0
			order by s.bcode")->result_object();
	}

	public function load_sales($sid)
	{
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where sid = ".$sid)->row();
		$sales->da_reason = $da_reason[$sales->da_reason];
		return $sales;
	}
}
