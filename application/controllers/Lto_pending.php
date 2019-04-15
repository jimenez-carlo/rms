<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lto_pending extends MY_Controller { 

  public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Lto_pending_model', 'lto_pending');
		$this->load->model('Sales_model', 'sales');
  }

	public function index()
	{
		$this->access(11);
		$this->header_data('title', 'LTO Pending');
		$this->header_data('nav', 'pending');
		$this->header_data('dir', './');

		// on submit save
		$submit = $this->input->post('submit');
		if (!empty($submit))
		{
			$this->submit_validate();
		}

		$view = $this->input->post('view');
		$ltid = $this->input->post('ltid');

		// on submit load
		if (!empty($view)) {
			$ltid = current(array_keys($view));
		}

		if (empty($ltid))
		{
			$data['table'] = $this->lto_pending->load_list($_SESSION['region']);
			$this->template('lto_pending/list', $data);
		}
		else
		{
			$data['transmittal'] = $this->lto_pending->load_transmittal($ltid);
			$data['reasons'] = $this->sales->lto_reason;
			$this->template('lto_pending/view', $data);
		}
	}

	public function submit_validate()
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
		else $this->submit_save();
	}

	public function submit_save()
	{
		$ltid = $this->input->post('ltid');
		$status = $this->input->post('status');
		$lto_reason = $this->input->post('lto_reason');

		foreach ($status as $sid => $val) {
			$sales = new Stdclass();
			$sales->sid = $sid;
			$sales->status = $status[$sid];
			$sales->lto_reason = $lto_reason[$sid];
			$this->sales->save_lto_pending($sales);
		}

		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$ltid)->row();

		$_SESSION['messages'][] = "Transmittal # ".$transmittal->code." updated successfully.";
		redirect("lto_pending");
	}
}
