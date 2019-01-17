<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_user_data($uid="") {
	  $global = $this->load->database('global', TRUE);
		$result = $global->query("SELECT
																	*
																FROM tbl_users_info
																WHERE uid = $uid");
		return $result->row();
	}

	public function update_data($uid="",$firstname="",$middlename="",$lastname="",$ext="") {
	  $global = $this->load->database('global', TRUE);
		$result = $global->query("UPDATE tbl_users_info
																SET
																	firstname  = '$firstname',
																	middlename = '$middlename',
																	lastname   = '$lastname',
																	ext        = '$ext'
																WHERE uid = $uid");
		$_SESSION['firstname'] = $_POST['firstname'];
		$_SESSION['lastname']  = $_POST['lastname'];
		return $result;
	}

	public function update_password($uid="",$password="") {
	  $global = $this->load->database('global', TRUE);
		$result = $global->query("UPDATE tbl_users
																SET
																	password  = '$password'
																WHERE uid = $uid");
		return $result;
	}
	
}