<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Sales_transmittal_model extends CI_Model{

	public $stid = 0;
	public $sales = 0;
	public $transmittal = 0;
	public $is_rejected = false;
	public $remarks = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function reset()
	{
		$this->stid = 0;
		$this->sales = 0;
		$this->transmittal = 0;
		$this->is_rejected = false;
		$this->remarks = '';
	}

	public function load($stid)
	{
		$sales_transmittal = $this->db->get_where('tbl_sales_transmittal', array('stid' => $stid))->row();
		$this->stid = $sales_transmittal->stid;
		$this->sales = $sales_transmittal->sales;
		$this->transmittal = $sales_transmittal->transmittal;
		$this->is_rejected = $sales_transmittal->is_rejected;
		$this->remarks = $sales_transmittal->remarks;
	}

	public function save()
	{
		if ($this->stid)
		{
			$this->db->update("tbl_sales_transmittal", $this, array("stid" => $this->stid));
		}
		else
		{
			$this->db->insert("tbl_sales_transmittal", $this);

			$this->stid = $this->db->insert_id();
		}
	}

	public function get_id($param)
	{
		$id = array();

		$result = $this->db->get_where('tbl_sales_transmittal', $param);
		foreach ($result->result_object() as $row)
		{
			$id[] = $row->stid;
		}

		return $id;
	}
}