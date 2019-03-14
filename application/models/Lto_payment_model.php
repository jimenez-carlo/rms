<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Lto_payment_model extends CI_Model{

	public $status = array(
		1 => 'Pending',
		2 => 'Processing',
		3 => 'For Deposit',
		4 => 'Deposited',
		5 => 'Liquidated',
		);

	public function __construct()
	{
		parent::__construct();
	}

	public function get_list($param)
	{
                $compQuery = ($_SESSION['company'] == 8) ? " AND company = ".$_SESSION['company'] : " ";

		if (empty($param->date_from)) $param->date_from = date('Y-m-d', strtotime('-5 days'));
		if (empty($param->date_to)) $param->date_to = date('Y-m-d');

		$region = (empty($param->region)) ? "" : " AND region = ".$param->region;

		$status = (empty($param->status)) ? "" : " AND status = ".$param->status;

		$reference = (empty($param->reference)) ? "" : " AND reference LIKE '%".$param->reference."%'";

		return $this->db->query("SELECT * FROM tbl_lto_payment
                        WHERE ref_date BETWEEN '".$param->date_from."' AND '".$param->date_to."'
			".$region." ".$status." ".$reference." ".$compQuery."
			ORDER BY created DESC limit 1000")->result_object();
	}

        public function load_payment($lpid) {
          $payment = $this->db->query("select * from tbl_lto_payment where lpid = ".$lpid)->row();
          $payment->status = $this->status[$payment->status];

          $payment->sales = $this->db->query("select * from tbl_sales
            inner join tbl_engine on engine = eid
            inner join tbl_customer on customer = cid
            where lto_payment = ".$lpid."
            order by bcode")->result_object();

          return $payment;
        }

	public function upload_screenshot()
	{
		$this->load->library('upload');
		$config['allowed_types'] = 'pdf';
		$config['upload_path'] = './rms_dir/temp/';
		$config['max_size'] = '1024';
		$this->upload->initialize($config);

		if ($this->upload->do_upload('screenshot')) return $this->upload->data('file_name');

		$_SESSION['warning'][] = $this->upload->display_errors();
		return false;
	}

	public function upload_batch()
	{
		$this->load->library('upload');
		$config['allowed_types'] = 'csv';
		$config['upload_path'] = './rms_dir/temp/';
		$this->upload->initialize($config);

		if ($this->upload->do_upload('batch')) return $this->upload->data('file_name');

		$_SESSION['warning'][] = $this->upload->display_errors();
		return false;
	}

	public function save_payment($payment, $batch)
	{
		$payment->status = 1;
		$this->db->insert('tbl_lto_payment', $payment);
		$payment->lpid = $this->db->insert_id();

		// if (!empty($payment->screenshot)) {
		// 	// create folder
		// 	$folder = './rms_dir/lto_screenshot/'.$payment->lpid.'/';
		// 	if (!is_dir($folder)) mkdir($folder, 0777, true);

		// 	// delete dir files
		// 	$this->load->helper('directory');
		// 	$dir_files = directory_map($folder, 1);
		// 	foreach ($dir_files as $file) {
		// 		if (!empty($file)) unlink($folder.$file);
		// 	}

		// 	rename('./rms_dir/temp/'.$payment->screenshot, $folder.$payment->screenshot);
		// }

		$path = './rms_dir/temp/'.$batch;
		$file = fopen($path, 'r');
		$header = fgetcsv($file, 4096, ';', '"');

		while (($row = fgetcsv($file, 4096, ';', '"')) !== false) {
			if (!empty($row)) {
				$col = explode(',', $row[0]);
				if (!empty($col[0])) {
					$this->db->query("update tbl_sales
						inner join tbl_engine on engine = eid
						set status = 3, lto_payment = ".$payment->lpid."
						where status = 2
						and region = ".$payment->region."
						and left(bcode, 1) = '".$payment->company."'
						and engine_no = '".$col[0]."'");
				}
			}
		}
		fclose($file);
		unlink($path);

		$_SESSION['messages'][] = 'LTO Payment # '.$payment->reference.' saved successfully.';
		redirect('/lto_payment/view/'.$payment->lpid);
	}

	public function update_payment($payment, $remove, $engine_no)
	{
		$this->db->update('tbl_lto_payment', $payment, array('lpid' => $payment->lpid));

		// if (isset($payment->screenshot)) {
		// 	// create folder
		// 	$folder = './rms_dir/lto_screenshot/'.$payment->lpid.'/';
		// 	if (!is_dir($folder)) mkdir($folder, 0777, true);

		// 	// delete dir files
		// 	$this->load->helper('directory');
		// 	$dir_files = directory_map($folder, 1);
		// 	foreach ($dir_files as $file) {
		// 		if (!empty($file)) unlink($folder.$file);
		// 	}

		// 	rename('./rms_dir/temp/'.$payment->screenshot, $folder.$payment->screenshot);
		// }

		if (!empty($remove)) {
			foreach ($remove as $sid) {
				$this->db->query("update tbl_sales
					set status = 2, lto_payment = 0
					where sid = ".$sid);
			}
		}

		if (!empty($engine_no)) {
			foreach ($engine_no as $row) {
				$this->db->query("update tbl_sales
					inner join tbl_engine on engine = eid
					set lto_payment = ".$payment->lpid."
					where engine_no = '".$row."'");
			}
		}

		$_SESSION['messages'][] = 'LTO Payment # '.$payment->reference.' updated successfully.';
		redirect('/lto_payment/view/'.$payment->lpid);
	}

        public function get_list_by_status($status) {
          $company = ($_SESSION['company'] != 8) ? ' AND company != 8' : ' AND company = 8';
          return $this->db->query("select * from tbl_lto_payment
            where status = ".$status.$company." order by created desc")->result_object();
        }

	public function update_payment_status($payment, $status)
	{
		$payment->status = $status;
		$this->db->update('tbl_lto_payment', $payment, array('lpid' => $payment->lpid));
	}

	public function upload_receipt($lpid)
	{
		$this->load->library('upload');
		$config['allowed_types'] = 'pdf';
		$config['upload_path'] = './rms_dir/temp/';
		$config['max_size'] = '1024';
		$this->upload->initialize($config);

		if ($this->upload->do_upload('receipt_'.$lpid)) return $this->upload->data('file_name');

		$_SESSION['warning'][] = $this->upload->display_errors();
		return false;
	}

	public function deposit_payment($payment)
	{
		$payment->status = 4;
		$this->db->update('tbl_lto_payment', $payment, array('lpid' => $payment->lpid));

		if (!empty($payment->receipt)) {
			// create folder
			$folder = './rms_dir/lto_receipt/'.$payment->lpid.'/';
			if (!is_dir($folder)) mkdir($folder, 0777, true);

			// delete dir files
			$this->load->helper('directory');
			$dir_files = directory_map($folder, 1);
			foreach ($dir_files as $file) {
				if (!empty($file)) unlink($folder.$file);
			}

			rename('./rms_dir/temp/'.$payment->receipt, $folder.$payment->receipt);
		}
	}

	public function get_liquidation_list()
	{
		return $this->db->query("select lp.*, sum(registration) as sales
			from tbl_lto_payment lp
			left join tbl_sales s on lto_payment = lpid
			where lp.status = 4
			group by lpid
			order by lp.created desc")->result_object();
	}

	public function load_batch_sales($lpid)
	{
		$payment = $this->db->query("select * from tbl_lto_payment where lpid = ".$lpid)->row();
		$payment->sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where lto_payment = ".$lpid."
			order by bcode desc, date_sold desc")->result_object();
		return $payment;
	}
}
