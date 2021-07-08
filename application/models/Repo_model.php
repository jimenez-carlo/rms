<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    // Check the value of $this->region and $this->company in application/core/MY_Controller
    if ($_SESSION['company'] == 8) {
      $this->region     = $this->mdi_region;
      $this->company    = $this->mdi;
      $this->companyQry = ' AND s.company_id = 8';
    } else {
      $this->companyQry = ' AND s.company_id != 8';
    }
  }

  // TODO Get the seconds and convert to month and days or year(optional)
  public function inventory() {
    $this->table->set_template(["table_open" => "<table class='table'>"]);
    $select = [
      'c.cust_code AS "Cust Code"',
      'TRIM(CONCAT(IF(c.last_name IS NULL, " " , CONCAT(c.last_name,",")), " ", IFNULL(c.first_name, " "), " ", IFNULL(c.middle_name, " "))) AS Customer',
      'e.engine_no AS "Engine #"',
      'e.mvf_no AS "MV File"',

      'IFNULL(s2.status_name, "") AS "Repo Type"',
      'IFNULL(s.status_name, "NEW") AS Status',
      'IFNULL(DATE_FORMAT(rs.date_sold, "%Y-%m-%d"),"") AS "Date Sold"',
      'IFNULL(DATE_FORMAT(rr.date_registered, "%Y-%m-%d"),"") AS "Date Registered"',
      //'IFNULL(
      //  CONCAT(
      //    "<span class=\'text-success\'>",
      //    PERIOD_DIFF(DATE_FORMAT(DATE_ADD(rr.date_registered, INTERVAL 11 MONTH), "%Y%m"), DATE_FORMAT(NOW(), "%Y%m"))," month(s)",
      //    "</span>"
      //  ), "<span class=\'text-error\'>Not yet registered.</span>"
      //) AS Expiration',
      'CONCAT(
        " <a class=\'btn btn-primary\'", " href=\''.base_url('repo/view/').'",rs.repo_sales_id,"\'"," target=\'_blank\'>View</a> ",
        " <a class=\'btn btn-warning\'",IF(rs.status_id = \'1\', CONCAT("href=\''.base_url('repo/sale/').'", rs.repo_sales_id,"\'"), "disabled")," target=\'_blank\'>Sales</a> ",
        " <a class=\'btn btn-success\'",IF(rs.status_id = \'2\' AND rb.debit_memo IS NOT NULL, CONCAT("href=\''.base_url('repo/registration/').'", rs.repo_sales_id,"/",rs.repo_sales_id,"\'"), "disabled")," target=\'_blank\'>Register</a> "
      ) AS ""',
    ];

    $result =  $this->db
    ->distinct()
    ->select($select, false)
    // ->from('tbl_repo_inventory ri')
    ->from('tbl_repo_sales rs')
    ->join('tbl_engine e', 'e.eid = rs.engine_id', 'inner')
    // ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_repo_batch rb', 'rb.repo_batch_id = rs.repo_batch_id', 'left')
    ->join('tbl_repo_registration rr', 'rr.repo_registration_id = rs.repo_registration_id', 'left')
    ->join('tbl_customer c', 'c.cid = rs.customer_id', 'left')
    ->join('tbl_status s', 'rs.status_id = s.status_id AND s.status_type = "REPO_SALES"', 'left')
    ->join('tbl_status s2', 'rs.repo_reg_type = s2.status_id AND s2.status_type = "REPO_REG_STATUS"', 'left')
    ->where([ 'rs.bcode' => $_SESSION['branch_code'] ])
    ->get();
    return $this->table->generate($result);
  }

  public function get_repo_in($select, $where) {
    $result = $this->db->select($select)
      ->from('tbl_engine e')
      ->join('tbl_sales s', 'e.eid = s.engine', 'left')
      ->join('tbl_customer sc', 'sc.cid = s.customer', 'left')
      ->join('tbl_repo_inventory ri', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_repo_registration rr', 'rr.repo_registration_id = rs.repo_registration_id', 'left')
      ->join('tbl_customer rsc', 'rsc.cid = rs.customer_id', 'left')
      ->where($where)
      ->order_by('rr.repo_registration_id DESC')
      ->limit(1);
    return $result->get()->row_array();
  }

  public function engine_details($repo_sales_id, $status) {
    switch ($status) {
      case '2':
      case '1':
        $status = "AND rs.status_id = '{$status}'";
        break;
      default:
        $status = '';
    }

    $engine_details = $this->db->select("
      rs.*, e.*, rr.repo_registration_id, rb.repo_batch_id,
      DATE_FORMAT(
        IFNULL(rr.date_registered, s.cr_date),
        '%Y-%m-%d'
      ) AS date_registered,
      rr.registration_type, IFNULL(rr.orcr_amt,0) AS orcr_amt,
      IFNULL(rr.renewal_amt,0) AS renewal_amt, IFNULL(rr.transfer_amt,0) AS transfer_amt,
      IFNULL(rr.hpg_pnp_clearance_amt,0) AS hpg_pnp_clearance_amt, IFNULL(rr.emission_amt,0) AS emission_amt,
      IFNULL(rr.insurance_amt,0) AS insurance_amt, IFNULL(rr.macro_etching_amt,0) AS macro_etching_amt,
      IFNULL(rr.renewal_tip,0) AS renewal_tip, IFNULL(rr.transfer_tip,0) AS transfer_tip,
      IFNULL(rr.hpg_pnp_clearance_tip,0) AS hpg_pnp_clearance_tip,
      IFNULL(rr.macro_etching_tip,0) AS macro_etching_tip, IFNULL(rr.plate_tip,0) AS plate_tip,
      rr.attachment, c.*,
      TRIM(
        CONCAT(
          IFNULL(c.first_name, ''), ' ',
          IFNULL(c.middle_name, ''), ' ',
          IFNULL(c.last_name, ''), ' ',
          IFNULL(c.suffix, '')
        )
      ) AS customer_name,
      rs.repo_sales_id, rs.customer_id,
      rs.rsf_num, rs.ar_num,
      rs.ar_amt, rs.date_sold,
      rb.reference,
      UPPER(s2.status_name) as repo_reg_type
    ")
    // ->from('tbl_repo_inventory ri')
    ->from('tbl_repo_sales rs')
    ->join('tbl_engine e', 'e.eid = rs.engine_id', 'left')
    // ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_repo_registration rr', 'rr.repo_registration_id= rs.repo_registration_id', 'left')
    ->join('tbl_repo_batch rb', 'rb.repo_batch_id = rs.repo_batch_id', 'left')
    ->join('tbl_customer c', 'c.cid = rs.customer_id', 'left')
    ->join('tbl_sales s', 's.engine = e.eid', 'left')
    ->join('tbl_status s2', 'rs.repo_reg_type = s2.status_id AND s2.status_type = "REPO_REG_STATUS"', 'left')
    ->where('rs.repo_sales_id = '.$repo_sales_id.' '.$status)->get()->row_array();
    return $engine_details;
  }

  public function claim($engine_id) {

    $get_repo = <<<SQL
      SELECT *
      FROM  tbl_repo_sales rs 
      LEFT JOIN tbl_repo_registration rr ON rr.repo_registration_id = rs.repo_registration_id
      LEFT JOIN tbl_repo_batch rb ON rb.repo_batch_id = rs.repo_batch_id
      WHERE rs.engine_id = {$engine_id} AND  rs.status_id != 3 AND rb.repo_batch_id IS NULL
SQL;
    $repo = $this->db->query($get_repo)->row_array();

    if (count($repo) > 0) {
      $_SESSION['warning'][] = 'Error!';
    } else {

      $data['engine_id']  = $engine_id;
      $data['bcode']      = $_SESSION['branch_code'];
      $data['bname']      = $_SESSION['branch_name'];
      $data['status_id']  = '1';
      $data['company_id'] = $_SESSION['company'];
      $this->db->trans_start();

      // CREATE INVENTORY
      // $this->db->insert('tbl_repo_inventory', $data);
      // $data['repo_inventory_id'] = $this->db->insert_id();

      $this->db->insert('tbl_repo_sales', $data);
      $data['repo_sales_id'] = $this->db->insert_id();
      $select = "
        s.sid, s.bcode, s.bname, r.region,
        DATE_FORMAT(s.date_sold, '%Y-%m-%d') AS date_sold,
        DATE_FORMAT(s.cr_date, '%Y-%m-%d') AS date_registered,
        c.cid, c.cust_code, c.first_name, c.middle_name,
        c.last_name, e.* ";
      $this->insert_history(
        $data['repo_sales_id'],
        'BNEW',
        $this->db->select($select)
        ->join('tbl_engine e','e.eid=s.engine','left')
        ->join('tbl_customer c','s.customer=c.cid','left')
        ->join('tbl_region r', 's.region=r.rid', 'left')
        ->order_by('s.sid', 'desc')
        ->get_where('tbl_sales s','e.eid='.$engine_id)
        ->row_array()
      );

      $this->insert_history(
        $data['repo_sales_id'],
        'REPO_IN',
        $this->db->get_where('tbl_repo_sales ri','ri.repo_sales_id = '.$data['repo_sales_id'])->row_array()
      );

      $this->db->trans_complete();
      if ($this->db->trans_status()) {
        $_SESSION['messages'][] = 'Success!';
      }
    }
  }

  public function expiration($date_registered) {
    if (empty($date_registered)) {
      return ['status' => 'error', 'message' => 'Please register.'];
    }

    $now = new DateTime('now');
    $date_expired = new DateTime($date_registered);
    $date_expired->modify("+1 year");

    $day   = 'day';
    $month = 'month';
    $year  = 'year';
    $expired = '';
    $expire = ['message' => ''];
    if ($date_expired > $now) {
      $interval = $now->diff($date_expired);
      $day   .= ($interval->d > 1) ? 's' : '';
      $month .= ($interval->m > 1) ? 's' : '';
      $year  .= ($interval->y > 1) ? 's' : '';
      if (($interval->y == 0 && in_array($interval->m, [0,1]) && $interval->d < 31)) {
        $expire['status'] = 'warning';
        $expire['message'] = 'Expire on';
      } else {
        $expire['status'] = 'success';
      }
    } else {
      $expire['status'] = 'error';
      $interval = $date_expired->diff($now);
      $day   .= ($interval->d > 1) ? 's' : '';
      $month .= ($interval->m > 1) ? 's' : '';
      $year  .= ($interval->y > 1) ? 's' : '';
      $expired = 'expired';
    }
    $format = '';
    $format .= ($interval->y !== 0) ? " %y {$year}" : '';
    $format .= ($interval->y !== 0 && $interval->m !== 0 && $interval->d === 0) ? " and " :'';
    $format .= ($interval->m !== 0) ? " %m {$month}" : '';
    $format .= (($interval->y !== 0 || $interval->m !== 0) && $interval->d !== 0) ? " and " :'';
    $format .= ($interval->d !== 0) ? " %d {$day}" : '';
    $message = $interval->format($format." ".$expired);
    $expire['message'] .= ($message !== " ") ? $message : 'Will expire tomorrow' ;

    return $expire;
  }

  public function initialize_regn($registration) { 
    $this->db->insert('tbl_repo_registration', $registration);
  }

  public function update_inv_status($repo_inventory_id, $status, $status_id = 0) {
    $this->db->update('tbl_repo_inventory', ['status' => $status, 'status_id' => $status_id], ['repo_inventory_id' => $repo_inventory_id]);
  }

  public function save_registration($repo_inventory_id, $repo_sales_id, $registration) { 
    $this->db->trans_begin();

    if ($this->db->insert('tbl_repo_registration', $registration)) {
      $repo_registration_id = $this->db->insert_id();
      // Attachment
      $attachment = [];
      // foreach ($_FILES['attachments'] as $key => $files) {
      //   $dir = '/rms_dir/repo/registration/'.$repo_registration_id.'/';
      //   if ($key === 'name') {
      //     foreach ($files as $key => $file) {
      //       $attachment[$key] = $dir.$key.'_'.$file[0];
      //     }
      //   }
      // }
      $attachments = json_encode($attachment);
      // $upload_status = $this->file->upload('attachments', '/repo/registration/'.$repo_registration_id);
      $this->db->update('tbl_repo_registration', ['attachment'=>$attachments], 'repo_registration_id='.$repo_registration_id);

      // $this->db->query("
      //   UPDATE tbl_repo_inventory ri, tbl_repo_sales rs
      //   SET ri.status='REGISTERED', rs.repo_registration_id = {$repo_registration_id},
      //   ri.status_id = '3'
      //   WHERE ri.repo_inventory_id = rs.repo_inventory_id AND rs.repo_sales_id = {$repo_sales_id}
      // ");
      $this->db->query("
        UPDATE tbl_repo_sales rs
        SET  rs.repo_registration_id = {$repo_registration_id}
        rs.status_id = 3
        WHERE  rs.repo_sales_id = {$repo_sales_id}
      ");

      $this->insert_history(
        $repo_inventory_id,
        'REGISTERED',
        $this->db
          ->select()
          ->join('tbl_repo_sales rs', 'rs.repo_registration_id = rr.repo_registration_id', 'left')
          ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rs.repo_inventory_id', 'left')
          ->get_where(
            'tbl_repo_registration rr',
            [
              'rr.repo_registration_id' => $repo_registration_id
            ]
          )->row_array()
      );
    }

    if ($this->db->trans_status()) {
      $this->db->trans_commit();
      return $registration['repo_registration_id'];
    } else {
      $this->db->trans_rollback();
      echo 'Error';
    }
  }

  public function save_sales($sales) {
    // CUSTOMER
    $customer = $this->db->get_where('tbl_customer', [ 'cust_code' => $sales['customer']['cust_code'] ], 1)->row_array();
    if (empty($customer)) {
      $this->db->insert('tbl_customer', $sales['customer']);
      $cid = $this->db->insert_id();
    } else {
      $cid = $customer['cid'];
    }

    $sales['repo_sale']['bcode'] = $_SESSION['branch_code'];
    $sales['repo_sale']['bname'] = $_SESSION['branch_name'];
    $sales['repo_sale']['company_id'] = $_SESSION['company'];
    $sales['repo_sale']['region_id'] = $_SESSION['rrt_region_id'];
    $sales['repo_sale']['customer_id'] = $cid;
    $sales['repo_sale']['status_id'] = 2;


    // REPO SALES

    $this->db->where('repo_sales_id',  $sales['repo_sale']['repo_sales_id']);
    $this->db->update('tbl_repo_sales', $sales['repo_sale']);
    $this->insert_history(
        $sales['repo_sale']['repo_sales_id'],
        'REPO_SALES',
        $this
          ->db
          ->select("
            c.*, rs.repo_sales_id,
            rs.rsf_num, rs.ar_num, rs.ar_amt,
            rs.date_sold, DATE_FORMAT(rs.date_created, '%Y-%m-%d') AS date_created
          ")
          ->join('tbl_customer c', 'c.cid = rs.customer_id', 'join')
          ->get_where(
            'tbl_repo_sales rs',
            [ 'rs.repo_sales_id' =>  $sales['repo_sale']['repo_sales_id'] ]
          )
          ->row_array()
      );
    //OLD
    // if ($this->db->insert('tbl_repo_sales', $sales['repo_sale'])) {
    //   $this->insert_history(
    //     $sales['repo_sale']['sales_id'],
    //     'REPO_SALES',
    //     $this
    //       ->db
    //       ->select("
    //         c.*, rs.repo_sales_id,
    //         rs.rsf_num, rs.ar_num, rs.ar_amt,
    //         rs.date_sold, DATE_FORMAT(rs.date_created, '%Y-%m-%d') AS date_created
    //       ")
    //       ->join('tbl_customer c', 'c.cid = rs.customer_id', 'join')
    //       ->get_where(
    //         'tbl_repo_sales rs',
    //         [ 'rs.repo_sales_id' => $this->db->insert_id() ]
    //       )
    //       ->row_array()
    //   );
    // }
  }

  public function generate_batch() {
    $repo_batch_ref = 'REPO-'.$_SESSION['branch_code'].'-'.date('Ymd');
    $repo_batch = $this->db->get_where(
      'tbl_repo_batch rb',
      [
        'rb.reference' => $repo_batch_ref,
        'rb.bcode' => $_SESSION['branch_code']
      ]
    )->row_array();

    if (!isset($repo_batch['repo_batch_id'])) {
      $this->db->insert(
        'tbl_repo_batch',
        [
          'reference' => $repo_batch_ref,
          'bcode' => $_SESSION['branch_code'],
          'bname' => $_SESSION['branch_name'],
          'type' => 'CASH ADVANCE',
          'status' => 'FOR CA',
          'region_id' => $_SESSION['rrt_region_id'],
          'company_id' => $_SESSION['company'],
        ]
      );
      $repo_batch_id = $this->db->insert_id();

    } else {
      $repo_batch_id = $repo_batch['repo_batch_id'];
    }

    return $repo_batch_id;
  }

  public function batch_list() {
    $this->table->set_template(["table_open" => "<table class='table'>"]);

    $result = $this->db
      ->select("
        DATE_FORMAT(rb.date_created, '%Y-%m-%d %r') AS 'Date Requested',
        CONCAT(rb.bcode,' ', rb.bname) AS Branch,
        rb.reference AS 'Reference#', FORMAT(rb.amount, 2) AS 'Amount',
        rb.doc_no AS 'Document#', rb.debit_memo AS 'Debit Memo', rb.status AS 'Status',
        CONCAT('
          <a class=\"btn btn-primary\" href=\"".base_url('repo/batch_view/')."',rb.repo_batch_id,'\" target=\"_blank\">View</a>
          <a class=\"btn btn-success\" href=\"".base_url('repo/batch_print/')."',rb.repo_batch_id,'\" target=\"_blank\">Print</a>
          <button class=\"btn btn-warning btn-add-return-fund \" value=\"' ,rb.repo_batch_id,'\" data-title=\"Return Fund - ',rb.reference,'\">Return Fund</button>
        ') AS ''
      ")
      ->from('tbl_repo_batch rb')
      ->where('rb.bcode = '.$_SESSION["branch_code"])
      ->order_by('rb.repo_batch_id', 'DESC')
      ->get();
    return $this->table->generate($result);
  }

  public function batch($repo_batch_id) {
    $branch = (!$_SESSION['dept_name'] === 'Accounting') ? ' AND rs.bcode = '.$_SESSION['branch_code'] : '';
    $data = $this->db->select('reference, misc_expenses')->get_where('tbl_repo_batch', 'repo_batch_id='.$repo_batch_id)->row_array();
    $this->table->set_template(["table_open" => "<table class='table'>"]);
    $result = $this->db
      ->select('
        TRIM(CONCAT(IF(c.last_name IS NULL, " " , CONCAT(c.last_name,",")), " ", IFNULL(c.first_name, " "), " ", IFNULL(c.middle_name, " "))) AS "Customer Name",
        c.cust_code AS "Customer Code",
        e.engine_no AS "Engine #",
        s.status_name AS `Registration Type`,
        rs.ar_num AS "AR#",
        FORMAT(rs.ar_amt,2) AS "AR Amt.",
        FORMAT(IFNULL(rr.orcr_amt, 0),2) AS "OR/CR Amt.",
        FORMAT(IFNULL(rr.renewal_amt, 0),2) AS "Renewal Amt.",
        FORMAT(IFNULL(rr.transfer_amt, 0),2) AS "Transfer Amt.",
        FORMAT(IFNULL(rr.hpg_pnp_clearance_amt, 0),2) AS "HPG/PNP Clearance Amt.",
        FORMAT(IFNULL(rr.insurance_amt, 0),2) AS "Insurance Amt.",
        FORMAT(IFNULL(rr.emission_amt, 0),2) AS "Emission Amt.",
        FORMAT(IFNULL(rr.macro_etching_amt, 0),2) AS "Macro Etching Amt.",
        FORMAT(IFNULL(rr.renewal_tip, 0),2) AS "Renewal Tip",
        FORMAT(IFNULL(rr.transfer_tip, 0),2) AS "Transfer Tip",
        FORMAT(IFNULL(rr.hpg_pnp_clearance_tip, 0),2) AS "HPG/PNP Clearance Tip",
        FORMAT(IFNULL(rr.macro_etching_tip, 0),2) AS "Macro Etching Tip",
        FORMAT(IFNULL(rr.plate_tip, 0),2) AS "Plate Tip"
      ')
      ->from('tbl_repo_batch rb')
      ->join('tbl_repo_sales rs', 'rs.repo_batch_id = rb.repo_batch_id', 'left')
      ->join('tbl_status s', 'rs.repo_reg_type = s.status_id AND status_type = "REPO_REG_STATUS"', 'left')
      ->join('tbl_repo_inventory ri', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_repo_registration rr', 'rs.repo_registration_id = rr.repo_registration_id', 'left')
      ->join('tbl_engine e', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_customer c', 'rs.customer_id = c.cid', 'left')
      
      ->where('rb.repo_batch_id = '.$repo_batch_id.' '.$branch)
      ->get();
    $data["batch"] = $this->table->generate($result);
    return $data;
  }

  public function save_expense($data) {
    $data_json = json_encode($data['data']);
    $success = $this->db->query("
      UPDATE tbl_repo_batch
      SET misc_expenses = IF(misc_expenses IS NULL, '{$data_json}', JSON_MERGE_PATCH(misc_expenses, '{$data_json}'))
      WHERE repo_batch_id = {$data['repo_batch_id']}
    ");
    
    return $success;
  }

  public function misc_insert() {
    $post = $this->input->post();
    $this->db->trans_start();
    $expense_id = md5($_SESSION['branch_code'].date('Y-m-d H:m:s'));
    $misc = array(
    'region'     =>  $_SESSION['region_id'],
    'or_no'      =>  $post['or_no'],
    'or_date'    =>  $post['or_date'],
    'amount'     =>  $post['amount'],
    'type'       =>  $post['expense_type'],
    'ca_ref'     =>  $post['repo_batch_id'],
    'status_id'  =>  2,
    'image_path' => '/rms_dir/repo/batch/misc_exp/'.$post['repo_batch_id'].'/'.$expense_id.'.jpg');
    $this->db->insert('tbl_repo_misc', $misc);
    $id = $this->db->insert_id();
    $history = array(
    'mid'    => $id,
    'status' => 2,
    'uid'    => $_SESSION['uid']);
    $this->db->insert('tbl_repo_misc_expense_history', $history);
    $this->db->trans_complete();
  }

  public function print_batch($repo_batch_id) {
    $batch = $this->db
      ->select("
        repo_batch_id, reference, bcode, bname, misc_expenses,
        DATE_FORMAT(date_created,'%Y-%m-%d') AS date_created
      ")
      ->where('repo_batch_id = '.$repo_batch_id)
      ->get('tbl_repo_batch')
      ->row_array();

    $batch_engines = $this->db
      ->select("
        ri.repo_inventory_id, e.*, rs.repo_sales_id, rs.rsf_num,
        rs.ar_num, rs.ar_amt, rs.date_sold, rr.repo_registration_id,
        rr.date_registered, rr.registration_type, rr.orcr_amt,
        rr.hpg_pnp_clearance_amt, rr.emission_amt, rr.insurance_amt,
        rr.macro_etching_amt, c.*
      ")
      ->from('tbl_repo_inventory ri')
      ->join('tbl_engine e', 'e.eid = ri.engine_id','left')
      ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_customer c', 'c.cid = rs.customer_id','left')
      ->join('tbl_repo_registration rr', 'rr.repo_registration_id = rs.repo_registration_id','left')
      ->where('rs.repo_batch_id = '.$repo_batch_id.' OR rs.repo_batch_id ='.$repo_batch_id)
      ->get()
      ->result_array();

    return [ 'batch' => $batch, 'batch_engines' => $batch_engines ];
  }

  public function request_ca() {
    $this->table->set_template(["table_open" => "<table class='table table-condensed'>"]);

    $result = $this->db
      ->select("
        CONCAT('<input type=\"checkbox\" name=\"repo_sales_id[]\" value=\"',rs.repo_sales_id,'\" required>') AS '',
        CONCAT(rs.bcode, ' ', rs.bname) AS 'Branch',
        rs.date_sold AS 'Date Sold',
        rs.rsf_num AS 'RSF#',
        rs.ar_num AS 'AR#',
        rs.ar_amt AS 'AR Amt.',
        TRIM(
          CONCAT(IF(c.last_name IS NULL, ' ' , CONCAT(c.last_name,',')), ' ',
          IFNULL(c.first_name, ' '), ' ',
          IFNULL(c.middle_name, ' '))
        ) AS 'Customer Name',
        e.mvf_no AS 'MVF No.',
        e.engine_no AS 'Engine No',
        IFNULL(p.plate_number, '') AS 'Plate Number'
      ")
      // ->from('tbl_repo_inventory ri')
      // ->join('tbl_repo_sales rs', 'ri.repo_inventory_id = rs.repo_inventory_id', 'inner')
      ->from('tbl_repo_sales rs')
      ->join('tbl_engine e', 'e.eid = rs.engine_id', 'inner')
      ->join('tbl_plate p', 'p.plate_id = e.plate_id', 'left')
      ->join('tbl_customer c', 'c.cid = rs.customer_id', 'inner')
      ->where('rs.bcode = '.$_SESSION["branch_code"].' AND rs.repo_batch_id IS NULL')
      ->order_by('rs.date_sold', 'DESC')
      ->get();
      // $this->db->last_query();
    return $this->table->generate($result);
  }

  public function get_for_ca() {
    $this->table->set_template(["table_open" => "<table class='table table-condensed'>"]);
    $sql = "
      SELECT
        r.region AS 'Region',
        c.company_code AS 'Company',
        CONCAT(IFNULL(rb.bcode,''), ' ', IFNULL(rb.bname,'')) AS 'Branch',
        rb.date_created AS 'Date Requested',
        rb.reference AS 'Reference',
        IFNULL(rb.doc_no, CONCAT('<div class=\'control-group\'><input id=\'',rb.repo_batch_id,'\' class=\'doc-no\' type=\'text\'></div>')) AS 'Document #',
        FORMAT(rb.amount, 2) AS 'Amount',
        CONCAT('<button id=\'save-',rb.repo_batch_id,'\' type=\'button\' class=\'btn btn-success\' name=\'save\' value=\'',rb.repo_batch_id,'\' disabled>Save Doc#</button>') AS ''
      FROM tbl_repo_batch rb
      LEFT JOIN tbl_region r ON r.rid = rb.region_id
      LEFT JOIN tbl_company c ON c.cid = rb.company_id
      WHERE rb.doc_no IS NULL AND DATE(rb.date_created) >= (now()- INTERVAL 1 DAY)
    ";
    $result = $this->db->query($sql);
    // var_dump( $this->db->last_query());die();
    return $this->table->generate($result);
  }

  public function print_ca(array $data) {
    return [
      'company_region' => $this->db
         ->select('CONCAT(rb.bcode, " ", rb.bname," ", r.region) AS company_region' , true)
         ->join('tbl_region r', 'r.rid = rb.region_id', 'inner')
         ->get_where('tbl_repo_batch rb', 'rb.region_id='.$data['region'])
         ->row_array()['company_region'],
      'prints' => $this->db
        ->select('rb.reference, rb.doc_no, rb.bcode, rb.bname, COUNT(*) AS no_of_unit, COUNT(*) * 3600 AS amount')
        ->from('tbl_repo_batch rb')
        ->join('tbl_repo_sales rs', 'rs.repo_batch_id = rb.repo_batch_id','left')
        ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rs.repo_inventory_id','left')
        ->where('rb.region_id', $data['region'])
        ->where("rb.date_doc_no_encoded BETWEEN '{$data['date_doc_no_encoded']} 00:00:00' AND '{$data['date_doc_no_encoded']} 23:59:59'")
        ->group_by('rb.repo_batch_id')
        ->get()->result_array(),
    ];
  }

  public function generate_ca(array $repo_sales_id) {
    $this->db->trans_start();
    $count = count($repo_sales_id);
    $sql = <<<SQL
    INSERT tbl_repo_batch(reference, amount, bcode, bname, type, status, date_created, region_id, company_id)
    SELECT
      CONCAT(
        "REPOCA-{$_SESSION['branch_code']}-",
        DATE_FORMAT(NOW(), "%y%m%d"),
        IF(COUNT(*)>0, CONCAT("-", CAST(COUNT(*) AS CHAR)),"")
      ) AS reference,
      {$count} * 3600 AS amount,
      {$_SESSION['branch_code']},
      "{$_SESSION['branch_name']}",
      "CASH ADVANCE", "NEW", NOW(),
      {$_SESSION['rrt_region_id']},
      {$_SESSION['company']}
    FROM tbl_repo_batch rb
    WHERE rb.reference LIKE CONCAT("%REPOCA-{$_SESSION['branch_code']}-",DATE_FORMAT(NOW(), "%y%m%d"),"%")
SQL;
    $this->db->query($sql);
    $ca_batch_id = $this->db->insert_id();
    $repo_ca_reference = $this->db->query("SELECT reference FROM tbl_repo_batch WHERE repo_batch_id = {$ca_batch_id}")->row_array()['reference'];

    $this->db->where_in('rs.repo_sales_id', $repo_sales_id);
    $this->db->update('tbl_repo_sales rs', ['rs.repo_batch_id' => $ca_batch_id]);
    $this->db->trans_complete();
    return [ 'status' => $this->db->trans_status(), 'repo_ca_reference' => $repo_ca_reference ];
  }

  public function repo_ca_template() {
    return [
      "filename" => "REPOCA",
      "date" => date('Y-m-d'),
      "data" => $this->db
        ->select("
          DATE_FORMAT(rb.date_created, '%Y-%m-%d') AS 'document_type',
          DATE_FORMAT(rb.date_created, '%Y-%m-%d') AS 'posting_date',
          'KR' AS 'kr', CONCAT(rb.company_id,'000') AS 'company_code',
          'PHP' AS 'php', rb.reference, CONCAT('10', rb.bcode) AS vendor,
          FORMAT(rb.amount, 0) AS amount, CONCAT(rb.company_id, '000000') AS profit_center,
          CONCAT(
            DATE_FORMAT(rb.date_created, '%d/%m/%Y'),
            ' REPO CA - ', COUNT(*), 'UNIT -',
            TRIM(BOTH 'MS' FROM
              TRIM(BOTH 'MTI' FROM
                TRIM(BOTH 'HPTI' FROM
                  TRIM(BOTH 'MNC' FROM
                    TRIM(BOTH 'MDI' FROM rb.bname)
                  )
                )
              )
            )
          ) AS description
        ")
        ->join("tbl_repo_sales rs", "rb.repo_batch_id = rs.repo_batch_id", "left")
        ->group_by("rb.reference")
        ->get_where("tbl_repo_batch rb", "rb.doc_no IS NULL")
        ->result_array()
    ];
  }

  private function insert_history($repo_inventory_id, $action, array $logs) {
    $data = [
      'repo_sales_id'     => $repo_inventory_id,
      'repo_inventory_id' => $repo_inventory_id,
      'user' => $_SESSION['username'],
      'action' => $action,
      'data' => json_encode($logs)
    ];
    $this->db->insert('tbl_repo_history', $data);
  }

  public function get_history($repo_inventory_id) {
    return $this
      ->db
      ->select('
        repo_history_id, repo_inventory_id,
        user, action, data,
        DATE_FORMAT(date_created, "%Y-%m-%d") AS date_created
      ')
      ->get_where('tbl_repo_history', ['repo_inventory_id' => $repo_inventory_id])
      ->result_array();
  }

  public function get_branch_tip_matrix($branch_code) {
    return $this->db
      ->select([
        'IFNULL(rbb.sop_renewal,0) AS sop_renewal', 'IFNULL(rbb.sop_transfer,0) AS sop_transfer',
        'IFNULL(sop_hpg_pnp_clearance,0) AS sop_hpg_pnp_clearance', 'IFNULL(rbb.insurance,0) AS insurance',
        'IFNULL(rbb.emission,0) AS emission', 'IFNULL(rbb.unreceipted_renewal_tip,0) AS unreceipted_renewal_tip',
        'IFNULL(rbb.unreceipted_transfer_tip,0) AS unreceipted_transfer_tip',
        'IFNULL(rbb.unreceipted_macro_etching_tip,0) AS unreceipted_macro_etching_tip',
        'IFNULL(rbb.unreceipted_hpg_pnp_clearance_tip,0) AS unreceipted_hpg_pnp_clearance_tip',
        'IFNULL(rbb.unreceipted_plate_tip,0) AS unreceipted_plate_tip',
      ])
      ->get_where('tbl_repo_branch_budget rbb', 'rbb.bcode = '. $branch_code)->row_array();
  }
  public function repo_dropdown(){
    return $this->db->query("SELECT status_id,UPPER(status_name)as status_name from tbl_status where status_type='REPO_REG_STATUS'")->result_array();
  }
  public function check_registration(string $type, array $data = []) {
    switch ($type) {
      case 'GET_REFERENCE':
        return $this->db->query(" SELECT
              repo_batch_id, reference
            FROM (
              SELECT
              s.region_id,v.repo_batch_id, v.reference
              FROM
                tbl_repo_batch v
              INNER JOIN
                tbl_repo_sales s ON v.repo_batch_id = s.repo_batch_id 
              LEFT JOIN
                tbl_repo_sap_upload_sales_batch susb ON susb.repo_sales_id = s.repo_sales_id
              WHERE
                s.status_id = 3 $this->companyQry AND s.da_id IN (0, 3) AND v.repo_batch_id IS NOT NULL AND susb.repo_sales_id IS NULL
              GROUP BY
                v.repo_batch_id, s.region_id
              ORDER BY
                v.reference DESC 
             
            ) AS result
            ORDER BY region_id, reference")->result_array();
            //AND v.repo_batch_id = s.repo_batch_id
        // return $this->db
        //   ->select('rb.repo_batch_id, rb.reference')
        //   ->get_where('tbl_repo_batch rb', 'rb.status = "DEPOSITED"')
        //   ->result_array();
        break;

      case 'CA_REF_DATA':
        $sql = <<<SQL
          SELECT `rb`.*,
            CONCAT('[',
              GROUP_CONCAT('{
                "repo_registration_id": "',`rr`.`repo_registration_id`,'",
                "branch":"',CONCAT(ri.bcode, " ", ri.bname),'",
                "date_sold":"',`rs`.`date_sold`,'", "engine_no":"',`e`.`engine_no`,'",
                "ar_num":"',`rs`.`ar_num`,'", "ar_amt":"',rs.ar_amt,'", "status":"',`ri`.`status`,'"
              }'),
            ']') AS repo_registration

            --  AS branch, , , `rs`.`ar_num`,`rs`.`ar_amt`, `ri`.`status`,
          FROM `tbl_repo_batch` `rb`
          INNER JOIN `tbl_repo_sales` `rs` ON `rs`.`repo_batch_id` = `rb`.`repo_batch_id`
          LEFT JOIN `tbl_repo_registration` `rr` ON `rs`.`repo_registration_id` = `rr`.`repo_registration_id`
          INNER JOIN `tbl_repo_inventory` `ri` ON `ri`.`repo_inventory_id` = `rs`.`repo_inventory_id`
          INNER JOIN `tbl_engine` `e` ON `e`.`eid` = `ri`.`engine_id`
          WHERE `rb`.`repo_batch_id` = {$data['repo_batch_id']} 
          GROUP BY `rb`.repo_batch_id
SQL;
$this->db->query("SELECT z.engine_no from 
tbl_repo_sales x inner join 
tbl_repo_inventory y on x.repo_inventory_id = y.repo_inventory_id inner join 
tbl_engine z on y.engine_id = z.eid inner join 
tbl_status a on y.status_id = a.status_id and a.status_type = 'REPO SALES' where x.repo_batch_id = '".$data['repo_batch_id']."'");
// and rb.misc_expenses = {$data['misc_expenses']}
        $repo_batch = $this->db->query($sql)->row_array();

        $this->table->set_template(["table_open" => "<table class='table'>"]);
        $this->table->set_heading('', '#', 'Branch', 'Date Sold', 'Engine #', 'AR #', 'AR Amt', 'Status', '');
        foreach (json_decode($repo_batch['repo_registration'], 1) as $i => $repo_regn) {
          $this->table->add_row(
            "<input type='checkbox' id='sales_id-{$repo_regn['repo_registration_id']}' value='{$repo_regn['repo_registration_id']}' data-amt='".$repo_regn['ar_amt']."' data-selectable='true'>", ++$i,
            $repo_regn['branch'], $repo_regn['date_sold'], $repo_regn['engine_no'], $repo_regn['ar_num'], $repo_regn['ar_amt'], $repo_regn['status'],
            "<button class='btn btn-primary view' style='float:right;' type='button' name='REPO_UNIT' value='{$repo_regn['repo_registration_id']}'>View</button>"
          );
        }
        $repo_batch_table = $this->table->generate();

        $this->table->clear();

        $misc_exp_raw = json_decode($repo_batch['misc_expenses'], 1);
        if (isset($misc_exp_raw)) {
          $this->table->set_template(["table_open" => "<table class='table'>"]);
          $this->table->set_heading('', '#', 'OR Date', 'OR No.', 'Expense Type', 'Amount', 'Status', '','');
          #Old 
          /*
          $misc_count = 0;
          foreach ($misc_exp_raw as $id => $misc) {
            if ($misc['is_deleted'] !== "1") {
              $misc_count++;
              $this->table->add_row(
                form_checkbox("misc_exp_id-".$id, $id,null, ['id'=> $id,'data-selectable' =>'true', 'data-amt'=>  $misc['amount']]), 
                $misc_count, 
                $misc['or_date'], 
                $misc['or_no'] ,
                $misc['expense_type'], 
                number_format($misc['amount'],2), 
                $misc['status'],
                ["data"=>''],
                form_button(["class"=>"btn btn-primary view", "style"=>"float:right", "name"=>"MISC_EXP", "value"=>$id, "content"=>"View"])
              );
            }
          }*/
          $batch_misc = $this->db->query("SELECT a.*,DATE(a.or_date) as or_date,b.status_name from tbl_repo_misc a inner join tbl_status b on a.status_id = b.status_id and b.status_type= 'MISC_EXP' where b.status_id NOT IN(90,1,0) AND a.ca_ref ='".$data['repo_batch_id']."'")->result_array();
          $misc_count = 0;
          foreach ($batch_misc as $res) {
            $misc_count++;
            $this->table->add_row(
                form_checkbox("misc[".$res['mid']."]", $res['mid'],null, ['id'=> 'misc_id-'.$res['mid'],'data-selectable' =>'true', 'data-amt'=>  $res['amount']]), 
                $misc_count, 
                $res['or_date'], 
                $res['or_no'],
                $res['type'], 
                number_format($res['amount'],2), 
                $res['status_name'],
                ["data"=>''],
                form_button(["class"=>"btn btn-primary view", "style"=>"float:right", "name"=>"MISC_EXP", "value"=>$res['mid'], "content"=>"View"])
              );
          }
          // $this->table->add_row("<div style='border-top: dotted gray; font-size: 16px;colspan:8'>");
          $this->table->add_row(['colspan'=>"8"                          , "class"=>'brdrt'],["data" => 'Total Amount','colspan' => "1", "class" => 'brdrt bld']);
          $this->table->add_row(["data"   =>'Batch'       ,'colspan'=>"8", "class"=>'bld']  ,["data" => "&#8369; ".number_format($repo_batch['amount'],2),'colspan'=>"1","class"=>'bld']);
          $this->table->add_row(["data"   =>'Liquidated'  ,'colspan'=>"8", "class"=>'bld']  ,["data" => "&#8369; 0.00",'colspan' => "1", "class" => 'bld']);
          $this->table->add_row(["data"   =>'Checked'     ,'colspan'=>"8", "class"=>'bld']  ,["data" => "&#8369; 0.00",'colspan' => "1", "class" => 'bld']);
          $this->table->add_row(["data"   =>'Balance'     ,'colspan'=>"8", "class"=>'bld']  ,["data" => "&#8369; ".number_format($repo_batch['amount'],2),'colspan'=>"1","class"=>'bld bal']);
          $this->table->add_row(["data"   => 'Balance for upload must not be negative.','colspan'=>"3","class"=>"bld clr-rd al"],['colspan'=>"4"],["data"=>'Expense'     ,'colspan'=>"1", "class"=>'bld'],["data" => "&#8369; 0.00",'colspan'=>"1","class"=>'bld exp_display clr-rd']);
          $this->table->add_row(["data"=>'','colspan'=>"9","class"=>'brdrb bld']);
          $repo_batch_table .= $this->table->generate();
        }

        return json_encode([
          "table" =>  $repo_batch_table
        ]);
        break;

      case 'CA_REF_DATA_PREV':
        $sql = <<<SQL
          SELECT `rb`.*,
            CONCAT('[',
              GROUP_CONCAT('{
                "repo_registration_id": "',`rr`.`repo_registration_id`,'",
                "branch":"',CONCAT(ri.bcode, " ", ri.bname),'",
                "date_sold":"',`rs`.`date_sold`,'", "engine_no":"',`e`.`engine_no`,'",
                "ar_num":"',`rs`.`ar_num`,'", "ar_amt":"',rs.ar_amt,'", "status":"',`ri`.`status`,'"
              }'),
            ']') AS repo_registration
            --  AS branch, , , `rs`.`ar_num`,`rs`.`ar_amt`, `ri`.`status`,
          FROM `tbl_repo_batch` `rb`
          INNER JOIN `tbl_repo_sales` `rs` ON `rs`.`repo_batch_id` = `rb`.`repo_batch_id`
          LEFT JOIN `tbl_repo_registration` `rr` ON `rs`.`repo_registration_id` = `rr`.`repo_registration_id`
          INNER JOIN `tbl_repo_inventory` `ri` ON `ri`.`repo_inventory_id` = `rs`.`repo_inventory_id`
          INNER JOIN `tbl_engine` `e` ON `e`.`eid` = `ri`.`engine_id`
          WHERE `rb`.`repo_batch_id` = {$data['repo_batch_id']}
          GROUP BY `rb`.repo_batch_id
SQL;
        $repo_batch = $this->db->query($sql)->row_array();

        $this->table->set_template(["table_open" => "<table class='table'>"]);
        $this->table->set_heading('', '#', 'Branch', 'Date Sold', 'Engine #', 'AR #', 'AR Amt', 'Status', '');
        foreach (json_decode($repo_batch['repo_registration'], 1) as $i => $repo_regn) {
          $this->table->add_row(
            "<input type='checkbox' id='cb-{$repo_regn['repo_registration_id']}' value='{$repo_regn['repo_registration_id']}' data-amt='".$repo_regn['ar_amt']."'>", ++$i,
            $repo_regn['branch'], $repo_regn['date_sold'], $repo_regn['engine_no'], $repo_regn['ar_num'], $repo_regn['ar_amt'], $repo_regn['status'],
            "<button class='btn btn-primary view' style='float:right;' type='button' name='REPO_UNIT' value='{$repo_regn['repo_registration_id']}'>View</button>"
          );
        }
        $repo_batch_table = $this->table->generate();

        $this->table->clear();

        $misc_exp_raw = json_decode($repo_batch['misc_expenses'], 1);
        if (isset($misc_exp_raw)) {
          $this->table->set_template(["table_open" => "<table class='table'>"]);
          $this->table->set_heading('', '#', 'OR Date', 'OR No.', 'Expense Type', 'Amount', 'Status', '','');
          $misc_count = 0;
          foreach ($misc_exp_raw as $id => $misc) {
            if ($misc['is_deleted'] !== "1") {
              $misc_count++;
              $this->table->add_row(
                form_checkbox("misc_exp_id-".$id, $id,null, ['id'=> $id, 'data-amt'=>  $misc['amount']]), $misc_count, $misc['or_date'], $misc['or_no'] ,
                $misc['expense_type'], number_format($misc['amount'],2), $misc['status'],["data"=>''],
                form_button(["class"=>"btn btn-primary view", "style"=>"float:right", "name"=>"MISC_EXP", "value"=>$id, "content"=>"View"])
              );
            }
          }
          // $this->table->add_row("<div style='border-top: dotted gray; font-size: 16px;colspan:8'>");
          $this->table->add_row(['colspan'=>"8","class"=>'brdrt'],["data"=>'Total Amount','colspan'=>"1", "class"=>'brdrt bld']);
          $this->table->add_row(["data"=>'Batch'       ,'colspan'=>"8", "class"=>'bld'],["data" => "&#8369; ".number_format($repo_batch['amount'],2),'colspan'=>"1","class"=>'bld']);
          $this->table->add_row(["data"=>'Liquidated'  ,'colspan'=>"8", "class"=>'bld'],["data" => "&#8369; 0.00",'colspan'=>"1","class"=>'bld']);
          $this->table->add_row(["data"=>'Checked'     ,'colspan'=>"8", "class"=>'bld'],["data" => "&#8369; 0.00",'colspan'=>"1","class"=>'bld']);
          $this->table->add_row(["data"=>'Balance'     ,'colspan'=>"8", "class"=>'bld'],["data" => "&#8369; ".number_format($repo_batch['amount'],2),'colspan'=>"1","class"=>'bld bal']);
          $this->table->add_row(["data" => 'Balance for upload must not be negative.','colspan'=>"3","class"=>"bld clr-rd al"],['colspan'=>"4"],["data"=>'Expense'     ,'colspan'=>"1", "class"=>'bld'],["data" => "&#8369; 0.00",'colspan'=>"1","class"=>'bld exp_display clr-rd']);
          $this->table->add_row(["data"=>'','colspan'=>"9","class"=>'brdrb bld']);
          $repo_batch_table .= $this->table->generate();
        }



        return json_encode([
          "table" =>  $repo_batch_table
        ]);
        break;

      case 'VIEW_ATTACHMENT':
        switch ($data['type']) {
          case 'MISC_EXP':
            # Old
            /*
            return $this->db->select('misc_expenses->>\'$."'.$data['misc_expense_id'].'"\' AS misc_expenses')
            ->get_where('tbl_repo_batch rb', 'repo_batch_id='.$data['repo_batch_id'])
            ->row_array()['misc_expenses'];
            */
            return json_encode($this->db->query("SELECT 
              DATE(x.or_date) as or_date,
              x.or_no,
              x.type,
              y.status_name,
              x.amount,
              x.image_path
             from tbl_repo_misc x inner join tbl_status y on x.status_id = y.status_id and y.status_type = 'MISC_EXP' where x.mid ='".$data['misc_expense_id']."'")->row());
            break;

          case 'REPO_UNIT':
            $repo_data = $this->db->select([
              'CONCAT(ri.bcode, " ", ri.bname) AS branch',
              'CONCAT(IFNULL(c.first_name,"")," ",IFNULL(c.middle_name, ""), " ", IFNULL(c.last_name, ""), " ", IFNULL(c.suffix,"")) AS customer_name',
              'e.engine_no', 'rs.rsf_num', 'rs.ar_num', 'FORMAT(rs.ar_amt,2) AS ar_amt', 'FORMAT(rr.orcr_amt, 2) AS orcr_amt',
              'FORMAT(rr.renewal_amt,2) AS renewal_amt', 'FORMAT(rr.transfer_amt,2) AS transfer_amt',
              'FORMAT(rr.hpg_pnp_clearance_amt,2) AS hpg_pnp_clearance_amt', 'FORMAT(rr.insurance_amt,2) AS insurance_amt',
              'FORMAT(rr.emission_amt,2) AS emission_amt', 'FORMAT(rr.macro_etching_amt,2) AS macro_etching_amt',
              'FORMAT(rr.renewal_tip,2) AS renewal_tip', 'FORMAT(rr.transfer_tip,2) AS transfer_tip',
              'FORMAT(rr.hpg_pnp_clearance_tip,2) AS hpg_pnp_clearance_tip', 'FORMAT(rr.macro_etching_tip,2) AS macro_etching_tip',
              'FORMAT(rr.plate_tip,2) AS plate_tip', 'rr.attachment'
            ])
            ->from('tbl_repo_sales rs')
            ->join('tbl_repo_registration rr', 'rs.repo_registration_id = rr.repo_registration_id', 'inner')
            ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rs.repo_inventory_id', 'inner')
            ->join('tbl_engine e', 'e.eid = ri.engine_id', 'inner')
            ->join('tbl_customer c', 'c.cid = rs.customer_id', 'inner')
            ->where('rr.repo_registration_id='.$data['repo_registration_id'])
            ->get()->row_array();
            $repo_data['attachment'] = json_decode($repo_data['attachment']);
            return json_encode($repo_data);
            break;

        }
        break;
    }
  }
  public function misc_da_dropdown() {
    return $this->db->query("SELECT status_id,status_name from tbl_status where status_type = 'MISC_DA_REASON' and is_active = 1")->result_array();
  }

  public function list_for_upload() {
    // $this->companyQry
    $result = $this->db->query("
      SELECT
        sub.subid,
        sub.trans_no,
        SUBSTR(sub.date_created, 1, 10) AS post_date,
        r.region,
        c.company_code AS company,
        sub.is_uploaded,
        sub.download_date
        ,GROUP_CONCAT(DISTINCT mid separator ',') AS misc_expense_id
      FROM
        tbl_repo_sap_upload_batch sub
      JOIN
        tbl_repo_sap_upload_sales_batch USING (subid)
      JOIN
        tbl_repo_sales s USING (repo_sales_id)
      INNER JOIN
        tbl_region r ON s.region_id = r.rid
      INNER JOIN
        tbl_company c ON s.company_id = c.cid
      LEFT JOIN
        tbl_repo_batch  v ON v.repo_batch_id = s.repo_batch_id
      LEFT JOIN
      (SELECT
         m.mid, m.ca_ref
      FROM
        tbl_repo_misc m
      LEFT JOIN
        tbl_repo_misc_expense_history mxh1 ON mxh1.mid = m.mid
      LEFT JOIN
        tbl_repo_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
      LEFT JOIN
        tbl_status st ON mxh1.status = st.status_id AND st.status_type = 'MISC_EXP'
      WHERE
        mxh2.id IS NULL AND st.status_name = 'For Liquidation'
      ) AS miscellaneous_expense ON ca_ref = v.repo_batch_id
      WHERE
        sub.is_uploaded = 0 
      GROUP BY subid, region, company
      ORDER BY sub.date_created DESC
      LIMIT 1000
    ")->result_object();

    return $result;
  }
    public function sap_upload($subid) {
      // s.si_no, s.ar_no,         s.registration + s.tip + s.penalty AS regn_expense,
    $sql = <<<SQL
      SELECT
        DISTINCT
        v.repo_batch_id as vid,
        DATE_FORMAT(s.date_sold, '%m/%d/%Y') AS post_date,
        CASE c.company_code
          WHEN 'MNC'  THEN '1000'
          WHEN 'MTI'  THEN '6000'
          WHEN 'HPTI' THEN '3000'
          WHEN 'MDI'  THEN '8000'
        END AS c_code,
        CASE 'Regular Regn. Paid'
          WHEN 'Free Registration'  THEN '215450'
          ELSE
            CONCAT('219',
            CASE
              WHEN c.company_code IN('MNC','MDI')   THEN SUBSTR(s.bcode, 2, 4)
              WHEN c.company_code IN('HPTI', 'MTI') THEN CONCAT(LEFT(s.bcode, 1),RIGHT(s.bcode,2))
            END)
        END AS sap_code,
        CASE reg.registration_type
          WHEN 'RENEW'  THEN SUM(
            IFNULL(reg.orcr_amt,0)+
            IFNULL(reg.renewal_amt,0)+
            IFNULL(reg.renewal_tip,0)+
            IFNULL(reg.transfer_tip,0)+
            IFNULL(reg.hpg_pnp_clearance_tip,0)+
            IFNULL(reg.macro_etching_tip,0))
          WHEN 'RENEW & TRANSFER'  THEN SUM(
          IFNULL(reg.orcr_amt,0)+
          IFNULL(reg.transfer_amt,0)+
          IFNULL(reg.renewal_tip,0)+
          IFNULL(reg.transfer_tip,0)+
          IFNULL(reg.hpg_pnp_clearance_tip,0)+
          IFNULL(reg.macro_etching_tip,0))
        END AS regn_expense,
         s.ar_num,s.ar_amt AS ar_amount,
        'Regular Regn. Paid' as registration_type, f.acct_number AS account_key,

        CONCAT(s.bcode, '000') AS branch_code,
        v.reference AS reference_number,
        cust.cust_code, CONCAT(IFNULL(cust.last_name,''), ', ', IFNULL(cust.first_name,'')) AS customer_name
      FROM
        tbl_repo_sap_upload_batch sub
      LEFT JOIN
        tbl_repo_sap_upload_sales_batch x ON sub.subid = x.subid
      LEFT JOIN
        tbl_repo_sales s ON x.repo_sales_id = s.repo_sales_id
      INNER JOIN
        tbl_customer cust ON s.customer_id = cust.cid
      LEFT JOIN 
        tbl_repo_registration reg ON s.repo_registration_id = reg.repo_registration_id
      INNER JOIN
        tbl_region r ON s.region_id = r.rid
      LEFT JOIN
        tbl_repo_fund f ON r.rid = f.region
      INNER JOIN
        tbl_company c ON s.company_id = c.cid
      LEFT JOIN
        tbl_repo_batch v ON s.repo_batch_id = v.repo_batch_id
      WHERE
      sub.subid = $subid
      ORDER BY
        v.repo_batch_id ASC
SQL;
// inner join dapat tbl_repo_registration,tbl_repo_fund 
    $batch = $this->db->query($sql)->result_array();

    $misc_exp_qry = <<<SQL
      SELECT repo_batch_id as vid,
        reference,
        FORMAT(SUM(amount),2) AS misc_expense_amount
      FROM (
        SELECT
          DISTINCT v.repo_batch_id, v.reference, m.mid, m.amount
        FROM tbl_repo_sap_upload_sales_batch susb
        LEFT JOIN tbl_repo_sales s ON s.repo_sales_id = susb.repo_sales_id
        LEFT JOIN tbl_repo_batch v ON v.repo_batch_id = s.repo_batch_id
        LEFT JOIN tbl_repo_misc m ON m.ca_ref = v.repo_batch_id
        LEFT JOIN tbl_status st ON m.status_id = st.status_id AND status_type = 'MISC_EXP'

        WHERE
          m.mid IS NOT NULL AND susb.subid = {$subid} AND st.status_id = 3
      ) AS first_result
      GROUP BY repo_batch_id
      ORDER BY repo_batch_id ASC
SQL;
// AND mxh2.id IS NULL
// LEFT JOIN tbl_repo_misc_expense_history mxh1 ON m.mid = mxh1.mid
// LEFT JOIN tbl_repo_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
    $misc_expenses = $this->db->query($misc_exp_qry)->result_array();

    $this->load->model('Login_model', 'login');
    $this->login->saveLog('donwnloaded sap template subid: '.$subid.'.');

    return array('batch' => $batch, 'misc_expenses' => $misc_expenses);
  }


  public function liquidate_batch($batch){
    $this->db->update('tbl_repo_sap_upload_batch', $batch, array('subid' => $batch->subid));
    $batch = $this->db->query("select * from tbl_repo_sap_upload_batch where subid = ".$batch->subid)->row();

    $date = date('Y-m-d');
                // update sales status
    $update_qry = <<<SQL
                  UPDATE
                    tbl_repo_sales s
                  INNER JOIN
                    tbl_repo_sap_upload_sales_batch susb  ON s.repo_sales_id = susb.repo_sales_id
                  SET
                    status_id = 4, close_date = "{$date}"
                  WHERE susb.subid = $batch->subid
SQL;
    $this->db->query($update_qry);
    $this->load->model('Login_model', 'login');
    $this->login->saveLog('saved document number ['.$batch->doc_no.'] for ['.$batch->trans_no.']');

    return $batch;
  }

  public function liquidate_misc_exp($misc_exp){
    $insert_misc_exp = array();
    $uid = $_SESSION['uid'];
    $misc_exp_ids = explode(',', $misc_exp);

    foreach ($misc_exp_ids as $misc_exp_id) {
      $insert_misc_exp[] = array(
        'mid' => $misc_exp_id,
        'status' => 4,
        'uid' => $uid
      );
    }
    $this->db->insert_batch('tbl_repo_misc_expense_history', $insert_misc_exp);
  }
}
