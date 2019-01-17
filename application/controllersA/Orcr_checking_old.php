<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking_old extends MY_Controller {
	
	public function __construct() { 
		parent::__construct();
		$this->load->helper('url');
    $this->load->model('Orcr_checking_model', 'orcr_checking');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'For Checking');
		$this->header_data('nav', 'orcr_checking');
		$this->header_data('dir', './');
		$this->header_data('link', '
			<link href="./assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="./assets/DT_bootstrap.js"></script>
      <script>
      $(function(){
			$(".table").dataTable({
				"sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
					"sLengthMenu": "_MENU_ records per page"
				},
				"bSort": false,
				"bFilter": false,
				"iDisplayLength": 5,
				"aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
			});
		});
		</script>');

		$data['table'] = $this->orcr_checking->get_list_for_checking("");
		$data['page_title'] = "For Checking";
		$this->template('orcr_checking_old/list', $data);
	}

	public function view($tid)
	{
		$this->access(1);
		$this->header_data('title', 'For ORCR Checking');
		$this->header_data('nav', 'orcr_checking');
		$this->header_data('dir', './../../'); 
		$this->footer_data('script', '
      <script src="./../../assets/modal/orcr_checking.js"></script>');

		$topsheet = $this->orcr_checking->load_topsheet($tid);
    $data['topsheet'] = $topsheet;
    $this->template('orcr_checking_old/view', $data);
	}

	public function check($sid)
	{
		$sales = $this->orcr_checking->check_sales($sid);
  	$_SESSION['messages'][] = 'Record of Engine # '.$sales->engine_no.' added to Batch # '.$sales->trans_no.'.';

  	// update topsheet status
  	$this->load->model('Topsheet_model', 'topsheet');
  	$topsheet = $this->topsheet->check_sales($sales->topsheet);

  	// auto check misc
  	if ($topsheet->sales == 0 && $topsheet->misc_status != 3) {
  		$this->misc_check($topsheet->tid);
  	}

  	if ($topsheet->status == 2) {
			redirect('orcr_checking');
		}
		else {
			redirect('orcr_checking_old/view/'.$topsheet->tid);
		}
	}

	public function misc_check($tid)
	{
		$topsheet = $this->orcr_checking->check_misc($tid);
  	$_SESSION['messages'][] = 'Checked miscellaneous expense for Transaction # '.$topsheet->trans_no.'.';

  	// update topsheet status
  	$this->load->model('Topsheet_model', 'topsheet');
  	$topsheet = $this->topsheet->check_sales($tid);

  	if ($topsheet->status == 2) {
			redirect('orcr_checking');
		}
		else {
			redirect('orcr_checking_old/view/'.$topsheet->tid);
		}
	}

	public function hold($sid)
	{
		$reason = $this->input->post('reason');
		$remarks = $this->input->post('remarks');
		$err_msg = array();
    
    if (empty($reason)) {
    	$err_msg[] = 'Please select a reason to hold.';
    }
    else if (in_array('0', $reason) && empty($remarks)) {
    	$err_msg[] = 'Please specify reason in remarks.';
    }

    if (!empty($err_msg)) {
    	echo json_encode(array("status" => FALSE, "message" => $err_msg));
    }
    else if ($sid > 0) {
    	$this->save_hold($sid);
    }
    else {
    	$this->save_hold_misc();
    }
	}

	private function save_hold($sid)
	{
		$sales = new Stdclass();
		$sales->sid = $sid;
		$sales->reason = $this->input->post('reason');
		$sales->remarks = $this->input->post('remarks');
		$sales = $this->orcr_checking->hold_sales($sales);

  	$_SESSION['message'][] = 'Hold record of '.$sales->first_name.' '.$sales->last_name.' for Engine # '.$sales->engine_no;
		echo json_encode(array("status" => TRUE));
	}

	private function save_hold_misc($tid)
	{
		$topsheet = new Stdclass();
		$topsheet->tid = $tid;
		$topsheet->reason = $this->input->post('reason');
		$topsheet->remarks = $this->input->post('remarks');
		$topsheet = $this->orcr_checking->hold_misc($topsheet);

  	$_SESSION['message'][] = 'Hold miscellaneous expense for Transaction # '.$topsheet->trans_no;
		echo json_encode(array("status" => TRUE));
	}
}
