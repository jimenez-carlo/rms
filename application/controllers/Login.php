<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

  public function __construct() {
     parent::__construct();
     $this->load->helper('url');
  }

	public function index()
	{
		// prevent login when logged in
		if ($this->session->has_userdata('username')) redirect('home');

		if (isset($_POST['login']))
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			// validate 1
			if (empty($username)) {
				$data['error'] = "Please enter your username.";
			}
			else if (empty($password)) {
				$data['error'] = "Please enter your password.";
			}
			else {
				$this->load->model('Login_model','login');
				//var_dump(array('DATA', $username,$this->login->get_user_info('1822-BH-001')));
				//die;
				// special usernames
				$user_info = $this->custom_login($username, $password);

				// global login
				if (empty($user_info)) {
					$raw['user_info'] = $this->login->get_user_info($username);
					$raw['sys_access'] = $this->login->get_system_access(20);
					$raw['page_access'] = $this->login->get_access();

					// validate 2 - username exists
					if (empty($raw['user_info'])) {
						$data['error'] = "Invalid username.";
					}
					else {

						// validate 3 - password match
						$user_password = $this->login->decrypt($raw['user_info']->password);
						if ($password != $user_password) {
							$data['error'] = "You have entered an incorrect password.";
						}
						else {

							// validate 4 - access to system
							foreach ($raw['sys_access'] as $value) {
								if ($raw['user_info']->position == $value['position'])
								{
								  $log = $this->login->add_user_log($raw['user_info']->username);
							          $user_info = array(
								  'ulid'        => $log->ulid,
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
				                                  'page_access' => $raw['page_access']
				            			);
									break;
								}
							}
						}
					}
				}

				// still? invalid or empty login credential
				if (empty($user_info)) {
					if (!isset($data['error'])) $data['error'] = "Your account does not have access to this system.";
				}
				else {
					// set variables based on position
					switch ($user_info['position'])
					{
						case 156:
						case 109:
						case 108: // if rrt, set region
							$name = explode('-', $user_info['username']);
							switch ($name[1]) {
                                                        case 'NCR':
                                                          $user_info['region'] = 1;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R1' :
                                                          $user_info['region'] = 2;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R2' :
                                                          $user_info['region'] = 3;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R3' :
                                                          $user_info['region'] = 4;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R4A':
                                                          $user_info['region'] = 5;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R4B': $user_info
                                                          ['region'] = 6;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R5' :
                                                          $user_info['region'] = 7;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R6' :
                                                          $user_info['region'] = 8;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R7' :
                                                          $user_info['region'] = 9;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R8' :
                                                          $user_info['region'] = 10;
                                                          $user_info['company'] = 1;
                                                          break;

                                                        case 'R9' :
                                                          $user_info['region'] = 11;
                                                          $user_info['company'] = 8;
                                                          break;

                                                        case 'R10':
                                                          $user_info['region'] = 12;
                                                          $user_info['company'] = 8;
                                                          break;

                                                        case 'R11':
                                                          $user_info['region'] = 13;
                                                          $user_info['company'] = 8;
                                                          break;

                                                        case 'R12':
                                                          $user_info['region'] = 14;
                                                          $user_info['company'] = 8;
                                                          break;

                                                        case 'R13':
                                                          $user_info['region'] = 15;
                                                          $user_info['company'] = 8;
                                                          break;
                                                        default:
                                                          $user_info['region'] = 0;
                                                          $user_info['company'] = 1;
							}

							$this->load->model('Cmc_model', 'cmc');
							$user_info['branches'] = $this->cmc->get_region_branches($user_info['region']);
							break;

						case 73:
						case 81: // if ccn, set branch
							$user_info['branch'] = substr($user_info['username'], 0, 4);
							break;

						case 3:
						case 34:
						case 53:
						case 98: // if acct-trsry, set tasks
							switch ($user_info['username'])
							{
								case 'ACCTG-PAYCL-001':
									$user_info['task'] = 'For ORCR Checking';
									$user_info['task_regions'] = '(1)';
                                                                        $user_info['company'] = 1;
									break;
								case 'ACCTG-PAYCL-002':
									$user_info['task'] = 'For ORCR Checking';
									$user_info['task_regions'] = '(2,3,4,5,6)';
                                                                        $user_info['company'] = 1;
									break;
								case 'ACCTG-PAYCL-003':
									$user_info['task'] = 'For ORCR Checking';
									$user_info['task_regions'] = '(7,8,9,10)';
                                                                        $user_info['company'] = 1;
									break;
								case 'ACCTG-PAYCL-004':
									$user_info['task'] = 'For SAP Uploading';
									$user_info['task_regions'] = '(1,2,3,4,5,6,7,8,9,10)';
                                                                        $user_info['company'] = 1;
									break;
								case 'ACCTG-PAYCL-005':
									$user_info['task'] = 'For Voucher';
									$user_info['task_regions'] = '(1,2,3,4,5,6,7,8,9,10)';
                                                                        $user_info['company'] = 1;
									break;
								case 'ACCTG-PAYCL-013':
                                                                        $user_info['company'] = 1;
									break;
								case 'ACCTG-AMGR':
									$user_info['task'] = 'For Manager Approval';
									$user_info['task_regions'] = '(1,2,3,4,5,6,7,8,9,10)';
                                                                        $user_info['company'] = 1;
									break;
								case 'TRSRY-ASST-001':
									$user_info['task'] = 'For Check Issuance';
                                                                        $user_info['company'] = 1;
									break;
								case 'TRSRY-ASST-002':
									$user_info['task'] = 'For Check Deposit';
                                                                        $user_info['company'] = 1;
									break;
								case 'CMC-CCO':
									$user_info['task'] = 'For Management Approval';

                                                                //FOR MDI USERS
                                                                case 'ACCTG-PAYCL-030':
                                                                        $user_info['company'] = 8;
									break;
								case 'ACCTG-PAYCL-031':
									$user_info['task'] = 'For ORCR Checking';
									$user_info['task_regions'] = '(2,3,4,5,6)';
                                                                        $user_info['company'] = 8;
									break;
								case 'TRSRY-ASST-010':
									$user_info['task'] = 'For Check Issuance';
                                                                        $user_info['company'] = 8;
                                                                        break;
                                                                default:
                                                                        $user_info['company'] = 1;
							}
							break;
                                                default:
                                                        $user_info['company'] = 1;
					}

					// session set
					$this->session->set_userdata($user_info);
					$this->login->saveLog("User [" . $_SESSION['uid'] . "] " . $_SESSION['username'] . " logged in.");
					redirect('home');
				}
			}
		} // clean end

		// login form
		$data['title'] = "Login | Registration Monitoring System";
		$this->load->view('login_view', $data);
	}

	private function custom_login($username, $password)
	{
		if ($username == 'marketing' && $password == 'marketing') {
			// for marketing, orcr extract
			$log = $this->login->add_user_log($username);
			return array(
			  'ulid'        => $log->ulid,
                          'uid'         => 0,
                          'username'    => $username,
                          'password'    => 'dummy',
                          'lastname'    => 'Manayan',
                          'firstname'   => 'Alma',
                          'middlename'  => '',
                          'ext'         => '',
                          'branch'      => '9000',
                          'position'    => '-1',
                          'department'  => '0',
                          'sys_access'  => array(),
                          'page_access' => array(),
            		);
		}

		if ($username == 'siteadmin' && $password == 'siteadmin') {
			// for marketing, orcr extract
			$log = $this->login->add_user_log($username);
			return array(
			  'ulid'        => $log->ulid,
                          'uid'         => 0,
                          'username'    => $username,
                          'password'    => 'dummy',
                          'lastname'    => 'De Vera',
                          'firstname'   => 'Mary Jane',
                          'middlename'  => '',
                          'ext'         => '',
                          'branch'      => '9000',
                          'position'    => '-2',
                          'department'  => '0',
                          'sys_access'  => array(),
                          'page_access' => array(),
            			);
		}

		return null;
	}
}
