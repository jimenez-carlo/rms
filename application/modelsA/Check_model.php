<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Check_model extends CI_Model{

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

	public function is_check_no_exists($check_no, $company)
	{
		return $this->db->query("select * from tbl_check where check_no = '$check_no' and company = $company and region = ".$_SESSION['region'])->num_rows();
	}

	public function get_checks($check_no, $company)
	{
		$checks = $this->db->query("select * from tbl_check where check_no like '%$check_no%' and company like '%$company%' and region = ".$_SESSION['region']." limit 1000")->result_object();

		foreach ($checks as $key => $value) {
			$checks[$key]->company = $this->company[$value->company];
			$checks[$key]->region = $this->region[$value->region];
		}

		return $checks;
	}

	public function save($check)
	{
		$this->db->insert('tbl_check', $check);
	}

	public function save_hold_reason($hold)
	{
		$this->db->insert('tbl_check_hold', $hold);
	}

	public function update_hold($status,$cid)
	{
		$this->db->query("update tbl_check set hold = $status where cid = $cid");
	}

}