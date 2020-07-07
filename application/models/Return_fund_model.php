<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Return_fund_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
                if ($_SESSION['company'] != 8) {
                  $this->companyQry = 'c.cid != 8';
                } else {
                  $this->companyQry = 'c.cid = 8';
                }
	}

	public function load_list($param)
	{
		$region = (empty($param->region)) ? "" : " AND v.fund = ".$param->region;
		$reference = (empty($param->reference)) ? "" : " AND v.reference LIKE '%".$param->reference."%'";
                $rrt = ($_SESSION['dept_name'] === 'Regional Registration') ? "AND r.rid = {$_SESSION['region_id']}" : "";

                $return_fund_list = $this->db->query("
                  SELECT
                    rf.rfid, DATE_FORMAT(rf.created, '%Y-%m-%d') AS created,
                    v.reference, c.company_code AS companyname,
                    r.region, rf.amount, rf.slip,
                    DATE_FORMAT(rf.liq_date, '%Y-%m-%d') AS liq_date,
                    st.status_id, st.status_name AS status
                  FROM
                    tbl_return_fund rf
                  INNER JOIN
                    tbl_return_fund_history rfh1 ON rfh1.rfid = rf.rfid
                  LEFT JOIN
                    tbl_return_fund_history rfh2
                      ON rfh2.rfid = rfh1.rfid AND rfh1.return_fund_history_id < rfh2.return_fund_history_id
                  INNER JOIN
                    tbl_status st ON st.status_id = rfh1.status_id AND st.status_type = 'RETURN_FUND'
                  INNER JOIN
                    tbl_voucher v ON v.vid = rf.fund
                  INNER JOIN
                    tbl_fund f ON v.fund = f.fid
                  INNER JOIN
                    tbl_region r ON r.rid = f.region
                  INNER JOIN
                    tbl_company c ON c.cid = v.company
                  WHERE
                    rfh2.return_fund_history_id IS NULL AND rf.is_deleted = 0 {$rrt}
                    AND {$this->companyQry} {$region} {$reference}
                    AND created BETWEEN '{$param->date_from} 00:00:00' AND '{$param->date_to} 23:59:59'
                  ORDER BY created DESC LIMIT 1000
                ")->result_object();

                return $return_fund_list;
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
                $this->db->trans_start();
                // Insert New Return Fund
		$this->db->insert('tbl_return_fund', $return);
		$return->rfid = $this->db->insert_id();

                // Update Cash on Hand
                $update_cash_on_hand = "UPDATE tbl_fund SET cash_on_hand = cash_on_hand - {$return->amount} WHERE fid = {$_SESSION['fund_id']}";
                $this->db->query($update_cash_on_hand);

                // Insert History
                $this->save_return_fund_history($return->rfid, 1);
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
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
                } else {
		  $_SESSION['warning'][] = 'Something went wrong.';
                  redirect($_SERVER['HTTP_REFERER']);
                }
	}

	public function load_return($rfid)
	{
                return $this->db->query("
                  SELECT
                    rf.rfid, v.fund AS fund_id, rf.amount, rf.slip,
                    DATE_FORMAT(rf.liq_date, '%Y-%m-%d') AS liq_date,
                    v.reference, st.status_name AS status
                  FROM
                    tbl_return_fund rf
                  INNER JOIN
                    tbl_voucher v ON v.vid = rf.fund
                  INNER JOIN
                    tbl_return_fund_history rfh1 ON rf.rfid = rfh1.rfid
                  LEFT JOIN
                    tbl_return_fund_history rfh2 ON rfh1.rfid = rfh2.rfid AND rfh1.return_fund_history_id < rfh2.return_fund_history_id
                  INNER JOIN
                    tbl_status st ON st.status_id = rfh1.status_id AND status_type = 'RETURN_FUND'
                  WHERE
                    rfh2.return_fund_history_id IS NULL AND rf.rfid = ".$rfid
                )->row();
	}

	public function liquidate_return($rfid)
	{
		$return = new Stdclass();
		$return->liq_date = date('Y-m-d H:i:s');
		$this->db->update('tbl_return_fund', $return, array('rfid' => $rfid));

                $this->save_return_fund_history($rfid, 30);
		$_SESSION['messages'][] = 'Transaction updated successfully.';
		redirect('return_fund/view/'.$rfid);
	}

        public function save_return_fund_history($rfid, $status_id)
        {
          $return_fund_history = array(
            'rfid' => $rfid,
            'status_id' => $status_id,
            'created_by' => $_SESSION['username']
          );
          $this->db->insert('tbl_return_fund_history', $return_fund_history);
        }

        public function correct_amount($rfid, $amount)
        {
          $cash_on_hand = $this->fund->get_cash_on_hand($_SESSION['fund_id']);
          $this->form_validation->set_rules(
            'amount',
            'Amount',
            'required|is_numeric|non_zero|less_than_equal_to['.$cash_on_hand.']',
            array('less_than_equal_to' => 'The amount must be less than or equal to Cash on Hand.')
          );
          if ($this->form_validation->run()) {
            $this->db->trans_start();
            $update_cash_on_hand = "UPDATE tbl_fund SET cash_on_hand = cash_on_hand - {$amount} WHERE fid = {$_SESSION['fund_id']}";
            $this->db->query($update_cash_on_hand);

            $update_amount = "UPDATE tbl_return_fund SET amount = {$amount} WHERE rfid = {$rfid}";
            $this->db->query($update_amount);

            // Status For Liquidation
            $return_fund_history = array(
              'rfid' => $rfid,
              'status_id' => 1,
              'created_by' => $_SESSION['username']
            );
            $this->db->insert('tbl_return_fund_history', $return_fund_history);
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
              $_SESSION['messages'][] = 'Return fund amount has been updated successfully';
            } else {
              $_SESSION['warning'][] = 'Something wen\'t wrong.';
            }
          }
        }
}
