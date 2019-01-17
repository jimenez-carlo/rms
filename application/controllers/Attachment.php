<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attachment extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
    $this->load->model('Sales_model', 'sales');
    $this->load->model('File_model', 'file');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Attachment');
		$this->header_data('nav', 'attachment');
		$this->header_data('dir', './');

		$data = array();
		$sid = $this->input->post('sid');
		$engine_no = $this->input->post('engine_no');
		$submit = $this->input->post('submit');

		// on submit
		if (!empty($submit)) {
			$this->submit_validate();
		}

		$data['temp'] = array();
		$upload = $this->input->post('upload');
		if (!empty($upload)) {
			$data['temp'] = $this->upload();
		}

		// on search
		if (!empty($engine_no))
		{
			$sid = $this->sales->search_engine($engine_no);
			if (empty($sid)) $_SESSION['warning'][] = 'Engine # '.$engine_no.' is invalid.';
		}

		if (!empty($sid)) {
			$data['sales'] = $this->sales->load_sales($sid);
		}

		$this->template('attachment/view', $data);
	}

	public function upload()
	{
		$temp = array();
		$total_size = 0;
		$content = '';

		if (isset($_FILES['scanFiles']))
		{
			array_multisort($_FILES['scanFiles']['name'], SORT_ASC, SORT_STRING);
			foreach ($_FILES['scanFiles']['name'] as $key => $val) {
				$total_size += $_FILES['scanFiles']['size'][$key];
			}
	
			if($total_size > 1000000) {
				$_SESSION['warning'][] = "The file you are attempting to upload is larger than the permitted size.";
			}
			else {
				$files = $this->file->upload_multiple();
				if (!empty($files)) {
					foreach ($files as $file) {
						$temp[] = $file->filename;
						$content .= '
							<div class="attachment temp" style="position:relative">
								'.form_hidden('temp[]', $file->filename).'
								<img src="'.$file->path.'" style="margin:5px; border:solid">
								<a style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>
							</div>';
					}
				}
			}
		}

		return $temp;
		// if (empty($content)) {
		// 	$message = $this->load->view('tpl/messages', array(), TRUE);
		// 	echo json_encode(array("status" => FALSE, "message" => $message));
		// }
		// else {
		// 	echo json_encode(array("status" => TRUE, "content" => $content));
		// }
	}

	public function delete()
	{
		$file = '/temp/'.$this->input->post('filename');
		$this->file->delete($file);
	}

	public function submit_validate()
	{
		$temp = $this->input->post('temp');
		$cr_no = $this->input->post('cr_no');
		$err_msg = array();

  	$this->form_validation->set_rules('cr_no', 'CR #', 'required');
  	$this->form_validation->set_rules('mvf_no', 'MVF #', 'required');

		if (empty($temp)) {
			$err_msg[] = 'File attachment is required.';
		}
		// if (strlen($cr_no) != 9) {
		// 	$err_msg[] = 'CR # must be 9 digits.';
		// }

  	if ($this->form_validation->run() == TRUE && empty($err_msg)) {
  		$this->submit_save();
  	}
  	else {
  		$_SESSION['warning'] = $err_msg;
  	}
	}

	public function submit_save()
	{
		$sid = $this->input->post('sid');
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on eid = engine
			inner join tbl_customer on cid = customer
			where sid = ".$sid)->row();

		$temp = $this->input->post('temp');
		$this->file->save_scan_docs($sales, array(), $temp);

		$new_sales = new Stdclass();
		$new_sales->sid = $sales->sid;
		$new_sales->cr_no = $this->input->post('cr_no');
		$new_sales->mvf_no = $this->input->post('mvf_no');
		$new_sales->plate_no = $this->input->post('plate_no');
		$new_sales->file = 1;
		$this->sales->save_registration($new_sales);

  	$this->load->model('Login_model', 'login');
		$filenames = implode(',', $files);
		$this->login->saveLog('saved details for sale ['.$sales->sid.'] with Engine # '.$sales->engine_no.':\r\nFiles - '.$filenames.'\r\nCR # - '.$sales->cr_no.'\r\nMVF # - '.$sales->mvf_no.'\r\nPlate # - '.$sales->plate_no);

		$_SESSION["messages"][] = 'Engine # '.$sales->engine_no.' for '.$sales->first_name.' '.$sales->last_name.' registered successfully.';
		redirect('attachment/pending_list');
	}

	public function pending_list()
	{
		$this->access(1);
		$this->header_data('title', 'Pending Attachment');
		$this->header_data('nav', 'attachment');
		$this->header_data('dir', './../');

		$data['table'] = $this->db->query("select *,
				left(date_sold, 10) as date_sold
			from tbl_sales
			inner join tbl_engine on eid = engine
			inner join tbl_customer on cid = customer
			where status > 2
			and file = 0
			limit 1000")->result_object();
		$this->template('attachment/list', $data);
	}
}
