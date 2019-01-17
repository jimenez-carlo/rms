<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'url'));
    $this->load->library('upload');
    $this->load->library('form_validation');
    $this->load->model('Expense_model', 'expense');
    $this->load->model('File_model', 'file');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Expense');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './');

		$data = array();
		$search = $this->input->post('search');
		$or_no = $this->input->post('or_no');
		$or_date = $this->input->post('or_date');

		$lto = $this->input->post('lto');
		$hold = $this->input->post('hold');

		if(!empty($search))
		{
			$data['miscs'] = $this->expense->get_miscs($or_no,$or_date,"all");
		}

		$this->template('expense/list', $data);
	}

	public function rejected()
	{
		$this->access(1);
		$this->header_data('title', 'Expense (Rejected)');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../');

		$data = array();
		$search = $this->input->post('search');
		$or_no = $this->input->post('or_no');
		$or_date = $this->input->post('or_date');

		$lto = $this->input->post('lto');
		$hold = $this->input->post('hold');

		if(!empty($search))
		{
			$data['miscs'] = $this->expense->get_miscs($or_no,$or_date,"rejected");
		}

		$this->template('expense/report', $data);
	}

	public function add()
	{
		$this->access(1);
		$this->header_data('title', 'Add Expense');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './../');
		$this->header_data('link', '
			<link href="./../vendors/uniform.default.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./../vendors/jquery.uniform.min.js"></script>
			<script src="./../assets/js/expense.js"></script>');

		$data = array();
		$save = $this->input->post('save');

		if(!empty($save))
		{
			$misc = new Stdclass();
			$misc->region = $_SESSION['region'];
			$misc->or_no = $this->input->post('or_no');

			$or_date = $this->input->post('or_date');
			if(empty($or_date)) $or_date = date('Y-m-d H:i:s');
			$misc->or_date = $or_date.' '.date('H:i:s');

			$misc->amount = $this->input->post('amount');

			$this->expense->add($misc);

			$files = $this->input->post('files');
			$files = (empty($files)) ? array() : $files;
			$temp = $this->input->post('temp');
			$temp = (empty($temp)) ? array() : $temp;
			$file = $this->file->save_misc_scans2($misc, $files, $temp);

			$this->expense->save_filename($misc->mid,$file);

			$_SESSION['messages'][] = 'Added Miscellaneous.';
		}

		$this->template('expense/add', $data);
	}

	public function upload()
	{
		$this->load->model('File_model', 'file');
		$file = $this->file->upload_single();
		
		if (!empty($file))
		{
			echo json_encode(array("status" => TRUE, "file" => $file));
		}
		else
		{
			$message = $this->load->view('tpl/messages', array(), TRUE);
			echo json_encode(array("status" => FALSE, "message" => $message));
		}
	}

	public function view($mid)
	{
		$this->access(1);
		$this->header_data('title', 'Expense Record');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './../../');

		$data = array();
		$data['misc'] = $this->expense->load($mid);

		$approve = $this->input->post('approve');
		$reject = $this->input->post('reject');

		if(!empty($approve))
		{
			$this->expense->save_status(1,$mid);
			$_SESSION['messages'][] = "You have successfully approved this expense record.";
			redirect('expense/view/'.$mid);
		}
		if(!empty($reject))
		{
			$this->expense->save_status(2,$mid);			
			$_SESSION['messages'][] = "You have successfully rejected this expense record.";
			redirect('expense/view/'.$mid);
		}

		$this->template('expense/view', $data);
	}
}
