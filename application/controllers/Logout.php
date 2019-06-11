<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Logout extends CI_Controller{

	function __construct(){
		parent::__construct();
     $this->load->helper('url');
	}

	public function index(){
          $this->load->model('Login_model','login');
          $this->login->end_user_log($_SESSION['ulid']);
          $this->login->saveLog("User [" . $_SESSION['uid'] . "] " . $_SESSION['username'] . " logged out.");

		$user_info = array(
                    'uLid',
                    'uid',
                    'username',
                    'password',
                    'lastname',
                    'firstname',
                    'middlename',
                    'ext',
                    'branch',
                    'position',
                    'department',
                    'region',
                    'branches',
                    'task',
                    'task_regions',
               );
		$this->session->unset_userdata($user_info);
		redirect('login');
	}
}
?>
