<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public $region = array(
		1 => 'NCR',
		2 => 'Region 1',
		3 => 'Region 2',
		4 => 'Region 3',
		5 => 'Region 4A',
		6 => 'Region 4B',
		7 => 'Region 5',
		8 => 'Region 6',
		9 => 'Region 7',
		10 => 'Region 8',
        );

        public $mdi_region = array(
                11 => 'Region IX',
                12 => 'Region X',
                13 => 'Region XI',
                14 => 'Region XII',
                15 => 'Region XIII'
        );



	public $reg_code = array(
		1 => 'NCR',
		2 => 'R1',
		3 => 'R2',
		4 => 'R3',
		5 => 'R4A',
		6 => 'R4B',
		7 => 'R5',
		8 => 'R6',
		9 => 'R7',
		10 => 'R8',
		11 => 'IX',
		12 => 'X',
		13 => 'XI',
		14 => 'XII',
		15 => 'XIII',
	);

	public $company = array(
		1 => 'MNC',
		2 => 'MTI',
		3 => 'HPTI',
		6 => 'MTI',
	);

        public $mdi = array(
                8 => 'MDI'
        );

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
		// no credentials
		if (!$this->session->has_userdata('username')) redirect('login');

		// special custom login, always grant
		if ($_SESSION['uid'] == 0) return;

		// if with access, grant
		foreach ($_SESSION['page_access'] as $access) {
			if ($access['page']==$page && $access['position']==$_SESSION['position']) {
				return;
			}
		}

		// if access check fails
		redirect('no_access');
	}

	public function template($page, $data = array()) {

		// set header
		$this->load->view('tpl/header', self::$header);

		// NAVIGATION BAR
		switch ($_SESSION['position']) {
			case 27: // PROGRAMMER/ADMIN
				$this->load->view('tpl/nav_dev');
				break;

			case 109:
			case 156: // RRT
				$this->load->view('tpl/nav_rrt');
				break;

			case 108: // RRT SPVSR
				$data['not_received'] = 0;
				$data['remarks_count'] = 0;

				$this->load->view('tpl/nav_rrt_spvsr', $data);
				break;

			case 107: // RRT MGR
                                $regions = ($_SESSION['company'] === '8') ? ' AND region BETWEEN 11 AND 15 ' : ' AND region BETWEEN 1 AND 10';
				$data['topsheet'] = $this->db->query("SELECT COUNT(*) AS count FROM tbl_topsheet WHERE print = 2 $regions")->row()->count;
				$data['rerfo'] = $this->db->query("SELECT COUNT(*) AS count FROM tbl_rerfo WHERE print = 2 $regions")->row()->count;
				$this->load->view('tpl/nav_rrt_mgr', $data);
				break;

			case 3: // ACCTG-PAYCL
				$data['alert'] = $this->db->query("select count(*) as count from tbl_sales
					where acct_status = 2")->row()->count;
				$this->load->view('tpl/nav_acctg', $data);
				break;

			case 53: // ACCTG-AMGR
				$this->load->view('tpl/nav_acctg_amgr', $data);
				break;

			case 98: // CCO
			case 34: // Treasury
				$this->load->view('tpl/nav_trsry', $data);
				break;

			case 95: // Treasury-head
				$this->load->view('tpl/nav_trsry_head');
				break;

			case 21: // BRANCH LO
				$this->load->view('tpl/nav_lo');
				break;

			case 72:
			case 81:
			case 73: // BRANCH CCN + BH
				$data['orcr'] = $this->db->query("select *
					from tbl_transmittal
					where status = 0
					and type <> 2
					and branch = ".$_SESSION['branch'])->num_rows();
				$this->load->view('tpl/nav_ccn', $data);
				break;

			case 158: // BMI
				$data['orcr'] = $this->db->query("select *
					from tbl_topsheet_transmittal
					where receive_date is null
					and sales_type=0")->num_rows();
				$this->load->view('tpl/nav_bmi', $data);
				break;

			case -1: // SPECIAL - MARKETING
				$this->load->view('tpl/nav_mktg');
				break;

			case -2: // SPECIAL - SITE ADMIN
				$this->load->view('tpl/nav_admin');
				break;

			default:
				$this->load->view('tpl/nav_def');
		}

		// templated
		$this->load->view('tpl/messages');
		$this->load->view($page, $data);
		$this->load->view('tpl/footer', self::$footer);
	}
}
