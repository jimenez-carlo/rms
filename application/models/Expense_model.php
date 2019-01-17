<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model{
	
	public $type = array(
		1 => 'Meal',
		2 => 'Photocopy',
		3 => 'Transportation',
		4 => 'Others',
	);
	
	public $status = array(
		0 => 'For Approval',
		1 => 'Rejected',
		2 => 'Approved',
		3 => 'For Liquidation',
		4 => 'Liquidated',
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function list_misc($param)
	{
		$date_from = (empty($param->date_from)) ? date('Y-m-d') : $param->date_from;
		$date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
		$type = (!empty($param->type) && is_numeric($param->type))
			? ' and type = '.$param->type : '';
		$status = (is_numeric($param->status))
			? ' and status = '.$param->status : '';

		$result = $this->db->query("select * from tbl_misc
			where region = ".$param->region."
			and left(or_date,10) between '".$date_from."' and '".$date_to."'
			".$type.$status."
			order by or_date desc limit 1000")->result_object();

		foreach ($result as $key => $misc) {
			$misc->edit = ($misc->status < 2);
			$misc->or_date = substr($misc->or_date, 0, 10);
			$misc->type = $this->type[$misc->type];
			$misc->status = $this->status[$misc->status];
			$result[$key] = $misc;
		}

		return $result;
	}

	public function load_misc($mid)
	{
		$this->load->helper('directory');
		$misc = $this->db->query("select m.*, v.reference as ca_ref from tbl_misc m
			inner join tbl_voucher v on m.ca_ref = v.vid
			where mid = ".$mid)->row();

		$misc->approval = ($misc->status == 0);
		$misc->or_date = substr($misc->or_date, 0, 10);
		$misc->type = $this->type[$misc->type];
		$misc->status = $this->status[$misc->status];
		$misc->files = directory_map('./rms_dir/misc/'.$mid.'/', 1);
		return $misc;
	}

	public function edit_misc($mid)
	{
		$this->load->helper('directory');
		$misc = $this->db->query("select * from tbl_misc where mid = ".$mid)->row();
		$misc->files = directory_map('./rms_dir/misc/'.$mid.'/', 1);
		return $misc;
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