<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
    $this->load->model('Sales_model', 'sales');
    $this->load->model('File_model', 'file');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Registration');
		$this->header_data('nav', 'registration');
		$this->header_data('dir', './');
		$this->header_data('link', '
			<link href="./vendors/uniform.default.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./vendors/jquery.uniform.min.js"></script>
			<script src="./assets/js/registration.js"></script>');

		$sales = null;
		$sid = $this->input->post('sid');
		$search = $this->input->post('search');
		$submit = $this->input->post('submit');
		$files = $this->input->post('files');

		// on search
		if (!empty($search))
		{
			$engine_no = $this->input->post('engine_no');
			$sid = $this->sales->search_engine($engine_no, $_SESSION['region']);

			if (empty($sid)) $_SESSION['warning'][] = 'Engine # '.$engine_no.' is invalid.';
		}

		// on post
		if (!empty($sid)) {
			$sales = $this->sales->load_sales($sid);
		}

		// on submit
		$submit = $this->input->post('submit');
		if (!empty($submit)) {
			$this->submit_validate($sales);
		}

		// load sales view
		if (!empty($sales)){
			$data['sales'] = $sales;
			$sales = $this->load->view('registration/sales', $data, TRUE);
		}

		$data['sales'] = $sales;
		$data['files'] = $files;
		$this->template('registration/view', $data);
	}

	public function upload()
	{
		array_multisort($_FILES['scanFiles']['name'], SORT_ASC, SORT_STRING);
		$files = $this->file->upload_multiple($_FILES['scanFiles']);
		if (!empty($files))
		{
			$content = '';
			/*
			$files = array_multisort(
				$file->filename, SORT_ASC, SORT_STRING
			);*/
			foreach ($files as $file)
			{
				$content .= '
        	<div class="attachment temp" style="position:relative">
	        	'.form_hidden('files[]', $file->filename).'
	        	<img src="'.$file->path.'" style="margin:5px; border:solid">
	        	<a style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>
	        </div>';
			}
			echo json_encode(array("status" => TRUE, "content" => $content));
		}
		else
		{
			$message = $this->load->view('tpl/messages', array(), TRUE);
			echo json_encode(array("status" => FALSE, "message" => $message));
		}
	}

	public function delete()
	{
		$file = '/temp/'.$this->input->post('filename');
		$this->file->delete($file);
	}

	public function submit_validate($sales)
	{
		$files = $this->input->post('files');
		$cr_no = $this->input->post('cr_no');
		$err_msg = array();

		$registration = $sales->registration - $this->input->post('registration');
		$tip = $sales->tip - $this->input->post('tip');
		$new_hand = $sales->fund + ($registration + $tip);

		$this->form_validation->set_rules('registration', 'Registration', 'required|is_numeric|non_zero');
		$this->form_validation->set_rules('tip', 'Tip', 'required|is_numeric');
  	$this->form_validation->set_rules('cr_no', 'CR #', 'required');
  	$this->form_validation->set_rules('mvf_no', 'MVF #', 'required');

		if (empty($files)) {
			$err_msg[] = 'File attachment is required.';
		}
		if ($new_hand < 0) {
			$err_msg[] = 'Total Expense is greater than Cash on Hand.';
		}
		if (strlen($cr_no) != 9) {
			$err_msg[] = 'CR # must be 9 digits.';
		}

  	if ($this->form_validation->run() == TRUE && empty($err_msg)) {
  		$this->submit_save($sales, $new_hand);
  	}
  	else {
  		$_SESSION['warning'] = $err_msg;
  	}
	}

	public function submit_save($sales, $new_hand)
	{
		$files = $this->input->post('files');
		$this->file->save_scan_docs($sales, array(), $files);

		$new_sales = new Stdclass();
		$new_sales->sid = $sales->sid;
		$new_sales->registration = $this->input->post('registration');
		$new_sales->tip = $this->input->post('tip');
		$new_sales->cr_no = $this->input->post('cr_no');
		$new_sales->mvf_no = $this->input->post('mvf_no');
		$new_sales->plate_no = $this->input->post('plate_no');
		$new_sales->status = 4;
		$new_sales->registration_date = date('Y-m-d H:i:s');
		$this->sales->save_registration($new_sales);

		$this->load->model('Fund_model', 'fund');
		$this->fund->save_registration($sales->branch, $new_hand);

  	$this->load->model('Login_model', 'login');
		$filenames = implode(',', $files);
		$this->login->saveLog('marked sale ['.$sales->sid.'] with Engine # '.$sales->engine_no.' as Registered, and saved details:\r\nFiles - '.$filenames.'\r\nRegistration - '.$sales->registration.'\r\nTip - '.$sales->tip.'\r\nCR # - '.$sales->cr_no.'\r\nMVF # - '.$sales->mvf_no.'\r\nPlate # - '.$sales->plate_no);

		$_SESSION["messages"][] = 'Engine # '.$sales->engine_no.' for '.$sales->first_name.' '.$sales->last_name.' registered successfully.';
		redirect('registration');
	}
}
