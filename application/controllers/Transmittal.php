<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transmittal extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Transmittal_model', 'transmittal');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './');

		$data['table'] = $this->transmittal->get_rrt_transmittal($_SESSION['region_id']);
		$this->template('transmittal/report', $data);
	}

	public function intransit($bcode)
	{
		$this->access(1);
		$this->header_data('title', 'Intransit Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './../../');

		$data['table'] = $this->transmittal->get_rrt_intransit($bcode);
		$this->template('transmittal/intransit', $data);
	}

	public function received($bcode)
	{
		$this->access(1);
		$this->header_data('title', 'Received Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './../../');

		$data['table'] = $this->transmittal->get_rrt_received($bcode);
		$this->template('transmittal/received', $data);
	}

	public function view()
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './../../');
		$this->footer_data('script', '
			<script src="./../../assets/modal/transmittal.js"></script>');

		$rid = $this->input->post('rid');

		$view = $this->input->post('view_tr');
		if (!empty($view)) $rid = current(array_keys($view));

		$receive = $this->input->post('receive');
		if (!empty($receive)) {
			$sid = current(array_keys($receive));
			$this->receive($sid);
		}

		if (empty($rid)) {
			if ($_SESSION['position'] == 108) redirect('topsheet');
			else redirect('transmittal/branch');
		}

		$branch = ($_SESSION['position'] == 108) ? 0 : $_SESSION['branch_code'];

		$data['rerfo'] = $this->transmittal->load_rerfo($rid, $branch);
		$this->template('transmittal/view', $data);
	}

	public function branch()
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', base_url());

		$data['table'] = $this->transmittal->get_branch_rerfo($_SESSION['branch_code']);
		$this->template('transmittal/branch_list', $data);
	}

	public function receive($sid)
	{
		$this->db->query("update tbl_sales set received_date = '".date('Y-m-d H:i:s')."' where sid = ".$sid);

		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_customer on customer = cid
			where sid = ".$sid)->row();

		$_SESSION['messages'][] = 'Received document for customer '.$sales->first_name.' '.$sales->last_name.'.';
	}
}


