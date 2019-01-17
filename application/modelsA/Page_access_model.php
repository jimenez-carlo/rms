<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Page_access_model extends CI_Model{

	public $paid = 0;
	public $page = 0;
	public $position = 0;

	public function __construct()
	{
		parent::__construct();
	}

	public function reset()
	{
		$this->paid = 0;
		$this->page = 0;
		$this->position = 0;
	}
	
	public function load($paid)
	{
		$page_access = $this->db->get_where('tbl_page_access', array('paid' => $paid))->row();
		$this->paid = $page_access->paid;
		$this->page = $page_access->page;
		$this->position = $page_access->position;
	}

	public function save()
	{
		if ($this->paid)
		{
			$this->db->update("tbl_page_access", $this, array("paid" => $this->paid));
		}
		else
		{
			$this->db->insert("tbl_page_access", $this);

			$this->paid = $this->db->insert_id();
		}
	}

	public function get_id($param)
	{
		$id = array();

		$result = $this->db->get_where('tbl_page_access', $param);
		foreach ($result->result_object() as $row)
		{
			$id[] = $row->paid;
		}

		return $id;
	}

	public function delete($param)
	{
		$this->db->delete("tbl_page_access", $param);
	}
}