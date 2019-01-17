<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Miscellaneous_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function load($mid)
	{
		$misc $this->db->query("select * from tbl_misc where mid = ".$mid)->row();

		return $misc;
	}

	public function get()
	{
		return $this->db->query("select * from tbl_misc
			where branch = ".$_SESSION['branch']."
			and exp_date = '".date("Y-m-d")."'
			and type = 'Repo'")->row();
	}

	public function get_misc_row($tid)
	{
		return $this->db->query("select * from tbl_topsheet_misc where topsheet = ".$tid)->row();
	}

	public function update($meal,$photocopy,$transportation,$mid)
	{
		$this->db->query("update tbl_misc
				set meal = ".$meal.",
				photocopy = ".$photocopy.",
				transportation = ".$transportation."
				where mid = ".$mid);
	}

	public function save_misc($meal,$photocopy,$transportation) {
		$exp_date = date("Y-m-d");
		$branch = $_SESSION['branch'];

		$this->db->query("INSERT INTO tbl_misc(
																		branch,
																		exp_date,
																		type,
																		meal,
																		photocopy,
																		transportation)
																	VALUES(
																		$branch,
																		'$exp_date',
																		'Repo',
																		$meal,
																		$photocopy,
																		$transportation)");
	}	
}