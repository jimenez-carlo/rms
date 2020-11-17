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
		$rows = 0;

                $start = date("Y-m-d H:i:s");
                $date_from = $this->input->post('date_from') ?? date('Y-m-d', strtotime('-7 days'));
                $date_yesterday = $this->input->post('date_yesterday') ?? date('Y-m-d', strtotime('-1 days'));
                $engine_numbers = '';
                $date_created = '';

                if ($this->input->post('engine_nums')) {
                  $trimmed = trim($this->input->post('engine_nums'), ' \t\n\r\0\"\,\.');
                  $remove_qoute = str_replace(array("'",'"'), '', $trimmed);
                  $fix_comma = str_replace(array(', ', ' ,'), ',', $remove_qoute);
                  $final_format = "'".implode("','", explode(',', $fix_comma))."'";
                  $engine_numbers = "AND engine_no IN ({$final_format})";
                } else {
		  $date_created = "AND (left(date_created, 10) BETWEEN '{$date_from}' AND  '{$date_yesterday}')";
                }

                $query = <<<SQL
                  SELECT
                    c.*, r.*, si_mat_no, regn_status,
                    date_created, si_email,
                    DATE_FORMAT(si_birth_date, '%Y-%m-%d') AS si_birth_date,
                    REPLACE(
                      IF(
                        CHAR_LENGTH(si_phone_number) = 10,
                        CONCAT('0', si_phone_number),
                        si_phone_number
                      ),
                      '-',
                      ''
                    ) AS si_phone_number,
                    ,CASE rrt_class
                      WHEN 'NCR' THEN 1
                      WHEN 'REGION 1' THEN 2
                      WHEN 'REGION 2' THEN 3
                      WHEN 'REGION 3' THEN 4
                      WHEN 'REGION 4' THEN 5
                      WHEN 'REGION 4 B' THEN 6
                      WHEN 'REGION 5' THEN 7
                      WHEN 'REGION 6' THEN 8
                      WHEN 'REGION 7' THEN 9
                      WHEN 'REGION 8' THEN 10
                      WHEN 'IX'   THEN 11
                      WHEN 'X'    THEN 12
                      WHEN 'XI'   THEN 13
                      WHEN 'XII'  THEN 14
                      WHEN 'XIII' THEN 15
                      ELSE 0
                    END AS region
                    ,CASE rrt_class
                      WHEN 'REGION 1'   THEN 'R1'
                      WHEN 'REGION 2'   THEN 'R2'
                      WHEN 'REGION 3'   THEN 'R3'
                      WHEN 'REGION 4'   THEN 'R4'
                      WHEN 'REGION 4 B' THEN 'R4b'
                      WHEN 'REGION 5'   THEN 'R5'
                      WHEN 'REGION 6'   THEN 'R6'
                      WHEN 'REGION 7'   THEN 'R7'
                      WHEN 'REGION 8'   THEN 'R8'
                      ELSE rrt_class
                    END AS r_code
                  FROM
                    customer_tbl c
		  INNER JOIN rrt_reg_tbl r ON branch_code = branch
		  INNER JOIN si_tbl_print ON si_engin_no = engine_no AND si_custcode = customer_id
		  INNER JOIN regn_status ON engine_nr = engine_no
		  INNER JOIN transmittal_tbl ON transmittal_code = transmittal_no
		  WHERE LEFT(date_sold, 10) >= '2018-08-01' {$date_created} {$engine_numbers}
