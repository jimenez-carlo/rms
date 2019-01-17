<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topsheet extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Topsheet_model', 'topsheet');
    $this->load->model('File_model', 'file');
    $this->load->model('Orcr_checking_model', 'orcr_checking');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet');
		$this->header_data('nav', 'topsheet');
		$this->header_data('dir', './');

		$data['table'] = $this->topsheet->get_list(); 
		$this->template('topsheet/ts_list', $data);
	}

	public function tid($company)
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet');
		$this->header_data('nav', 'topsheet');
		$this->header_data('dir', './../../');
		$this->header_data('link', '
			<link href="./../../vendors/uniform.default.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./../../vendors/jquery.uniform.min.js"></script>
			<script src="./../../assets/js/topsheet.js"></script>');

		$data = array();

		// unset temp file keys
		$tempkeys = $this->input->post('tempkeys');

		// on upload
		$upload = $this->input->post('upload');
		if (!empty($upload))
		{
			$this->upload();
		}

		// load topsheet
		$rerfo_date = date("m/d/Y");

		// on search, format date
		$rerfo_date = DateTime::createFromFormat('m/d/Y', $rerfo_date)->format("Y-m-d");

		$topsheet = new Stdclass();
		$topsheet->rerfo_date = date("Y-m-d");
		$topsheet->region = $_SESSION['region'];
		$topsheet->company = $company;
		$topsheet->type = 'Brand New';
		$topsheet = $this->topsheet->topsheet($topsheet);

		if (empty($topsheet))
		{
			$_SESSION['warning'][] = 'No result found.';
		}
		else
		{
			$data["topsheet"] = $topsheet;

			// get fund
			$cash_on_hand = $this->db->query("select sum(cash_on_hand) as cash_on_hand 
				from tbl_fund where region = ".$_SESSION['region']."
				group by region")->row()->cash_on_hand;
			$data["cash_on_hand"] = $cash_on_hand;
			
			// enable misc input if rerfo date is set today
			if ($topsheet->rerfo_date == date('Y-m-d')) $data['set_misc'] = 1;
		}

		// on print
		$print = $this->input->post('print');
		if (!empty($print))
		{
  		$neg_expense = $this->input->post('neg_expense') *-1;

			$set_misc = $this->input->post('set_misc');
			if (!empty($set_misc))
			{
				// compute total misc based on post
				$topsheet->total_misc = ($this->input->post('meal')
					+ $this->input->post('photocopy')
					+ $this->input->post('transportation')
					+ $this->input->post('others'));
			}

			$filekeys = $this->input->post('filekeys');
			$tempkeys = $this->input->post('tempkeys');
			if (empty($filekeys) && empty($tempkeys) && $topsheet->total_misc != 0)
			{
				$_SESSION['warning'][] = 'File attachment is required when miscellaneous expense has values.';
			}
			else if ((!empty($filekeys) || !empty($filekeys)) && $topsheet->total_misc == 0)
			{
				$_SESSION['warning'][] = 'Miscellaneous expense is required when uploading file attachments.';
			}
  		else if ($cash_on_hand < $neg_expense)
  		{
				$_SESSION['warning'][] = 'Miscellaneous expense is greater than Cash on Hand.';
			}
			else
			{
				$this->generate();
			}
		}

		$request = $this->input->post('request');
		if (!empty($request))
		{
			$tid = $this->input->post('tid');
			$request = $this->db->query("select * from tbl_topsheet_request
				where topsheet = ".$tid)->row();

      if (empty($request))
      {
      	$request = new Stdclass();
      	$request->topsheet = $tid;
      	$this->db->insert('tbl_topsheet_request', $request);

      	$topsheet = $this->db->query("select * from tbl_topsheet where tid = ".$tid)->row();
      	$_SESSION['messages'][] = 'Request for reprint sent.';
      	$this->login->saveLog('requested reprinting of topsheet '.$topsheet->trans_no.' to Manager');
      }
      else
      {
      	$_SESSION['warning'][] = 'Request for reprint already sent.';
      }
    }

		$this->template('topsheet/ts_view', $data);  
	}

	public function upload()
	{
		$files = $this->file->upload_multiple($_FILES['scanFiles']);
		if (!empty($files))
		{
			foreach ($files as $file)
			{
				$filename = $file->filename;
				$path = './../../'.$file->path;
			}
			echo json_encode(array("status" => TRUE, "filename" => $filename, "path" => $path));
		}
		else
		{
			echo json_encode(array("status" => FALSE, "message" => $_SESSION['warning']));
			unset($_SESSION['warning']);
		}
	}

	public function delete($file)
	{
		$file = '/temp/'.$this->input->post('filename');
		$this->file->delete($file);
	}

	private function validate_submit($topsheet)
	{
		$others = $this->input->post('others');
		$others_specify = $this->input->post('others_specify');
		$files = $this->input->post('files');
		$temp = $this->input->post('temp');
		$err_msg = array();

		$total_misc = $this->input->post('meal')
			+ $this->input->post('photocopy')
			+ $this->input->post('transportation')
			+ $this->input->post('others');
		$expense = $total_misc - $topsheet->total_misc;
		$new_hand = $topsheet->fund - $expense;

		if (empty($files) && empty($temp) && $total_misc != 0) {
			$err_msg[] = 'File attachment is required when miscellaneous expense has values.';
		}
		if ((!empty($files) || !empty($temp)) && $total_misc == 0) {
			$err_msg[] = 'Miscellaneous expense is required when uploading file attachments.';
		}
		if ($new_hand < 0) {
			$err_msg[] = 'Miscellaneous expense is greater than Cash on Hand.';
		}
		if ($others > 0 && empty($others_specify)) {
			$err_msg[] = 'Please specify other expenses.';
		}
		
		if (!empty($err_msg)) $_SESSION['warning'] = $err_msg;
		else $this->save_submit($topsheet, $new_hand);
	}

	private function save_submit($topsheet, $new_hand)
	{
		if ($new_hand != 0)
		{
			$files = $this->input->post('files');
			$files = (empty($files)) ? array() : $files;
			$temp = $this->input->post('temp');
			$temp = (empty($temp)) ? array() : $temp;
			$this->file->save_misc_scans($topsheet, $files, $temp);

			$misc = new Stdclass();
			$misc->meal = $this->input->post('meal');
			$misc->photocopy = $this->input->post('photocopy');
			$misc->transportation = $this->input->post('transportation');
			$misc->others = $this->input->post('others');
			$misc->others_specify = $this->input->post('others_specify');
			$this->topsheet->save_misc($topsheet, $misc);

			$this->load->model('Fund_model', 'fund');
			$this->fund->save_misc($topsheet, $new_hand);
		}

		redirect('topsheet/sprint/'.$topsheet->tid);
	}

	public function sprint($tid)
	{
		$data['topsheet'] = $this->topsheet->print_topsheet($tid);
		$this->load->view('topsheet/print', $data);
	}

	public function request($tid)
	{
    if ($this->topsheet->request_reprint($tid)) {
    	$_SESSION['messages'][] = 'Request for reprint sent.';
    }
    else {
    	$_SESSION['warning'][] = 'Request for reprint already sent.';
    }
    redirect('topsheet');
	}

	public function status()
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet Status');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../');

		$data = array();

		// on search
		$trans_no = $this->input->post('trans_no');
		$print_date = $this->input->post('print_date');
		$search = $this->input->post('search');

		if(!empty($search)) $data['table'] = $this->topsheet->topsheet_status($trans_no,$print_date);

/*		if (!empty($search))
		{
			$topsheet = $this->db->get_where("tbl_topsheet", array('trans_no' => $trans_no, 'region' => $_SESSION['region']))->row();

			if (!empty($topsheet))
			{
				redirect('topsheet/view/'.$topsheet->tid);
			}
			else
			{
				$_SESSION['warning'][] = 'Transaction # '.$trans_no.' does not exist.';
			}
		}*/

		$this->template('topsheet/ts_list', $data);
	}

	public function view($tid)
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet');
		$this->header_data('nav', 'topsheet');
		$this->header_data('dir', './../../');
		$this->footer_data('script', '
			<script>
			$(function(){
				if ($("tr.attachments").length > 0)
				{
					$("tr.sales").click(function(){
						var index = $(this).index()+1;
						$("tbody tr:eq("+index+")").removeClass("hide");
					});
					
					$("tr.attachments a").click(function(){
						$(this).closest("tr").addClass("hide");
					});
				}
			});
			</script>');

		$topsheet = $this->topsheet->load($tid);
    $data['topsheet'] = $topsheet;
		$data['misc'] = $this->load->view('topsheet/misc', $data, TRUE);
    $this->template('topsheet/overview', $data);
	}

	public function ts_view($tid)
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../../');
		$this->header_data('link', '
			<link href="./../../vendors/uniform.default.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="./../../vendors/jquery.uniform.min.js"></script>
			<script src="./../../assets/js/topsheet.js"></script>
			<script>
			$(function(){
				if ($("tr.attachments").length > 0)
				{
					$("tr.sales").click(function(){
						var index = $(this).index()+1;
						$("tbody tr:eq("+index+")").removeClass("hide");
					});
					
					$("tr.attachments a").click(function(){
						$(this).closest("tr").addClass("hide");
					});
				}
			});
			</script>');

		$topsheet = $this->orcr_checking->load_topsheet($tid);// $this->topsheet->load($tid);

		// on print
		$print = $this->input->post('print');
		if (!empty($print))
		{
			$this->validate_submit($topsheet);
		}

    $data['tid'] = $tid;
    $data['topsheet'] = $topsheet;
		$data['misc'] = $this->load->view('topsheet/misc', $data, TRUE);
    $this->template('topsheet/ts_view', $data);
	}
}
