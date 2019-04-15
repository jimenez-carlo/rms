<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function add_user_log($username="") { 
		$this->db->query("INSERT INTO tbl_user_logs (
												user,
												ip_address,
												user_name,
												datetime_in
											)
											VALUES (
												'$username',
												'".$_SESSION['firstname']." ".$_SESSION['middlename']." ".$_SESSION['lastname']."',
												'".$_SERVER['REMOTE_ADDR']."',
												'".date('Y-m-d H:i:s', time())."'
											)");
		$result = $this->db->query("SELECT
																	ulid
																FROM tbl_user_logs
																ORDER BY ulid DESC LIMIT 1");
		return $result->row();
	}

	public function end_user_log($ulid="") {
		$this->db->query("UPDATE tbl_user_logs
											SET
												datetime_out='".date('Y-m-d H:i:s', time())."'
											WHERE ulid=$ulid");
	}

	public function saveLog($action="") {
		$ulid = (isset($_SESSION['ulid'])) ? $_SESSION['ulid'] : 0;
		$this->db->query("INSERT INTO tbl_user_action_logs (
													userlid,
													action_taken,
													datetime_log
												)
												VALUES (
												".$ulid.",
												'" . str_replace("'", "\'", $action) . "',
												'" . date('Y-m-d H:i:s', time()) . "')");
	}


	public function get_user_info($username) {
	  $global = $this->load->database('global', TRUE);
		$query  = "SELECT a.username, a.password, b.* ";
		$query .= "FROM tbl_users a INNER JOIN tbl_users_info b ON a.uid=b.uid ";
		$query .= 'WHERE username="'.$username.'"'; 
				
		$result = $global->query($query);
		return $result->row();
	}

	public function get_system_access($system="") {
	  $global = $this->load->database('global', TRUE);
		$result = $global->query("SELECT
																	*
																FROM tbl_system_access
																WHERE
																	system = '$system'");
		return $result->result_array();
	}

	public function get_access() {
		$result = $this->db->query("SELECT
																	*
																FROM tbl_page_access");
		return $result->result_array();
	}

	public function decrypt($password="") {
	  $key = 'passwordforportal';

	  $data = base64_decode($password);
	  $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

	  return $decrypted_password = rtrim(
	    mcrypt_decrypt(
	      MCRYPT_RIJNDAEL_128,
	      hash('sha256', $key, true),
	      substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
	      MCRYPT_MODE_CBC,
	      $iv
	    ),
	    "\0"
	  );
	}

	public function encrypt($password="") {
	  $key = 'passwordforportal';

	  $iv = mcrypt_create_iv(
	      mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
	      MCRYPT_DEV_URANDOM
	  );

	  return $encrypted_password = base64_encode(
	    $iv .
	    mcrypt_encrypt(
	      MCRYPT_RIJNDAEL_128,
	      hash('sha256', $key, true),
	      $password,
	      MCRYPT_MODE_CBC,
	      $iv
	    )
	  );
	}
}