SQL;

                $dev_rms_result     = $this->dev_rms->query($query)->result_object();
                $mdi_dev_rms_result = $this->mdi_dev_rms->query($query)->result_object();
                $result = array_merge($dev_rms_result, $mdi_dev_rms_result);

                $branches_result = $this->global->query("select bid, b_code from tbl_branches")->result_object();
                $branches = array();
                foreach ($branches_result as $branch) {
                  $branches[$branch->b_code] = $branch->bid;
                }

		foreach ($result as $row)
		{
                        $company = substr($row->branch, 0, 1);

			// lto_transmittal
			$code = 'LT-'.$row->r_code.'-'
				.substr($row->branch, 0, 1).'0'
				.substr($row->date_created, 2, 2)
				.substr($row->date_created, 5, 2)
				.substr($row->date_created, 8, 2);

                        $transmittal = $this->db->query("
                          SELECT
                            *
                          FROM
                            tbl_lto_transmittal
                          WHERE
                            code = '".$code."'")->row();

			if (empty($transmittal))
			{
				$transmittal = new Stdclass();
				$transmittal->date = $row->date_created;
				$transmittal->code = $code;
				$transmittal->region = $row->region;
				$transmittal->company = $company;
				$this->db->insert('tbl_lto_transmittal', $transmittal);
				$transmittal->ltid = $this->db->insert_id();
			}

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
				$customer->date_of_birth = $row->si_birth_date;
				$customer->phone_number = $row->si_phone_number;
				$customer->email = $row->si_email;
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
				$sales->branch = (!empty($row->branch)) ? $branches[$row->branch] : 0;
				$sales->bcode = $row->branch_code;
				$sales->bname = $row->branch_name;
				$sales->region = $row->region;
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
		$rows = 0;
		$dev_ces2 = $this->load->database('dev_ces2', TRUE);
		$date_from = $this->input->post('date_from') ?? date('Y-m-d', strtotime('-3 days'));
		$date_to = $this->input->post('date_yesterday') ?? date('Y-m-d');

		$query  = "SELECT sid, engine_no, cust_code, ";
		$query .= "LEFT(pending_date, 10) AS pending_date, registration, ";
		$query .= "LEFT(cr_date, 10) AS cr_date, ";
		$query .= "LEFT(registration_date, 10) AS regn_upload_d ";
		$query .= "FROM tbl_sales s ";
		$query .= "INNER JOIN tbl_customer c ON customer=cid ";
		$query .= "INNER JOIN tbl_engine e ON engine=eid ";
		$query .= 'WHERE (left(pending_date,10) BETWEEN "'.$date_from.'" AND "'.$date_to.'") ';
		$query .= 'OR (left(registration_date,10) BETWEEN "'.$date_from.'" AND "'.$date_to.'")';
		$result = $this->db->query($query)->result_object();

                foreach ($result as $row) {
                  $expense = $dev_ces2->query("SELECT rec_no, custcode FROM rms_expense WHERE engine_num = '{$row->engine_no}'")->row();
                  if (empty($expense)) {
                    $expense = new Stdclass();
                    $expense->nid = 0;
                    $expense->custcode = $row->cust_code;
                    $expense->engine_num = $row->engine_no;
                    $expense->tip_conf = 0;
                    $expense->tip_conf_d = $row->pending_date;
                    $expense->regn_upload_d = $row->regn_upload_d;
                    if (!empty($row->cr_date)) $expense->regn_exp = $row->registration;
                    if (!empty($row->cr_date)) $expense->regn_exp_d = $row->cr_date;
                    $dev_ces2->insert('rms_expense', $expense);

                  } else {
                    $expense->tip_conf = 0;
                    $expense->tip_conf_d = $row->pending_date;
                    $expense->regn_upload_d = $row->regn_upload_d;
                    if ($expense->custcode !== $row->cust_code) $expense->custcode = $row->cust_code; // Update dev_ces2.rms_expense custcode if data is not equal in RMS cust_code.
                    if (!empty($row->cr_date)) $expense->regn_exp = $row->registration;
                    if (!empty($row->cr_date)) $expense->regn_exp_d = $row->cr_date;
                    $dev_ces2->update('rms_expense', $expense, array('rec_no' => $expense->rec_no));
                  }
                  $rows++;
                }

		$end = date("Y-m-d H:i:s");

		$log = new Stdclass();
		$log->ckey = 2;
		$log->date = date('Y-m-d');
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
		$result = $this->db->query("
		  select
		  sid, bcode, ar_no, cust_code
		  from tbl_sales s
		  inner join tbl_customer c on customer = cid
		  where amount = 0 and not ar_no = 'N/A'
		  limit 10000
		")->result_object();
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
