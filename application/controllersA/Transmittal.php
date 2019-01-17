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

		$data['table'] = $this->transmittal->get_topsheet_for_transmittal(); 
		$this->template('transmittal/topsheet_list', $data);
	}

	public function generate($tid)
	{
		$this->transmittal->transmittal_topsheet($tid);
		redirect('transmittal/sprint/'.$tid);
	}

	public function sprint($tid)
	{
		$data['result'] = $this->transmittal->print_topsheet_transmittal($tid);
		$this->load->view('transmittal/print', $data);
	}

	public function branch()
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './../');

		$data['table'] = $this->transmittal->get_branch_transmittal($_SESSION['branch']);
		$this->template('transmittal/branch_list', $data);
	}

	public function bmi()
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './../');

		$data['table'] = $this->transmittal->get_bmi_transmittal();
		$this->template('transmittal/bmi_list', $data);
	}

	public function view($tid)
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal');
		$this->header_data('nav', 'transmittal');
		$this->header_data('dir', './../../');
		$this->footer_data('script', '
			<script src="./../../assets/modal/transmittal.js"></script>');

		$data['transmittal'] = $this->transmittal->load_transmittal($tid);
		$this->template('transmittal/view', $data);
	}

	public function view_remarks()
	{
		$tid = $this->input->post('tid');
		$sid = $this->input->post('sid');
		$remarks = $this->transmittal->load_remarks($tid, $sid);

		$content = '';
		foreach ($remarks as $row)
		{
			$content .= $row->remarks.'<br>
        <i>by '.$row->user->firstname.' '.$row->user->lastname.' on '.$row->date.'</i>
        <hr>';
		}

		echo json_encode(array("content" => $content));
	}

	public function save_remarks()
	{
		$this->form_validation->set_rules('remarks', 'Remarks', 'required');

		if ($this->form_validation->run() == TRUE)
		{
			$remarks = new Stdclass();
			$remarks->transmittal = $this->input->post('tid');
			$remarks->sales = $this->input->post('sid');
			$remarks->remarks = $this->input->post('remarks');
			$this->transmittal->save_remarks($remarks);

			$_SESSION['messages'][] = 'Added new remarks.';	
			echo json_encode(array("status" => TRUE));
		}
		else
		{
			echo json_encode(array("status" => FALSE, "message" => validation_errors()));
		}
	}

	public function receive($sid)
	{
		$sales = $this->transmittal->received($sid);
		$_SESSION['messages'][] = 'Received document for customer '.$sales->first_name.' '.$sales->last_name.'.';
		redirect('transmittal/view/'.$sales->transmittal);
	}

	public function status()
	{
		$this->access(1);
		$this->header_data('title', 'Transmittal Status');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../');

		//$data['table'] = $this->transmittal->get_topsheet_for_transmittal();
		$data['table'] = $this->transmittal->get_transmittal_status_list();
		$this->template('transmittal/status_list', $data);
	}
}


