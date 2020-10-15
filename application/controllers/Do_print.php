<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Do_print extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
	}

	public function transmittal($tmid = 0)
	{
		if (!$tmid)
		{
			show_404();
		}

		if($tmid) {
			$this->load->model('Sales_transmittal_model', 'sales_transmittal');
			$this->load->model('Transmittal_model', 'transmittal');
			$this->load->model('Sales_model', 'sales');
			$this->load->model('Customer_model', 'customer');
			$this->load->model('Engine_model', 'engine');
			$this->load->model('Cmc_model', 'cmc');

			$get_stid = $this->sales_transmittal->get_id(array('transmittal' => $tmid));

			$sales = array();
			foreach ($get_stid as $row)
			{
				$this->sales_transmittal->load($row);
				$this->sales->load($this->sales_transmittal->sales);
				$this->customer->load($this->sales->customer);
				$this->engine->load($this->sales->engine);

				$record = new Stdclass();
				$record->stid = $this->sales_transmittal->stid;
				$record->sid = $this->sales->sid;
				$record->date_sold = $this->sales->date_sold;
				$record->amount = $this->sales->amount;
				$record->ar_no = $this->sales->ar_no;
				$record->si_no = $this->sales->si_no;
				$record->sales_type = $this->sales->sales_type;
				$record->registration_type = $this->sales->registration_type;
				$record->engine_no = $this->engine->engine_no;
				$record->first_name = $this->customer->first_name;
				$record->middle_name = $this->customer->middle_name;
				$record->last_name = $this->customer->last_name;
				$record->insurance = $this->sales->insurance;
				$record->registration = $this->sales->registration;
				$record->tip = $this->sales->tip;
				$record->emission = $this->sales->emission;
				$record->is_rejected = $this->sales_transmittal->is_rejected;
				$record->remarks = $this->sales_transmittal->remarks;
				$sales[] = $record;
			}

			$this->transmittal->load($tmid);
			$res = $this->cmc->get_full_branch_name($this->transmittal->branch);
			$data['branch'] = $res[0]->branch_name;
			$data['transmittal'] = $this->transmittal;
			$data['sales'] = $sales;
			$this->load->view('transmittal_print_view', $data);
		}
	}

	public function sap()
	{
		$global = $this->load->database('global', TRUE);

		$result = array();
		$ctr = 1;

		$exp_date = $this->input->post('exp_date');
		$exp_date = DateTime::createFromFormat('m/d/Y', $exp_date)->format("Y-m-d");
		$region = $this->input->post('region');

		$branches = $global->query("select * from tbl_branches
			inner join tbl_ph_regions on region = phrid
			where ph_region = ".$region)->result_object();

		foreach ($branches as $branch)
		{
			$sales = $this->db->query("select * from tbl_transmittal
				inner join tbl_sales_transmittal on transmittal = tmid
				inner join tbl_sales on sales = sid
				inner join tbl_customer on customer = cid
				where left(end_date, 10) = '".$exp_date."'
				and tbl_transmittal.branch = ".$branch->bid)->result_object();

			foreach ($sales as $row)
			{
				$obj = new Stdclass();
				$obj->count = $ctr;
				$ctr++;

				$obj->b_code = $branch->b_code;
				$obj->account_key = $branch->account_key;
				$obj->company = substr($branch->b_code, 0, 1)."000";

				if ($obj->company == "1000") $obj->sap_code = "219".substr($branch->b_code, 1, 3);
				else $obj->sap_code = "219".substr($branch->b_code, 0, 1).substr($branch->b_code, 2, 2);

				$obj->date_sold = $row->date_sold;
				$obj->created = $row->date_sold;
				$obj->ar_no = $row->ar_no;
				$obj->expense = ($row->insurance + $row->emission + $row->registration + $row->tip);
				$obj->name = $row->last_name.", ".$row->first_name." ".$row->middle_name;

				$result[] = $obj;
			}
		}

		$data["result"] = $result;
		$this->load->view('accounting/sap_print_view', $data);
	}

	public function top_sheet($exp_date)
	{
		$bid = $_SESSION['branch_code'];

		if (!empty($exp_date) && !empty($bid))
		{
	    	// load topsheet based on branch and date
			$this->load->model("Accounting_model", "accounting");
			$topsheet = $this->accounting->load_topsheet_repo($bid, $exp_date);

			// determine if report has been created
			$arid = $this->db->query("select arid from tbl_acct_report
				where branch = ".$bid."
				and left(exp_date, 10) = '".$exp_date."'")->row();

			$data["topsheet"] = $topsheet;
			$data["is_generated"] = (!empty($arid));
		}

		$this->load->view('accounting/top_sheet_print', $data);
	}

	public function top_sheet_bnew($exp_date, $bid)
	{
		if (!empty($exp_date) && !empty($bid))
		{
	    	// load topsheet based on branch and date
			$this->load->model("Accounting_model", "accounting");
			$topsheet = $this->accounting->load_topsheet_bnew($_SESSION['region_id'], $exp_date, $bid);

			// determine if report has been created
			$arid = $this->db->query("select arid from tbl_acct_report
				where branch = ".$bid."
				and left(exp_date, 10) = '".$exp_date."'")->row();

			$data["topsheet"] = $topsheet;
			$data["is_generated"] = (!empty($arid));
		}

		$this->load->view('accounting/top_sheet_print_bnew', $data);
	}
}


