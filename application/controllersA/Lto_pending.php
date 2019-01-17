<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lto_pending extends MY_Controller { 

  public function __construct() {
     parent::__construct();
     $this->load->helper('url');
     $this->load->model('Lto_pending_model', 'lto_pending');
  }

	public function index()
	{
		$this->access(11);
		$this->header_data('title', 'LTO Pending');
		$this->header_data('nav', 'pending');
		$this->header_data('dir', './');
		$this->header_data('link', '
			<link href="./assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="./assets/DT_bootstrap.js"></script>
      <script src="./assets/js/lto_pending_list.js"></script>');

		$data['table'] = $this->lto_pending->load_list($_SESSION['region']);
		$this->template('lto_pending/list', $data);
	}

	public function view($ltid)
	{
		$this->access(11);
		$this->header_data('title', 'LTO Pending');
		$this->header_data('nav', 'pending');
		$this->header_data('dir', './../../');
		$this->footer_data('script', '
      <script src="./../../assets/js/lto_pending_view.js"></script>');

		// on submit
		$submit = $this->input->post('submit');
		if (!empty($submit))
		{
			$this->load->model('Sales_model', 'sales');
			$this->submit_validate($ltid);
		}

		$data['transmittal'] = $this->lto_pending->load_transmittal($ltid);
		$data['reasons'] = $this->lto_pending->get_reasons();
		$this->template('lto_pending/view', $data);
	}

	public function submit_validate($ltid)
	{
		$status = $this->input->post('status');
		$lto_reason = $this->input->post('lto_reason');
		$err_msg = array();

		foreach ($status as $sid => $val)
		{
			// require reason field if rejected is chosen
			if ($status[$sid] == 1 && $lto_reason[$sid] == 0)
			{
				$engine = $this->sales->get_engine($sid);
				$err_msg[] = 'Reason for rejection of Engine # '.$engine.' is required.';
			}
		}

		if (!empty($err_msg)) $_SESSION['warning'] = $err_msg;
		else $this->submit_save($ltid);
	}

	public function submit_save($ltid)
	{
		$status = $this->input->post('status');
		$lto_reason = $this->input->post('lto_reason');

		foreach ($status as $sid => $val) {
			$sales = new Stdclass();
			$sales->sid = $sid;
			$sales->status = $status[$sid];
			$sales->lto_reason = $lto_reason[$sid];
			$this->sales->save_lto_pending($sales);
		}

		$this->lto_pending->save_transmittal($ltid);
		redirect("lto_pending");
	}
}
