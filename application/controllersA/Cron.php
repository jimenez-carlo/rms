<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
		$this->load->helper('directory');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Cron');
		$this->header_data('nav', 'cron');
		$this->header_data('dir', './');

		$this->load->model('Login_model', 'login');
		$submit = $this->input->post('submit');

		// on generate
		if (!empty($submit))
		{
			foreach ($submit as $key => $val)
			{
				switch($key)
				{
					case 1:
						$this->data_storage1();
						$this->login->saveLog('Run Cron: Create RMS data based on data from BOBJ ['.$key.']');
						break;
					case 2:
						$this->update_sales();
						$this->login->saveLog('Run Cron: Update RMS data based on data from BOBJ ['.$key.']');
						break;
					case 3:
						$this->update_sales_regn();
						$this->login->saveLog('Run Cron: Update RMS data with AR and Registration Status from BOBJ ['.$key.']');
						break;
					case 4:
						$this->lto_transmittal();
						$this->login->saveLog('Run Cron: Update RMS data with transmittal_date from LTO Transmittal System ['.$key.']');
						break;
					case 5:
						$this->rms_expense();
						$this->login->saveLog('Run Cron: Update rms_expense table for BOBJ Report ['.$key.']');
						break;
				}
			}

			$_SESSION['messages'][] = 'Done.';
		}

		$this->template('cron/view');
	}

	public function data_storage1()
	{
		print date("Y-d-m H:i:s");
		$dev_ces2 = $this->load->database('dev_ces2', TRUE);
		$global = $this->load->database('global', TRUE);

		$result = $dev_ces2->query("select * from data_storage1
			where str_to_date(substr(field_ces_date_sold_value, 2), '%m/%d/%Y') >= '2018-04-01'")->result_object();
		foreach ($result as $row)
		{
			// engine
			$engine = $this->db->query("select * from tbl_engine where engine_no = '".$row->field_engin_no_value."'")->row();
			if (empty($engine))
			{
				$engine = new Stdclass();
				$engine->engine_no = $row->field_engin_no_value;
				$engine->chassis_no = $row->field_chass_no_value;
				$this->db->insert('tbl_engine', $engine);
				$engine->eid = $this->db->insert_id();

				$engine = $this->db->query("select * from tbl_engine where eid = ".$engine->eid)->row();
			}

			// customer
			$customer = $this->db->query("select * from tbl_customer where cust_code = '".$row->field_cust_code_value."'")->row();
			if (empty($customer))
			{
				$customer = new Stdclass();
				$customer->first_name = $row->field_ces_cust_fname_value;
				$customer->last_name = $row->field_ces_cust_lastname_value;
				$customer->cust_code = $row->field_cust_code_value;
				$customer->cust_type = (empty($row->field_ces_cust_fname_value)
					|| empty($row->field_ces_cust_lastname_value)) ? 1 : 0;
				$this->db->insert('tbl_customer', $customer);
				$customer->cid = $this->db->insert_id();

				$customer = $this->db->query("select * from tbl_customer where cid = ".$customer->cid)->row();
			}

			$sales = $this->db->query("select * from tbl_sales
				where engine = ".$engine->eid."
				and customer = ".$customer->cid)->row();
			if (empty($sales))
			{
				$branch = $global->query("select * from tbl_branches where b_code = '".$row->field_bcode_value."'")->row();

				if (!empty($branch))
				{
					$sales = new Stdclass();
					$sales->post_date = date('Y-m-d H:i:s');
					$sales->engine = $engine->eid;
					$sales->customer = $customer->cid;
					$sales->branch = $branch->bid;
					$sales->date_sold = date("Y-m-d", strtotime(substr($row->field_ces_date_sold_value, 1)));
					$sales->si_no = $row->field_ces_si_no_value;
					$sales->ar_no = $row->field_ces_ref_ar_no_value;
					$sales->amount = $row->field_ces_cust_amt_given_value;
					$sales->sales_type = ($row->field_sales_type_value == 'ZMCC') ? 0 : 1;

					$this->db->insert('tbl_sales', $sales);
					$sales->sid = $this->db->insert_id();

					// insert expense
					$expense = new Stdclass();
					$expense->nid = $sales->sid;
					$expense->custcode = $customer->cust_code;
					$expense->engine_num = $engine->engine_no;
					$this->db->insert('rms_expense', $expense);
				}
			}
		}
		print date("Y-d-m H:i:s");
	}

	public function update_sales()
	{
		print date("Y-d-m H:i:s");
		$dev_ces2 = $this->load->database('dev_ces2', TRUE);
		$global = $this->load->database('global', TRUE);

		$result = $dev_ces2->query('select * from data_storage1')->result_object();
		foreach ($result as $row)
		{
			$engine = $this->db->query("select * from tbl_engine where engine_no = '".$row->field_engin_no_value."'")->row();
			$customer = $this->db->query("select * from tbl_customer where cust_code = '".$row->field_cust_code_value."'")->row();

			if (!empty($engine) && !empty($customer))
			{
				$customer->first_name = $row->field_ces_cust_fname_value;
				$customer->last_name = $row->field_ces_cust_lastname_value;
				$customer->cust_type = (empty($row->field_ces_cust_fname_value)
					|| empty($row->field_ces_cust_lastname_value)) ? 1 : 0;
				$this->db->update('tbl_customer', $customer, array('cid' => $customer->cid));

				$sales = $this->db->query("select * from tbl_sales
					where engine = ".$engine->eid."
					and customer = ".$customer->cid)->row();

				if (!empty($sales))
				{
					$branch = $global->query("select * from tbl_branches where b_code = '".$row->field_bcode_value."'")->row();

					$sales->branch = $branch->bid;
					$sales->date_sold = date("Y-m-d", strtotime(substr($row->field_ces_date_sold_value, 1)));
					$sales->si_no = $row->field_ces_si_no_value;
					$sales->ar_no = $row->field_ces_ref_ar_no_value;
					$sales->amount = $row->field_ces_cust_amt_given_value;
					$sales->sales_type = ($row->field_sales_type_value == 'ZMCC') ? 0 : 1;

					$this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));
				}
			}
		}
		print date("Y-d-m H:i:s");
	}

	public function update_sales_regn()
	{
		print date("Y-d-m H:i:s");
		$dev_ces2 = $this->load->database('dev_ces2', TRUE);
		$dev_rms = $this->load->database('dev_rms', TRUE);
		$date_15 = date('Y-m-d', strtotime('-7 days'));

		// update sales posted within 15 days
		$sales = $this->db->query('select sid,cust_code,engine_no,registration_type
							from tbl_sales
							inner join tbl_customer c on customer=cid
							inner join tbl_engine e on engine=eid
							where left(post_date, 10) > "'.$date_15.'"')->result_object();
		foreach ($sales as $sale)
		{
			// update sales registration status
			$regn_status = $dev_rms->query('select regn_status from regn_status
				where cust_id = "'.$sale->cust_code.'"
				and engine_nr = "'.$sale->engine_no.'"')->row();
			if (!empty($regn_status))
			{
				$obj_r = new Stdclass();
				$obj_r->registration_type = $regn_status->regn_status;
				$obj_r->is_self = ($regn_status->regn_status == 'Self Registration');
				$this->db->update('tbl_sales', $obj_r, array('sid' => $sale->sid));
			}

			// update sales ar assignment
			$ar = $dev_ces2->query('select * from ar_engine_tbl
				inner join ar_amount_tbl on ar_num = ar_no
				where cust_id = "'.$sale->cust_code.'"
				and engine_num = "'.$sale->engine_no.'"')->row();
			if (!empty($ar))
			{
				$obj_r = new Stdclass();
				$obj_r->ar_no = $ar->ar_num;
				$obj_r->amount = $ar->amount;
				$this->db->update('tbl_sales', $obj_r, array('sid' => $sale->sid));
			}
		}
		print date("Y-d-m H:i:s");
	}

	public function lto_transmittal()
	{
		print date("Y-d-m H:i:s");
		$global = $this->load->database('global', TRUE);
		$dev_rms = $this->load->database('dev_rms', TRUE);
		$date_yesterday = date('Y-m-d', strtotime('-1 days'));

		// set transmittal date
		$result = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where transmittal_date is null")->result_object();
		foreach ($result as $row)
		{
			$transmittal = $dev_rms->query("select date_created from customer_tbl
				inner join transmittal_tbl on transmittal_code = transmittal_no
				where engine_no = '".$row->engine_no."'
				and customer_id = '".$row->cust_code."'")->row();

			if (!empty($transmittal))
			{
				$this->db->query("update tbl_sales
					set transmittal_date = '".$date_yesterday."'
					where sid = ".$row->sid);
			}
		}

		// transmittal
		$result = $this->db->query("select group_concat(sid) as sid, branch, cust_type
			from tbl_sales
			inner join tbl_customer on customer = cid
			where left(transmittal_date, 10) = '".$date_yesterday."'
			and is_self = 0
			group by branch, cust_type")->result_object();
		foreach ($result as $row)
		{
			$transmittal = new Stdclass();
			$transmittal->date = $date_yesterday;
			$transmittal->branch = $row->branch;
			$transmittal->cust_type = $row->cust_type;
			$transmittal->status = 0;
			$this->db->insert('tbl_lto_transmittal', $transmittal);
			$transmittal->ltid = $this->db->insert_id();

			$this->db->query("update tbl_sales
				set lto_transmittal = ".$transmittal->ltid."
				where sid in (".$row->sid.")");
		}

		// projected fund
		$result = $this->db->query("select * from tbl_fund")->result_object();
		foreach ($result as $fund)
		{
			// branches
			$bcode = ($fund->company == 2) ? 6 : $fund->company;
			$branches = $global->query("select group_concat(bid) as x 
				from tbl_branches
				where left(b_code,1) = '".$bcode."' 
				and ph_region = ".$fund->region)->row()->x;
			if (empty($branches)) $branches = "''";

			// sales
			$sales = $this->db->query("select 
				ifnull(sum(case when sales_type = 1 
					then 1 else 0 end), 0) as unit_cash,
				ifnull(sum(case when sales_type = 0 
					then 1 else 0 end), 0) as unit_inst
				from tbl_sales
				where is_self = 0
				and left(transmittal_date, 10) = '".$date_yesterday."'
				and branch in (".$branches.")")->row();

			if (($sales->unit_cash + $sales->unit_inst) > 0)
			{
				$projected = new Stdclass();
				$projected->fund = $fund->fid;
				$projected->date = $date_yesterday;
				$projected->amount = ($sales->unit_cash + $sales->unit_inst) * 1200;
				$projected->unit_cash = $sales->unit_cash;
				$projected->unit_inst = $sales->unit_inst;
				$projected->amount_cash = 1200;
				$projected->amount_inst = 1200;
				$this->db->insert('tbl_fund_projected', $projected);
				$projected->fpid = $this->db->insert_id();

				$this->db->query("update tbl_sales
					set projected = ".$projected->fpid."
					where is_self = 0
					and left(transmittal_date, 10) = '".$date_yesterday."'
					and branch in (".$branches.")");
			}
		}
		print date("Y-d-m H:i:s");
	}

	public function rms_expense()
	{
		print date("Y-d-m H:i:s");
		$dev_ces2 = $this->load->database('dev_ces2', TRUE);
		$date_yesterday = date('Y-m-d', strtotime('-1 days'));

		// update lto pending sales
		$tip = $this->db->query('select sid,pending_date
								from tbl_sales s
								where left(pending_date,10)="'.$date_yesterday.'"')->result_object();
		foreach ($tip as $t) {
			$obj_t = new Stdclass();
			$obj_t->tip_conf = 0;
			$obj_t->tip_conf_d = $t->pending_date;
			$dev_ces2->update('rms_expense', $obj_t, array('nid' => $sale->sid));
		}

		// update registered sales
		$sales = $this->db->query('select sid,registration,registration_date
								from tbl_sales s
								inner join tbl_customer c on customer=cid
								inner join tbl_engine e on engine=eid
								where left(registration_date,10)="'.$date_yesterday.'"')->result_object();
		foreach ($sales as $sale) {
			$obj_e = new Stdclass();
			$obj_e->regn_exp = $sale->registration;
			$obj_e->regn_exp_d = $sale->registration_date;
			$dev_ces2->update('rms_expense', $obj_e, array('nid' => $sale->sid));
		}
		print date("Y-d-m H:i:s");
	}
}