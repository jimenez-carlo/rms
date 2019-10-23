<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Return_fund_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
                if ($_SESSION['company'] != 8) {
                  $this->companyQry = 'company != 8';
                } else {
                  $this->companyQry = 'company = 8';
                }
	}

	public function load_list($param)
	{
		if (empty($param->region)) $region = "";
		else $region = " and v.fund = ".$param->region;

		if (empty($param->reference)) $reference = "";
		else $reference = " and v.reference like '%".$param->reference."%'";

                return $this->db->query("
                      SELECT *, CASE
				WHEN company = 1 THEN 'MNC'
				WHEN company = 2 THEN 'MTI'
				WHEN company = 3 THEN 'HPTI'
                                WHEN company = 8 THEN 'MDI'
                                END as companyname, rf.amount
                      FROM
                        tbl_return_fund rf
                      INNER
                        join tbl_voucher v on v.vid = rf.fund
                      WHERE
                        ".$this->companyQry." ".$region." ".$reference."
                        AND created BETWEEN '".$param->date_from." 00:00:00' AND '".$param->date_to." 23:59:59'
                      ORDER BY created DESC LIMIT 1000
                ")->result_object();
	}

	public function load_fund($vid)
	{
		return $this->db->query("select * from tbl_voucher where vid = ".$vid)->row();
	}

	public function upload_slip()
	{
		$this->load->library('upload');
		$config['allowed_types'] = 'jpg|jpeg';
		$config['upload_path'] = './rms_dir/temp/';
		$config['max_size'] = '1024';
		$this->upload->initialize($config);

		if ($this->upload->do_upload('slip')) return $this->upload->data('file_name');

		$_SESSION['warning'][] = $this->upload->display_errors();
		return false;
	}

	public function save_return($return)
	{
		$this->db->insert('tbl_return_fund', $return);
		$return->rfid = $this->db->insert_id();

		if (!empty($return->slip)) {
			// create folder
			$folder = './rms_dir/deposit_slip/'.$return->rfid.'/';
			if (!is_dir($folder)) mkdir($folder, 0777, true);

			// delete dir files
			$this->load->helper('directory');
			$dir_files = directory_map($folder, 1);
			foreach ($dir_files as $file) {
				if (!empty($file)) unlink($folder.$file);
			}

			rename('./rms_dir/temp/'.$return->slip, $folder.$return->slip);
		}

		$_SESSION['messages'][] = 'Transaction saved successfully.';
		redirect('liquidation');
	}

	public function load_return($rfid)
	{
		return $this->db->query("select *, rf.amount from tbl_return_fund rf
			inner join tbl_voucher on vid = rf.fund
			where rfid = ".$rfid)->row();
	}

	public function liquidate_return($rfid)
	{
		$return = new Stdclass();
		$return->liq_date = date('Y-m-d H:i:s');
		$this->db->update('tbl_return_fund', $return, array('rfid' => $rfid));

		$_SESSION['messages'][] = 'Transaction updated successfully.';
		redirect('liquidation');
	}
}
