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
		$this->header_data('title', 'Miscellaneous Expense');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './');

		if ($_SESSION['position'] == 108) { // spvsr
			$data['add'] = $data['edit'] = 0;
			$data['default_status'] = 0;
		}
		else {
			$data['add'] = $data['edit'] = 1;
			$data['default_status'] = 1;
		}

		$param = new Stdclass();
		$param->region = $_SESSION['region'];
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->type = $this->input->post('type');
		$param->status = $this->input->post('status');
		
		$param->status = (empty($param->status) && !is_numeric($param->status))
			? $data['default_status'] : $param->status;

		$data['table'] = $this->expense->list_misc($param);
		$data['type'] = $this->expense->type;
		$data['status'] = $this->expense->status;
		$this->template('expense/list', $data);
	}

	public function add()
	{
		$this->access(1);
		$this->header_data('title', 'Add Expense');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './../');
		$this->footer_data('script', '
			<script src="./../assets/js/expense.js"></script>');

		$save = $this->input->post('save');
		if (!empty($save)) $this->validate();

		$data['temp'] = array();
		$upload = $this->input->post('upload');
		if (!empty($upload)) {
			$this->load->model('File_model', 'file');
			$file = $this->file->upload_single();
			$data['temp'][] = $file->filename;
		}

		$reference = array('0' => '- select a reference -');
		$result = $this->db->query("select vid, reference from tbl_voucher where fund = ".$_SESSION['region'])->result_object();
		foreach ($result as $row) {
			$reference[$row->vid] = $row->reference;
		}
		$data['reference'] = $reference;

		$data['type'] = $this->expense->type;
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

	private function validate()
	{
		$err_msg = array();
		$type = $this->input->post('type');
		$other = $this->input->post('other');
		$files = $this->input->post('files');
		$temp = $this->input->post('temp');

		$this->form_validation->set_rules('or_no', 'Reference # (SI/OR)', 'required');
		$this->form_validation->set_rules('or_date', 'OR Date', 'required');
		$this->form_validation->set_rules('amount', 'Amount', 'required|is_numeric|non_zero');
		$this->form_validation->set_rules('ca_ref', 'CA Reference', 'required');

		$valid = $this->form_validation->run();

		if ($type == 4 && empty($other)) {
			$err_msg[] = 'Please specify other type of expense.';
		}
		if (empty($files) && empty($temp)) {
			$err_msg[] = 'Please upload an attachment.';
		}

		if (!empty($err_msg)) {
			$_SESSION['warning'] = $err_msg;
		}
		else if ($valid) {
			$this->save();
		}
	}

	private function save()
	{
		$misc = new Stdclass();
		$misc->mid = $this->input->post('mid');
		$misc->region = $_SESSION['region'];
		$misc->or_no = $this->input->post('or_no');
		$misc->or_date = $this->input->post('or_date');
		$misc->amount = $this->input->post('amount');
		$misc->type = $this->input->post('type');
		$misc->other = $this->input->post('other');
		$misc->ca_ref = $this->input->post('ca_ref');
		$misc->remarks = $this->input->post('remarks');

		if (empty($misc->mid)) {
			$this->db->insert('tbl_misc', $misc);
			$misc->mid = $this->db->insert_id();
		}
		else {
			$this->db->update('tbl_misc', $misc, array('mid' => $misc->mid));
		}

		$files = $this->input->post('files');
		$files = (empty($files)) ? array() : $files;
		$temp = $this->input->post('temp');
		$temp = (empty($temp)) ? array() : $temp;
		$file = $this->file->save_misc_scans2($misc, $files, $temp);

		$_SESSION['messages'][] = 'Reference # '.$misc->or_no.' was saved successfully.';
		redirect('expense');
	}

	public function view()
	{
		$mid = $this->input->post('mid');
		$data['misc'] = $this->expense->load_misc($mid);

		if ($_SESSION['position'] == 108 && $data['misc']->approval) {
			$data['approval'] = $data['reject'] = 1;
		}
		else {
			$data['approval'] = $data['reject'] = 0;
		}

		$view = $this->load->view('expense/view', $data, TRUE);
		echo json_encode($view);
	}

	public function edit()
	{
		$this->access(1);
		$this->header_data('title', 'Expense Record');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './../');
		$this->footer_data('script', '
			<script src="./../assets/js/expense.js"></script>');


		$edit = $this->input->post('edit');
		$mid = current(array_keys($edit));

		$save = $this->input->post('save');
		if (!empty($save)) $this->validate();

		$data['temp'] = array();
		$upload = $this->input->post('upload');
		if (!empty($upload)) {
			$this->load->model('File_model', 'file');
			$file = $this->file->upload_single();
			$data['temp'][] = $file->filename;
		}

		$reference = array('0' => '- select a reference -');
		$result = $this->db->query("select vid, reference from tbl_voucher where fund = ".$_SESSION['region'])->result_object();
		foreach ($result as $row) {
			$reference[$row->vid] = $row->reference;
		}
		$data['reference'] = $reference;
		
		$data['misc'] = $this->expense->edit_misc($mid);
		$data['type'] = $this->expense->type;
		$this->template('expense/edit', $data);
	}

	public function approve()
	{
		$mid = $this->input->post('mid');
		if (empty($mid)) redirect('expense');

		$this->db->query("update tbl_misc set status = 2 where mid = ".$mid);
		$misc = $this->db->query("select * from tbl_misc where mid = ".$mid)->row();

		$this->db->query("update tbl_fund set cash_on_hand = cash_on_hand - ".$misc->amount." where fid = ".$misc->region);

		$_SESSION['messages'][] = 'Reference # '.$misc->or_no.' was updated successfully'.
		redirect('expense');
	}

	public function reject()
	{
		$mid = $this->input->post('mid');
		if (empty($mid)) redirect('expense');

		$misc = new Stdclass();
		$misc->reason = $this->input->post('reason');
		$misc->status = 1;
		$this->db->update('tbl_misc', $misc, array('mid' => $mid));
		$misc = $this->db->query("select or_no from tbl_misc where mid = ".$mid)->row();

		$_SESSION['messages'][] = 'Reference # '.$misc->or_no.' was updated successfully'.
		redirect('expense');
	}

	public function ca_ref()
	{
		$this->access(1);
		$this->header_data('title', 'Update CA Reference');
		$this->header_data('nav', 'expense');
		$this->header_data('dir', './../');

		$mid = $this->input->post('mid');

		$update = $this->input->post('update');
		if (!empty($update)) {
			$mid = current(array_keys($update));
		}

		$save = $this->input->post('save');
		if (!empty($save)) {
			$this->form_validation->set_rules('ca_ref', 'CA Reference', 'required');

			if ($this->form_validation->run()) {
				$misc = new Stdclass();
				$misc->ca_ref = $this->input->post('ca_ref');
				$this->db->update('tbl_misc', $misc, array('mid' => $mid));

				$mid = null;
				$_SESSION['messages'][] = 'Record updated successfully.';
			}
		}

		if (!empty($mid)) {
			$reference = array('0' => '- select a reference -');
			$result = $this->db->query("select vid, reference from tbl_voucher where fund = ".$_SESSION['region'])->result_object();
			foreach ($result as $row) {
				$reference[$row->vid] = $row->reference;
			}
			$data['reference'] = $reference;
			
			$data['misc'] = $this->expense->edit_misc($mid);
			$data['type'] = $this->expense->type;
			$data['status'] = $this->expense->status;
			$this->template('expense/ca_ref_up', $data);
		}
		else {
			// echo "select m.*, v.reference as ca_ref, t.trans_no as topsheet
			//	from tbl_misc m
			//	left join tbl_voucher v on ca_ref = vid
			//	left join tbl_topsheet t on topsheet = tid
			//	where m.region = ".$_SESSION['region'];
			$data['type'] = $this->expense->type;
			$data['status'] = $this->expense->status;
			$data['table'] = $this->db->query("select m.*, v.reference as ca_ref, t.trans_no as topsheet
				from tbl_misc m
				left join tbl_voucher v on ca_ref = vid
				left join tbl_topsheet t on topsheet = tid
				where m.region = ".$_SESSION['region'])->result_object();
			$this->template('expense/ca_ref', $data);
		}
	}
}
