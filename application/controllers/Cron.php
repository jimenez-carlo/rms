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
                //$date_from = $this->input->post('date_from') ?? date('Y-m-d', strtotime('-7 days'));
                //$date_yesterday = $this->input->post('date_yesterday') ?? date('Y-m-d', strtotime('-1 days'));
                $engine_numbers = '';
                $date_created = '';

                if ($this->input->post('engine_nums')) {
                  $trimmed = trim($this->input->post('engine_nums'), ' \t\n\r\0\"\,\.');
                  $remove_qoute = str_replace(array("'",'"'), '', $trimmed);
                  $fix_comma = str_replace(array(', ', ' ,'), ',', $remove_qoute);
                  $final_format = "'".implode("','", explode(',', $fix_comma))."'";
                  $engine_numbers = "AND engine_no IN ({$final_format})";
                }

                $result = $this->db
                  ->select([
                    'si_bcode', 'si_bname', 'si_sino', 'si_dsold', 'si_custname',
                    'si_firstname', 'si_middlename', 'si_lastname', 'si_suffix',
                    'si_birth_date', 'LOWER(si_email) AS si_email', 'si_engin_no',
                    'si_chassisno', 'si_custcode', 'LEFT(si_bcode, 1) AS company_id',
                    'si_mat_no', 'regn_status', 'ar_no', 'r.rid AS region_id', 'r.r_code',
                    'DATE_FORMAT(date_inserted,"%Y-%m-%d") AS date_inserted',
                    'CASE
                      WHEN si_sales_type = "ZMCC" THEN 0
                      WHEN si_sales_type = "ZMCF" THEN 1
                    END AS si_sales_type',
                    'REPLACE(
                      IF( CHAR_LENGTH(si_phone_number) = 10, CONCAT("0", si_phone_number), si_phone_number),
                      "-", ""
                    ) AS si_phone_number'
                  ], false)
                  ->where('regn_status != "Self Registration"')
                  ->where("DATE_FORMAT(date_inserted,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')")
                  ->join('tbl_region r', 'r.rid = tbs.rrt_region','inner')
                  ->get('tbl_bobj_sales tbs')
                  ->result_object();

                foreach ($result as $row)
                {
                        // lto_transmittal
                        $code = 'LT-'.$row->r_code.'-'
                                .substr($row->si_bcode, 0, 1).'0'
                                .substr($row->date_inserted, 2, 2)
                                .substr($row->date_inserted, 5, 2)
                                .substr($row->date_inserted, 8, 2);

                        $transmittal = $this->db->query("SELECT * FROM tbl_lto_transmittal WHERE code = '".$code."'")->row();

                        if (empty($transmittal))
                        {
                                $transmittal = new Stdclass();
                                $transmittal->date = $row->date_inserted;
                                $transmittal->code = $code;
                                $transmittal->region = $row->region_id;
                                $transmittal->company = $row->company_id;
                                $this->db->insert('tbl_lto_transmittal', $transmittal);
                                $transmittal->ltid = $this->db->insert_id();
                        }

                        $engine = $this->db->query("SELECT * FROM tbl_engine WHERE engine_no = '".$row->si_engin_no."'")->row();
                        if (empty($engine))
                        {
                                $engine = new Stdclass();
                                $engine->engine_no = $row->si_engin_no;
                                $engine->chassis_no = $row->si_chassisno;
                                $engine->mat_no = $row->si_mat_no;
                                $this->db->insert('tbl_engine', $engine);
                                $engine->eid = $this->db->insert_id();
                        }

                        // customer
                        $customer = $this->db->query("SELECT * FROM tbl_customer WHERE cust_code = '".$row->si_custcode."'")->row();
                        if (empty($customer))
                        {
                                $customer = new Stdclass();
                                $customer->first_name = $row->si_firstname;
                                $customer->last_name = $row->si_lastname;
                                $customer->cust_code = $row->si_custcode;
                                $customer->cust_type = (empty($row->si_lastname) || empty($row->si_lastname)) ? 1 : 0;
                                $customer->date_of_birth = $row->si_birth_date;
                                $customer->phone_number = $row->si_phone_number;
                                $customer->email = $row->si_email;
                                $this->db->insert('tbl_customer', $customer);
                                $customer->cid = $this->db->insert_id();
                        }

                        // sales
                        $sales = $this->db->query("SELECT * FROM tbl_sales WHERE engine = ".$engine->eid." AND customer = ".$customer->cid)->row();
                        if (empty($sales))
                        {
                                $sales = new Stdclass();
                                $sales->engine = $engine->eid;
                                $sales->customer = $customer->cid;
                                $sales->bcode = $row->si_bcode;
                                $sales->bname = $row->si_bname;
                                $sales->region = $row->region_id;
                                $sales->company = $row->company_id;
                                $sales->date_sold = $row->si_dsold;
                                $sales->si_no = $row->si_sino;
                                $sales->ar_no = $row->ar_no;
                                $sales->amount = 0;
                                $sales->sales_type = $row->si_sales_type;
                                $sales->registration_type = $row->regn_status;
                                $sales->created_date = date('Y-m-d');
                                $sales->transmittal_date = $row->date_inserted;
                                $sales->lto_transmittal = $transmittal->ltid;
                                $this->db->insert('tbl_sales', $sales);
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

                $this->db->simple_query("SET SESSION group_concat_max_len = 18446744073709551615");
                // select sales with AR and zero amount
                $customers = $this->db->query("
                  SELECT
                    CONCAT(\"'\",GROUP_CONCAT(DISTINCT c.cust_code SEPARATOR \"','\"),\"'\") AS code
                  FROM tbl_sales s
                  INNER JOIN tbl_customer c on s.customer = c.cid
                  WHERE s.amount = 0 OR s.ar_no = 'N/A'
                ")->row_array();

                $ack_receipts = $dev_ces2->query("
                  SELECT
                    a.cust_cd, a.ar_no, a.amount, e.engine_num
                  FROM
                    ar_amount_tbl a, ar_engine_tbl e
                  WHERE
                    a.cust_cd = e.cust_id AND a.ar_no = e.ar_num
                    AND a.cust_cd IN ({$customers['code']})
                ")->result_array();

                foreach ($ack_receipts as $ar)
                {
                  $this->db->query("
                    UPDATE
                      tbl_sales s, tbl_engine e, tbl_customer c
                    SET
                      s.ar_no = '{$ar['ar_no']}',
                      s.amount = '{$ar['amount']}'
                    WHERE
                      e.eid = s.engine AND c.cid = s.customer AND
                      e.engine_no = '{$ar['engine_num']}' AND c.cust_code = '{$ar['cust_cd']}'
                  ");
                }
                $end = date("Y-m-d H:i:s");

                $log = new Stdclass();
                $log->ckey = 3;
                $log->date = date('Y-m-d');
                $log->start = $start;
                $log->end = $end;
                $log->rows = count($ack_receipts);
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
