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

  public function rms_create() {
    $start = date("Y-m-d H:i:s");
    # Order by date_sold
    $this->db->simple_query("SET SESSION group_concat_max_len=18446744073709551615");
    $sql = <<<SQL
      SELECT
        CONCAT('LT-',r.r_code,'-',SUBSTR(tbs.si_bcode,1,1),'0',DATE_FORMAT(NOW(), '%y'),DATE_FORMAT(NOW(), '%m'),DATE_FORMAT(NOW(), '%d')) AS transmittal,
        LEFT(si_bcode, 1) AS company_id, r.rid, r.region, COUNT(*) AS count,
        CONCAT('[',GROUP_CONCAT(DISTINCT '{
          "si_bcode":"',si_bcode,'", "si_bname":"',si_bname,'", "si_sino":"',si_sino,'",
          "si_dsold":"',si_dsold,'", "si_custname":"',si_custname,'", "si_cust_type":"',IF(si_firstname IS NULL OR si_lastname IS NULL, 1, 0),'",
          "si_firstname":"',IF(si_lastname IS NULL, CONCAT(TRIM(si_firstname)," ", TRIM(si_middlename)), IFNULL(TRIM(si_firstname),"")),'",
          "si_middlename":"',IF(si_lastname IS NULL, "", si_middlename),'", "si_lastname":"',IFNULL(si_lastname,""),'", "si_suffix":"',IFNULL(si_suffix,""),'",
          "si_birth_date":"',si_birth_date,'", "si_email":"',LOWER(si_email),'", "si_engin_no":"',si_engin_no,'",
          "si_chassisno":"',si_chassisno,'", "si_custcode":"',si_custcode,'", "company_id":"',LEFT(si_bcode, 1),'",
          "si_mat_no":"',si_mat_no,'", "regn_status":"',regn_status,'", "ar_no":"',IFNULL(ar_no,'N/A'),'",
          "ar_amount":"',ar_amount,'", "region_id":"',r.rid,'", "date_inserted":"',date_inserted,'"
          "si_phone_number":"',REPLACE(IF( CHAR_LENGTH(si_phone_number) = 10, CONCAT("0", si_phone_number), si_phone_number), "-", ""),'",
          "si_sales_type":"',CASE WHEN si_sales_type = "ZMCC" THEN 0 WHEN si_sales_type = "ZMCF" THEN 1 END,'",
        }' ORDER BY si_dsold ASC, si_bcode ASC),']') AS sales
      FROM tbl_bobj_sales tbs
      LEFT JOIN tbl_engine e ON e.engine_no = tbs.si_engin_no
      INNER JOIN tbl_region r ON r.rid = tbs.rrt_region
      WHERE e.eid IS NULL
      GROUP BY transmittal, company_id, r.rid
      ORDER BY r.rid
      LIMIT 10
SQL;

    $result = $this->db->query($sql)->result_array();
    $rows = 0;
    foreach ($result as $row) {

      $transmittal = $this->db->query("SELECT * FROM tbl_lto_transmittal WHERE code = '".$row['transmittal']."'")->row_array();
      if (empty($transmittal)) {
        $transmittal = [
          'date' => date('Y-m-d'),
          'code' => $row['transmittal'],
          'region' => $row['rid'],
          'company' => $row['company_id']
        ];
        $this->db->insert('tbl_lto_transmittal', $transmittal);
        $transmittal['ltid'] = $this->db->insert_id();
      }

      foreach (json_decode($row['sales'], 1) as $sale) {
        $engine = $this->db->query("SELECT * FROM tbl_engine WHERE engine_no = '".$sale['si_engin_no']."'")->row_array();
        if (empty($engine)) {
          // engine
          $engine = [
            'engine_no' => $sale['si_engin_no'],
            'chassis_no' => $sale['si_chassisno'],
            'mat_no' => $sale['si_mat_no']
          ];
          $this->db->insert('tbl_engine', $engine);
          $engine['eid'] = $this->db->insert_id();

          // customer
          $customer = $this->db->query("SELECT * FROM tbl_customer WHERE cust_code = '".$sale['si_custcode']."'")->row_array();
          if (empty($customer)) {
            $customer = [
              'first_name' => $sale['si_firstname'],
              'middle_name' => $sale['si_middlename'],
              'last_name' => $sale['si_lastname'],
              'cust_code' => $sale['si_custcode'],
              'cust_type' => $sale['si_cust_type'],
              'date_of_birth' => $sale['si_birth_date'],
              'phone_number' => $sale['si_phone_number'],
              'email' => $sale['si_email']
            ];
            $this->db->insert('tbl_customer', $customer);
            $customer['cid'] = $this->db->insert_id();
          }

          // sales
          $sales = $this->db->query("SELECT * FROM tbl_sales WHERE engine = ".$engine['eid'])->row_array();
          if (empty($sales)) {
            $sales = [
              'engine' => $engine['eid'],
              'customer' => $customer['cid'],
              'bcode' => $sale['si_bcode'],
              'bname' => $sale['si_bname'],
              'region' => $sale['region_id'],
              'company' => $sale['company_id'],
              'date_sold' => $sale['si_dsold'],
              'si_no' => $sale['si_sino'],
              'ar_no' => $sale['ar_no'],
              'ar_amount' => $sale['ar_amount'],
              'sales_type' => $sale['si_sales_type'],
              'registration_type' => $sale['regn_status'],
              'created_date' => date('Y-m-d'),
              'transmittal_date' => $sale['date_inserted'],
              'lto_transmittal' => $transmittal['ltid']
            ];
            $status = $this->db->insert('tbl_sales', $sales);
            if ($status) {
              $rows++;
            }
          }
        }
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

    $this->login->saveLog('Run Cron: Create RMS data based on data from LTO Transmittal System [rms_create] Count: '.$rows.', Duration: '.$start.' - '.$end);

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

  public function diy() {
    $start = date("Y-m-d H:i:s");
    $this->dev_rms->truncate('diy_tbl');
    $diy = $this->db
         ->select('c.cust_code AS cust_id, e.engine_no, s.created_date AS trans_date')
         ->from('tbl_sales s')
         ->join('tbl_customer c', 'c.cid = s.customer', 'inner')
         ->join('tbl_engine e', 'e.eid = s.engine', 'inner')
         ->where('s.registration_type <> "Self Registration" AND s.created_date = DATE_FORMAT(NOW(), "%Y-%m-%d")')
         ->get()
         ->result_array();

    $this->dev_rms->insert_batch('diy_tbl', $diy);
    $end = date("Y-m-d H:i:s");
    $this->login->saveLog('Run Cron: Inserted DIY: '.$start.' - '.$end);
  }

}
