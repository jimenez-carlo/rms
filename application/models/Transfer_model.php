<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Transfer_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function load($tfid)
	{
		$transfer = $this->db->get_where('tbl_transfer'
			, array('tfid' => $tfid))->row();
		$transfer->sales = $this->db->get_where('tbl_sales'
			, array('sid' => $transfer->sales))->row();
		$transfer->sales->engine = $this->db->get_where('tbl_engine'
			, array('eid' => $transfer->sales->engine))->row();
		$transfer->sales->customer = $this->db->get_where('tbl_customer'
			, array('cid' => $transfer->sales->customer))->row();

		return $transfer;
	}
	
	public function save(&$transfer)
	{
		// if the post date is empty, use current timestamp
		if (empty($transfer->post_date))
		{
			$transfer->post_date = date("Y-m-d H:i:s");
		}

		// if the old cr no is not provided, use the cr no from the sales object
		if (empty($transfer->old_cr_no))
		{
			$transfer->old_cr_no = $transfer->sales->cr_no;
		}

		// if the object was from the load function, return only the sid from the sales object
		if (!is_numeric($transfer->sales))
		{
			$transfer->sales = $transfer->sales->sid;
		}

		// if the tfid is provided, then update, else insert new record
		if ($transfer->tfid)
		{
			$this->db->update("tbl_transfer", $transfer, array("tfid" => $transfer->tfid));
		}
		else
		{
			$this->db->insert("tbl_transfer", $transfer);

			$transfer->tfid = $this->db->insert_id();
		}

		// reload transfer object
		$transfer = $this->load($transfer->tfid);
	}

	public function update_transfer($tmid,$tfid)
	{
		$this->db->query("update tbl_transfer
						set transmittal = ".$tmid.",
						transmittal_date = '".date('Y-m-d H:i:s')."'
						where tfid = ".$tfid);
	}
}