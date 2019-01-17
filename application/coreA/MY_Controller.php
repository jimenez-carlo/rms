<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	private static $header = array();
	private static $footer = array();
	
	public function __construct() { 
	  parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		
	  $this->load->helper(array('form', 'url'));
	  $this->load->library('form_validation');
	}

	public function header_data($key, $data)
	{
		self::$header[$key] = $data;
	}

	public function footer_data($key, $data)
	{
		self::$footer[$key] = $data;
	}

	public function access($page)
	{
		if($this->session->has_userdata('username')) {
			$grant_access = false;
			
			foreach ($_SESSION['page_access'] as $access) {
				if($access['page']==$page && $access['position']==$_SESSION['position']) {
					$grant_access = true;
				}
			}

			if(!$grant_access) {
				redirect('no_access');
			}
		}
		else {
			redirect('login');
		}
	}

	public function template($page, $data = array())
	{
		$this->load->view('tpl/header', self::$header);
		if($_SESSION['position'] == 158)
				$data['orcr'] = $this->db->query("select *
					from tbl_topsheet_transmittal
					where receive_date is null
					and sales_type=0")->num_rows();
		if($_SESSION['position'] == 73)
				$data['orcr'] = $this->db->query("select *
					from tbl_transmittal
					where status = 0
					and type <> 2
					and branch = ".$_SESSION['branch'])->num_rows();

		// NAVIGATION BAR
		switch ($_SESSION['position']) {
			case 21: $this->load->view('tpl/nav_lo'); break; // LO
			case 27: $this->load->view('tpl/nav_dev'); break; // PROGRAMMER/ADMIN
			case 81: 
			case 73: $this->load->view('tpl/nav_ccn', $data); break; // CCN
			case 3: {
				$data['alert'] = $this->db->query("select count(*) as count from tbl_sales
					where acct_status = 2")->row()->count;
				$this->load->view('tpl/nav_acctg', $data);
				break;
			} // ACCTG-PAYCL
			case 53: $this->load->view('tpl/nav_acctg_amgr', $data); break; // ACCTG-AMGR
			case 34: $this->load->view('tpl/nav_trsry', $data); break; // Treasury
			case 95: $this->load->view('tpl/nav_trsry_head'); break; // Treasury-head
			case 98: $this->load->view('tpl/nav_trsry'); break; // CCO
			case 107: {
				$data['topsheet'] = $this->db->query("select count(*) as count from tbl_topsheet
					where print = 2")->row()->count;
				$data['rerfo'] = $this->db->query("select count(*) as count from tbl_rerfo
					where print = 2")->row()->count;
				$this->load->view('tpl/nav_rrt_mgr', $data);
				break;
			} // RRT MGR
			case 109: 
			case 156: $this->load->view('tpl/nav_rrt'); break; // RRT
			case 158: $this->load->view('tpl/nav_bmi', $data); break; // BMI
			case 108: {
				$data['not_received'] = 0;
				$data['remarks_count'] = 0;
				/*$data['not_received'] = $this->db->query("SELECT DISTINCT sales FROM tbl_orcr_remarks orcr WHERE (SELECT COUNT(*) FROM tbl_sales_status WHERE sales=orcr.sales AND status='ORCR Received' ) = 0")->num_rows();
				$data['remarks_count'] = $this->db->query("select * from tbl_topsheet_sales
					inner join tbl_topsheet on tid = topsheet
					where region = ".$_SESSION['region']."
					and hold = 1")->num_rows();
				$data['remarks_count'] += $this->db->query("select * from tbl_topsheet
					where region = ".$_SESSION['region']."
					and (select count(*) from tbl_batch
					where misc = 1 and topsheet = tid) = 0
					and (select count(*) from tbl_topsheet_misc_remarks
					where tid = topsheet) > 0")->num_rows();*/
				$this->load->view('tpl/nav_rrt_spvsr', $data);
				break;
			} // RRT SUPERVISOR
			default: break;
		}
		
		$this->load->view('tpl/messages');

		$this->load->view($page, $data);

		$this->load->view('tpl/footer', self::$footer);
	}
}