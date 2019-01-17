<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model{

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
	
	public $status = array(
		0 => 'For Approval',
		1 => 'Approved',
		2 => 'Rejected',
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function load($mid)
	{
		$misc = $this->db->query("select * from tbl_misc where mid=".$mid)->row();
		$misc->status = $this->status[$misc->status];

		return $misc;
	}

	public function get_miscs($or_no, $or_date, $status)
	{
		if($status == "rejected") $status_clause = " and status=2 ";
		else $status_clause = "";

		$miscs = $this->db->query("select * from tbl_misc where or_no like '%$or_no%' and left(or_date,10) like '%$or_date%' and region = ".$_SESSION['region'].$status_clause." order by or_date desc limit 1000")->result_object();

		foreach ($miscs as $key => $misc) {
			$miscs[$key]->status = $this->status[$misc->status];
		}

		return $miscs;
	}

	public function add($misc)
	{
		$this->db->insert('tbl_misc', $misc);
		$misc->mid = $this->db->insert_id();
	}

	public function save_filename($mid,$filename)
	{
		$this->db->query("update tbl_misc set filename = '$filename' where mid = $mid");
	}

	public function save_status($status,$mid)
	{
		$this->db->query("update tbl_misc set status = $status where mid = $mid");
	}

	public function update_hold($status,$cid)
	{
		$this->db->query("update tbl_check set hold = $status where cid = $cid");
	}

}