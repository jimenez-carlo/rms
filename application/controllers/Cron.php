<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
		$this->load->helper('directory');
		$this->load->model('Login_model', 'login');
		$this->global  = $this->load->database('global', TRUE);
		$this->dev_rms = $this->load->database('dev_rms', TRUE);
		$this->mdi_dev_rms = $this->load->database('mdi_dev_rms', TRUE);
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Cron');
		$this->header_data('nav', 'cron');
		$this->header_data('dir', './');

		// on generate
		$submit = $this->input->post('submit');
		if (!empty($submit))
		{
			$key = current(array_keys($submit));
			$this->manual($key);
		}

		// on force
		$force = $this->input->post('force');
		if (!empty($force))
		{
			$this->force($force);
		}

		$this->template('cron/view');
	}

	public function manual($key)
	{
		$date_yesterday = $this->input->post('date_yesterday');
		if (substr($date_yesterday, 0, 10) <= date('Y-m-d', strtotime('-1 days')))
		{
			$log = $this->db->query("select * from tbl_cron_log
				where ckey = ".$key."
				and date = '".$date_yesterday."'")->row();

			if (empty($log))
			{
				switch($key)
				{
					case 1: $this->rms_create(); break;
					case 2: $this->rms_expense(); break;
					case 3: $this->ar_amount(); break;
				}
				$_SESSION['messages'][] = 'Cron executed successfully.';
			}
			else {
				$_SESSION['warning'][] = 'Cron was executed before on '.date('F j, Y H:i:s', strtotime($log->start)).'. <span id="trigger-force" data-key="'.$key.'" class="hide"></span>';
			}
		}
		else {
			$_SESSION['warning'][] = 'Cannot execute future cron date.';
		}
	}

	public function force($key)
	{
		$date_yesterday = $this->input->post('date_yesterday');
		if (substr($date_yesterday, 0, 10) <= date('Y-m-d', strtotime('-1 days')))
		{
			switch($key)
			{
				case 1: $this->rms_create(); break;
				case 2: $this->rms_expense(); break;
				case 3: $this->ar_amount(); break;
			}
			$_SESSION['messages'][] = 'Cron executed successfully.';
		}
		else {
			$_SESSION['warning'][] = 'Cannot execute future cron date.';
		}
	}

	public function rms_create()
	{
		$start = date("Y-m-d H:i:s");
		$rows = 0;

		$date_yesterday = $this->input->post('date_yesterday');
		$date_yesterday = (empty($date_yesterday)) ? date('Y-m-d', strtotime('-1 days')) : $date_yesterday;
		$date_from = date('Y-m-d', strtotime('-7 days'));

                $query = <<<QRY
                  SELECT
                    c.*, r.*, si_mat_no, regn_status, date_created
                  FROM
                    customer_tbl c
		  INNER JOIN rrt_reg_tbl r ON branch_code = branch
		  INNER JOIN si_tbl_print ON si_engin_no = engine_no AND si_custcode = customer_id
		  INNER JOIN regn_status ON engine_nr = engine_no
		  INNER JOIN transmittal_tbl ON transmittal_code = transmittal_no
		  WHERE left(date_sold, 10) >= '2018-08-01'
		  AND (left(date_created, 10) BETWEEN '$date_from' AND  '$date_yesterday')
QRY;
                $dev_rms_result     = $this->dev_rms->query($query)->result_object();
                $mdi_dev_rms_result = $this->mdi_dev_rms->query($query)->result_object();


                var_dump(array($dev_rms_result, $mdi_dev_rms_result)); die();
		foreach ($result as $row)
		{
			// branch dtls
			switch ($row->rrt_class)
			{
				case 'NCR': $region = 1; $r_code = 'NCR'; break;
				case 'REGION 1': $region = 2; $r_code = 'R1'; break;
				case 'REGION 2': $region = 3; $r_code = 'R2'; break;
				case 'REGION 3': $region = 4; $r_code = 'R3'; break;
				case 'REGION 4': $region = 5; $r_code = 'R4'; break;
				case 'REGION 4 B': $region = 6; $r_code = 'R4b'; break;
				case 'REGION 5': $region = 7; $r_code = 'R5'; break;
				case 'REGION 6': $region = 8; $r_code = 'R6'; break;
				case 'REGION 7': $region = 9; $r_code = 'R7'; break;
				case 'REGION 8': $region = 10; $r_code = 'R8'; break;
				case 'IX': $region = 11; $r_code = 'IX'; break;
				case 'X': $region = 12; $r_code = 'X'; break;
				case 'XI': $region = 13; $r_code = 'XI'; break;
				case 'XII': $region = 14; $r_code = 'XII'; break;
				case 'XIII': $region = 15; $r_code = 'XIII'; break;
				default: $region = 0;
			}
                        if ($region > 10) {
                          $company = 8; // MDI
                        } else {
                          $company = (substr($row->branch, 0, 1) == 6)
                              ? 2
                              : substr($row->branch, 0, 1);
                        }

			$branch = $global->query("select * from tbl_branches where b_code = '".$row->branch."'")->row();

			// lto_transmittal
			$code = 'LT-'.$r_code.'-'
				.substr($row->branch, 0, 1).'0'
				.substr($row->date_created, 2, 2)
				.substr($row->date_created, 5, 2)
				.substr($row->date_created, 8, 2);
			$transmittal = $this->db->query("select * from tbl_lto_transmittal
				where code = '".$code."'")->row();
			if (empty($transmittal))
			{
				$transmittal = new Stdclass();
				$transmittal->date = $row->date_created;
				$transmittal->code = $code;
				$transmittal->region = $region;
				$transmittal->company = $company;
				$this->db->insert('tbl_lto_transmittal', $transmittal);
				$transmittal->ltid = $this->db->insert_id();
			}

			// engine
			//if($row->engine_no == 'E472-800781'){
			//echo "<script>alert('a21');</script>";
			//}
			$engine = $this->db->query("select * from tbl_engine
				where engine_no = '".$row->engine_no."'")->row();
			if (empty($engine))
			{
				$engine = new Stdclass();
				$engine->engine_no = $row->engine_no;
				$engine->chassis_no = $row->chassis_no;
				$engine->mat_no = $row->si_mat_no;
				$this->db->insert('tbl_engine', $engine);
				$engine->eid = $this->db->insert_id();
			}

			// customer
			$customer = $this->db->query("select * from tbl_customer
				where cust_code = '".$row->customer_id."'")->row();
			if (empty($customer))
			{
				$customer = new Stdclass();
				$customer->first_name = $row->first_name;
				$customer->last_name = $row->last_name;
				$customer->cust_code = $row->customer_id;
				$customer->cust_type = (empty($row->first_name) || empty($row->last_name)) ? 1 : 0;
				$this->db->insert('tbl_customer', $customer);
				$customer->cid = $this->db->insert_id();
			}

			// sales
			$sales = $this->db->query("select * from tbl_sales
				where engine = ".$engine->eid."
				and customer = ".$customer->cid)->row();
			if (empty($sales))
			{
				$sales = new Stdclass();
				$sales->engine = $engine->eid;
				$sales->customer = $customer->cid;
				$sales->branch = (!empty($branch)) ? $branch->bid : 0;
				$sales->bcode = $row->branch_code;
				$sales->bname = $row->branch_name;
				$sales->region = $region;
				$sales->company = $company;
				$sales->date_sold = $row->date_sold;
				$sales->si_no = $row->sales_invoice;
				$sales->ar_no = $row->ar_no;
				$sales->amount = 0;
				$sales->sales_type = ($row->sale_type == 465) ? 0 : 1;
				$sales->registration_type = $row->regn_status;
				$sales->transmittal_date = $row->date_created;
				$sales->lto_transmittal = $transmittal->ltid;
				$this->db->insert('tbl_sales', $sales);
				$sales->sid = $this->db->insert_id();

				$rows++;
			}
		}

		$end = date("Y-m-d H:i:s");

		$log = new Stdclass();
		$log->ckey = 1;
		$log->date = $date_yesterday;
		$log->start = $start;
		$log->end = $end;
		$log->rows = $rows;
		$this->db->insert('tbl_cron_log', $log);

		$this->login->saveLog('Run Cron: Create RMS data based on data from LTO Transmittal System [rms_create] Duration: '.$start.' - '.$end);

		$submit = $this->input->post('submit');
		if (empty($submit)) redirect('cron');
	}

	public function rms_expense()
	{
		$start = date("Y-m-d H:i:s");
		$current_date = date("Y-m-d");
		$rows = 0;

		$dev_ces2 = $this->load->database('dev_ces2', TRUE);

		$date_yesterday = $this->input->post('date_yesterday');
		$date_yesterday = (empty($date_yesterday))
			        ? date('Y-m-d', strtotime('-1 days')) : $date_yesterday;
		$date_from = date('Y-m-d', strtotime('-3 days'));

		// update lto pending sales
		//$result = $this->db->query('select sid, engine_no, cust_code,
		//		left(pending_date, 10) as pending_date, registration,
		//		left(cr_date, 10) as cr_date
		//	from tbl_sales s
		//	inner join tbl_customer c on customer = cid
		//	inner join tbl_engine e on engine = eid
		//	where left(pending_date,10) = "'.$date_yesterday.'"
		//		or left(registration_date,10) = "'.$date_from.'"')->result_object();


		$query  = "SELECT sid, engine_no, cust_code, ";
		$query .= "LEFT(pending_date, 10) AS pending_date, registration, ";
		$query .= "LEFT(cr_date, 10) AS cr_date ";
		$query .= "FROM tbl_sales s ";
		$query .= "INNER JOIN tbl_customer c ON customer=cid ";
		$query .= "INNER JOIN tbl_engine e ON engine=eid ";
		$query .= 'WHERE (left(pending_date,10) BETWEEN "'.$date_from.'" AND "'.$current_date.'") ';
		$query .= 'OR (left(registration_date,10) BETWEEN "'.$date_from.'" AND "'.$current_date.'")';

		$result = $this->db->query($query)->result_object();

		// FOR DEBUGGING
		//print_r(array($start, $current_date, $date_yesterday, $date_from, $query, $result));
		//exit;


		//START: PRESERVE THIS BLOCK OF CODE FOR OPTIMIZATION BUT USE IN DEV ENVIRONMENT INSTEAD OF PRODUCTION - Jake
		//foreach ($result as $row){
		//	$engine_nums[] = $row->engine_no;
		//	$cust_codes[]  = $row->cust_code;
		//}

		//$dev_ces2->select('rec_no');
		//$dev_ces2->from('rms_expense');
		//$dev_ces2->where_not_in('engine_num', $engine_nums);
		//$dev_ces2->where_not_in('custcode', $cust_codes);
		//$expense_not_exist = $dev_ces2->get()->result_array();
		//print_r(array('NOT_EXIST', $expense_not_exist));
		//exit;

		//$dev_ces2->select('rec_no');
		//$dev_ces2->from('rms_expense');
		//$dev_ces2->where_in('engine_num', $engine_nums);
		//$dev_ces2->where_in('custcode', $cust_codes);
		//$expense_exist = $dev_ces2->get()->result_array();

		//END

		//print_r(array('EXIST', $expense_exist));
		//exit;

		foreach ($result as $row)
		{
			$expense = $dev_ces2->query("select rec_no from rms_expense
				where engine_num = '".$row->engine_no."'
				and custcode = '".$row->cust_code."'")->row();

			if (empty($expense))
			{
				$expense = new Stdclass();
				$expense->nid = 0;
				$expense->custcode = $row->cust_code;
				$expense->engine_num = $row->engine_no;
				$expense->tip_conf = 0;
				$expense->tip_conf_d = $row->pending_date;
				if (!empty($row->cr_date)) $expense->regn_exp = $row->registration;
				if (!empty($row->cr_date)) $expense->regn_exp_d = $row->cr_date;
				$dev_ces2->insert('rms_expense', $expense);
			}
			else
			{
				$expense->tip_conf = 0;
				$expense->tip_conf_d = $row->pending_date;
				if (!empty($row->cr_date)) $expense->regn_exp = $row->registration;
				if (!empty($row->cr_date)) $expense->regn_exp_d = $row->cr_date;
				$dev_ces2->update('rms_expense', $expense, array('rec_no' => $expense->rec_no));
			}
			$rows++;
		}

		$end = date("Y-m-d H:i:s");

		$log = new Stdclass();
		$log->ckey = 2;
		$log->date = $date_yesterday;
		$log->start = $start;
		$log->end = $end;
		$log->rows = $rows;
		$this->db->insert('tbl_cron_log', $log);

		$this->login->saveLog('Run Cron: Update rms_expense table for BOBJ Report [rms_expense] Duration: '.$start.' - '.$end);

		$submit = $this->input->post('submit');
		if (!empty($submit)) redirect('cron');
	}

	public function ar_amount()
	{
		$start = date("Y-m-d H:i:s");
		$rows = 0;

		$dev_ces2 = $this->load->database('dev_ces2', TRUE);

		// select sales with AR and zero amount
		$result = $this->db->query("select sid, bcode, ar_no, cust_code
			from tbl_sales s
			inner join tbl_customer c on customer = cid
			where amount = 0 and not ar_no = 'N/A'
			limit 1000")->result_object();
		foreach ($result as $row)
		{
			$ar = $dev_ces2->query("select amount from ar_amount_tbl
				where branch = '".$row->bcode."'
				and cust_cd = '".$row->cust_code."'
				and ar_no = '".addslashes($row->ar_no)."'")->row();

			$sales = new Stdclass();
			$sales->amount = (!empty($ar)) ? $ar->amount : 0;

			$this->db->update('tbl_sales', $sales, array('sid' => $row->sid));
			$rows++;
		}
		$end = date("Y-m-d H:i:s");

		$log = new Stdclass();
		$log->ckey = 3;
		$log->date = date('Y-m-d');
		$log->start = $start;
		$log->end = $end;
		$log->rows = $rows;
		$this->db->insert('tbl_cron_log', $log);

		$this->login->saveLog('Run Cron: Update Sales AR Amount from BOBJ [ar_amount] Duration: '.$start.' - '.$end);

		$submit = $this->input->post('submit');
		if (!empty($submit)) redirect('cron');
	}

	public function empty_temp()
	{
		$start = date("Y-m-d H:i:s");
		$rows = 0;

		$folder = './rms_dir/temp/';
		$this->load->helper('directory');
		$dir_files = directory_map($folder, 1);

		// delete dir files
		foreach ($dir_files as $file) {
			if (!empty($file)) {
				unlink($folder.$file);
				$rows++;
			}
		}

		$end = date("Y-m-d H:i:s");

		$log = new Stdclass();
		$log->ckey = 4;
		$log->date = date('Y-m-d');
		$log->start = $start;
		$log->end = $end;
		$log->rows = $rows;
		$this->db->insert('tbl_cron_log', $log);

		$this->login->saveLog('Run Cron: Delete files from rms_dir/temp [empty_temp] Duration: '.$start.' - '.$end);

		$submit = $this->input->post('submit');
		if (empty($submit)) redirect('cron');
	}
}
