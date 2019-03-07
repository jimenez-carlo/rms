<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {
	
  public function __construct() { 
     parent::__construct();
     $this->load->helper('url');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Profile');
		$this->header_data('nav', 'profile');
		$this->header_data('dir', './');

		$header_data['title'] = "Profile";
		$header_data['nav'] = "profile";
		$header_data['link'] = '
		<link href="assets/DT_bootstrap.css" rel="stylesheet" media="screen">
    <link href="vendors/chosen.min.css" rel="stylesheet" media="screen">';
		$this->load->view('tpl/header', $header_data);
		$this->load->view('tpl/navigation');

		$this->load->model('Profile_model','profile');
		$user_data = $this->profile->get_user_data($_SESSION['uid']);
		$data['firstname']  = $user_data->firstname;
		$data['middlename'] = $user_data->middlename;
		$data['lastname']   = $user_data->lastname;


		$footer_data['script'] = '
			<script src="vendors/jquery-1.9.1.js"></script>
			<script type="text/javascript" src="vendors/jquery-validation/dist/jquery.validate.min.js"></script>
			<script src="assets/form-validation.js"></script>
      <script src="assets/scripts.js"></script>
      <script>
			jQuery(document).ready(function() {   
			   FormValidation.init();
			});
      </script>';

		$this->template('profile/profile_view', $data);
	}

	public function save_profile()
	{
		$this->load->model('Profile_model','profile');
		$this->profile->update_data(
																	$_SESSION['uid'],
																	strtoupper($_POST['firstname']),
																	strtoupper($_POST['middlename']),
																	strtoupper($_POST['lastname']),
																	strtoupper($_POST['extname'])
															 );
		redirect('profile?submit=1');
	}

	public function save_password()
	{
		$this->load->model('Login_model','login');
		$user_info = $this->login->get_user_info($_SESSION['username']);
		$user_password = $this->login->decrypt($user_info->password);

		if($user_password==$_POST['pw3']) {
			$this->load->model('Profile_model','profile');
			$this->profile->update_password($_SESSION['uid'],$this->login->encrypt($_POST['pw1']));
			redirect('profile?submit=2');
		}
		else redirect('profile?submit=3');
	}

}
