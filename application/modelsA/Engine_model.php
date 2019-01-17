<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Engine_model extends CI_Model{

	public $eid = 0;
	public $engine_no = '';
	public $chassis_no = '';
	public $is_pnp_clear = true;
	public $is_manual = true;

	public function __construct()
	{
		parent::__construct();
	}

	public function reset()
	{
		$this->eid = 0;
		$this->engine_no = '';
		$this->chassis_no = '';
		$this->is_pnp_clear = true;
		$this->is_manual = true;
	}
	
	public function load($eid)
	{
		$engine = $this->db->get_where('tbl_engine', array('eid' => $eid))->row();
		$this->eid = $engine->eid;
		$this->engine_no = $engine->engine_no;
		$this->chassis_no = $engine->chassis_no;
		$this->is_pnp_clear = $engine->is_pnp_clear;
		//$this->is_manual = $engine->is_manual;
	}

	public function load2($engine_no)
	{
		return $this->db->query("SELECT eid,engine_no FROM tbl_engine WHERE engine_no like '$engine_no'")->row();
	}

	public function save()
	{
		if ($this->eid)
		{
			$this->db->update("tbl_engine", $this, array("eid" => $this->eid));
		}
		else
		{
			$this->db->insert("tbl_engine", $this);

			$this->eid = $this->db->insert_id();
		}
	}

	public function get_id($param)
	{
		$id = array();

		$result = $this->db->get_where('tbl_engine', $param);
		foreach ($result->result_object() as $row)
		{
			$id[] = $row->eid;
		}

		return $id;
	}

	public function get_engine_no($sid)
	{
		return $this->db->query("select engine_no from tbl_engine
					inner join tbl_sales on engine = eid
					where sid = ".$sid)->row()->engine_no;
	}
}