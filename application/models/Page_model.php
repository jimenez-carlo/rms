<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Page_model extends CI_Model{

	public $pid = 0;
	public $name = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function reset()
	{
		$this->pid = 0;
		$this->name = '';
	}
	
	public function load($paid)
	{
		$page = $this->db->get_where('tbl_pages', array('pid' => $pid))->row();
		$this->pid = $page->pid;
		$this->name = $page->name;
	}

	public function save()
	{
		if ($this->pid)
		{
			$this->db->update("tbl_pages", $this, array("pid" => $this->pid));
		}
		else
		{
			$this->db->insert("tbl_pages", $this);

			$this->pid = $this->db->insert_id();
		}
	}

	public function get_id($param)
	{
		$id = array();

		$result = $this->db->get_where('tbl_pages', $param);
		foreach ($result->result_object() as $row)
		{
			$id[] = $row->pid;
		}

		return $id;
	}
}