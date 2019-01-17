<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class User_assignment_model extends CI_Model{ 

	public function __construct()
	{
		parent::__construct();
	}

	public function save(&$assignment)
	{
		// return foreign key
		if (is_array($assignment->user))
		{
			$assignment->user = $assignment->user->uid;
		}

		// save regions
		foreach ($assignment->region as $region)
		{
			$this->db->insert('tbl_user_assignment_region', $region);
		}
		unset($assignment->region);

		if ($assignment->uaid)
		{
			$this->db->update('tbl_user_assignment', $assignment, array('uaid' => $uaid[$uid]));
		}
		else
		{
			$this->db->insert('tbl_user_assignment', $assignment);

			$assignment->uaid = $this->db->insert_id();
		}

		$assignment = $this->load($assignment->uaid);
	}
	
	public function load($uaid)
	{
		$assignment = $this->db->get_where('tbl_user_assignment', array('uaid' => $uaid))->row();
		$assignment->region = $this->db->get_where('tbl_user_assignment_region', array('assignment' => $uaid))->result_object();

		$global = $this->load->database('global', TRUE);
		$assignment->user = $global->get_where('tbl_users', array('uid' => $assignment->user))->row();
		$assignment->user->info = $global->get_where('tbl_users_info', array('uid' => $assignment->user->uid))->row();

		return $assignment;
	}
}