<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
  public function __construct() { 
     parent::__construct(); 
     $this->load->helper('url');
  }

	public function index()
	{
		if(isset($_POST['login'])) {
			if($_POST['username']!="" && $_POST['password']!="") {
				$this->load->model('Login_model','login');
				$raw['user_info'] = $this->login->get_user_info($_POST['username']);
				$raw['sys_access'] = $this->login->get_system_access(20);
				$raw['page_access'] = $this->login->get_access();

				if(!is_null($raw['user_info'])) {
					$user_password = $this->login->decrypt($raw['user_info']->password);

					if($user_password==$_POST['password']) {
						$grant_access = false;
						foreach ($raw['sys_access'] as $value) {
							if($raw['user_info']->position == $value['position']) {
								$log = $this->login->add_user_log($raw['user_info']->username);
								$grant_access = true;
								$user_info = array(
												'ulid'				=> $log->ulid,
			                  'uid'         => $raw['user_info']->uid,
			                  'username'    => $raw['user_info']->username,
			                  'password'    => $raw['user_info']->password,
			                  'lastname'    => $raw['user_info']->lastname,
			                  'firstname'   => $raw['user_info']->firstname,
			                  'middlename'  => $raw['user_info']->middlename,
			                  'ext'         => $raw['user_info']->ext,
			                  'branch'      => $raw['user_info']->branch,
			                  'position'    => $raw['user_info']->position,
			                  'department'  => $raw['user_info']->department,
			                  'sys_access'  => $raw['sys_access'],
			                  'page_access' => $raw['page_access']);

							    // if rrt, set region
								if ($user_info['position'] == 156
									|| $user_info['position'] == 109)
								{
									$name = explode('-', $user_info['username']);
									switch ($name[1])
									{
										case 'R1': $user_info['region'] = 2; break;
										case 'R2': $user_info['region'] = 3; break;
										case 'R3': $user_info['region'] = 4; break;
										case 'R4A': $user_info['region'] = 5; break;
										case 'R4B': $user_info['region'] = 6; break;
										case 'R5': $user_info['region'] = 7; break;
										case 'R6': $user_info['region'] = 8; break;
										case 'R7': $user_info['region'] = 9; break;
										case 'R8': $user_info['region'] = 10; break;
										default: $user_info['region'] = 1; break; // NCR
									}
								}

							    // if rrt spvsr, set region
								if ($user_info['position'] == 108)
								{
									$name = explode('-', $user_info['username']);
									$user_info['region'] = (int)$name[2];
								}

							    // if acct-trsry, set tasks
								if ($user_info['position'] == 3
									|| $user_info['position'] == 34
									|| $user_info['position'] == 53
									|| $user_info['position'] == 98)
								{
									switch ($user_info['username'])
									{
										case 'ACCTG-PAYCL-001':
											$user_info['task'] = 'For ORCR Checking';
											$user_info['task_regions'] = '(1)';
											break;
										case 'ACCTG-PAYCL-002':
											$user_info['task'] = 'For ORCR Checking';
											$user_info['task_regions'] = '(2,3,4,5,6)';
											break;
										case 'ACCTG-PAYCL-003':
											$user_info['task'] = 'For ORCR Checking';
											$user_info['task_regions'] = '(7,8,9,10)';
											break;
										case 'ACCTG-PAYCL-004':
											$user_info['task'] = 'For SAP Uploading';
											$user_info['task_regions'] = '(1,2,3,4,5,6,7,8,9,10)';
											break;
										case 'ACCTG-PAYCL-005':
											$user_info['task'] = 'For Voucher';
											$user_info['task_regions'] = '(1,2,3,4,5,6,7,8,9,10)';
											break;
										case 'ACCTG-AMGR':
											$user_info['task'] = 'For Manager Approval';
											$user_info['task_regions'] = '(1,2,3,4,5,6,7,8,9,10)';
											break;
										case 'TRSRY-ASST-001':
											$user_info['task'] = 'For Check Issuance';
											break;
										case 'TRSRY-ASST-002':
											$user_info['task'] = 'For Check Deposit';
											break;
										case 'CMC-CCO':
											$user_info['task'] = 'For Management Approval';
											break;
									}
								}

								$this->session->set_userdata($user_info);
								$this->login->saveLog("User [" . $_SESSION['uid'] . "] " . $_SESSION['username'] . " logged in.");
								redirect('home');
							}
						}
						if(!$grant_access) {
							$data['error']="Your account does not have access to this system.";
						}
					}
					else {
						$data['error']="You have entered an incorrect password.";
					}
				}
				else {
					$data['error']="Invalid username/password.";
				}
			}
			else {
				$data['error']="Invalid username/password.";
			}
		}
		
		if($this->session->has_userdata('username')) {
			redirect('home');
		}

		$data['title'] = "Login | Registration Monitoring System";
		$this->load->view('login_view', $data);
	}
}
