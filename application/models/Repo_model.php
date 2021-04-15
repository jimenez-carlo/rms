<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  // TODO Get the seconds and convert to month and days or year(optional)
  public function inventory() {
    $this->table->set_template(["table_open" => "<table class='table'>"]);
    $select = [
      'c.cust_code AS "Cust Code"',
      'TRIM(CONCAT(IF(c.last_name IS NULL, " " , CONCAT(c.last_name,",")), " ", IFNULL(c.first_name, " "), " ", IFNULL(c.middle_name, " "))) AS Customer',
      'e.engine_no AS "Engine #"',
      'e.mvf_no AS "MV File"',
      'ri.status AS Status',
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
        " <a class=\'btn btn-primary\'", " href=\''.base_url('repo/view/').'",ri.repo_inventory_id,"\'"," target=\'_blank\'>View</a> ",
        " <a class=\'btn btn-warning\'",IF(ri.status = \'NEW\', CONCAT("href=\''.base_url('repo/sale/').'", ri.repo_inventory_id,"\'"), "disabled")," target=\'_blank\'>Sales</a> ",
        " <a class=\'btn btn-success\'",IF(ri.status = \'SALES\' AND rb.debit_memo IS NOT NULL, CONCAT("href=\''.base_url('repo/registration/').'", ri.repo_inventory_id,"/",rs.repo_sales_id,"\'"), "disabled")," target=\'_blank\'>Register</a> "
      ) AS ""',
    ];

    $result =  $this->db
    ->distinct()
    ->select($select, false)
    ->from('tbl_repo_inventory ri')
    ->join('tbl_engine e', 'e.eid = ri.engine_id', 'inner')
    ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_repo_batch rb', 'rb.repo_batch_id = rs.repo_batch_id', 'left')
    ->join('tbl_repo_registration rr', 'rr.repo_registration_id = rs.repo_registration_id', 'left')
    ->join('tbl_customer c', 'c.cid = rs.customer_id', 'left')
    ->where([ 'ri.bcode' => $_SESSION['branch_code'] ])
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

  public function engine_details($repo_inventory_id, $status) {
    switch ($status) {
      case 'SALES':
      case 'NEW':
        $status = "AND ri.status = '{$status}'";
        break;
      default:
        $status = '';
    }

    $engine_details = $this->db->select("
      ri.*, e.*, rr.repo_registration_id, rb.repo_batch_id,
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
      rb.reference
    ")
    ->from('tbl_repo_inventory ri')
    ->join('tbl_engine e', 'e.eid = ri.engine_id', 'left')
    ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_repo_registration rr', 'rr.repo_registration_id= rs.repo_registration_id', 'left')
    ->join('tbl_repo_batch rb', 'rb.repo_batch_id = rs.repo_batch_id', 'left')
    ->join('tbl_customer c', 'c.cid = rs.customer_id', 'left')
    ->join('tbl_sales s', 's.engine = e.eid', 'left')
    ->where('ri.repo_inventory_id = '.$repo_inventory_id.' '.$status)->get()->row_array();
    return $engine_details;
  }

  public function claim($engine_id) {
    $get_repo = <<<SQL
      SELECT *
      FROM tbl_repo_inventory ri
      LEFT JOIN tbl_repo_sales rs ON rs.repo_inventory_id = ri.repo_inventory_id
      LEFT JOIN tbl_repo_registration rr ON rr.repo_registration_id = rs.repo_registration_id
      LEFT JOIN tbl_repo_batch rb ON rb.repo_batch_id = rs.repo_batch_id
      WHERE ri.engine_id = {$engine_id} AND ri.status != 'Registered' AND rb.repo_batch_id IS NULL
SQL;
    $repo = $this->db->query($get_repo)->row_array();

    if (count($repo) > 0) {
      $_SESSION['warning'][] = 'Error!';
    } else {
      $data['engine_id'] = $engine_id;
      $data['bcode'] = $_SESSION['branch_code'];
      $data['bname'] = $_SESSION['branch_name'];
      $data['status'] = 'NEW';
      $data['company_id'] = $_SESSION['company'];
      $this->db->trans_start();

      // CREATE INVENTORY
      $this->db->insert('tbl_repo_inventory', $data);
      $data['repo_inventory_id'] = $this->db->insert_id();

      $select = "
        s.sid, s.bcode, s.bname, r.region,
        DATE_FORMAT(s.date_sold, '%Y-%m-%d') AS date_sold,
        DATE_FORMAT(s.cr_date, '%Y-%m-%d') AS date_registered,
        c.cid, c.cust_code, c.first_name, c.middle_name,
        c.last_name, e.*
";
      $this->insert_history(
        $data['repo_inventory_id'],
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
        $data['repo_inventory_id'],
        'REPO_IN',
        $this->db->get_where('tbl_repo_inventory ri','ri.repo_inventory_id = '.$data['repo_inventory_id'])->row_array()
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

  public function update_inv_status($repo_inventory_id, $status) {
    $this->db->update('tbl_repo_inventory', ['status' => $status], ['repo_inventory_id' => $repo_inventory_id]);
  }

  public function save_registration($repo_inventory_id, $repo_sales_id, $registration) {
    $this->db->trans_begin();

    if ($this->db->insert('tbl_repo_registration', $registration)) {
      $repo_registration_id = $this->db->insert_id();
      // Attachment
      $attachment = [];
      foreach ($_FILES['attachments'] as $key => $files) {
        $dir = '/rms_dir/repo/registration/'.$repo_registration_id.'/';
        if ($key === 'name') {
          foreach ($files as $key => $file) {
            $attachment[$key] = $dir.$key.'_'.$file[0];
          }
        }
      }
      $attachments = json_encode($attachment);
      $upload_status = $this->file->upload('attachments', '/repo/registration/'.$repo_registration_id);
      $this->db->update('tbl_repo_registration', ['attachment'=>$attachments], 'repo_registration_id='.$repo_registration_id);

      $this->db->query("
        UPDATE tbl_repo_inventory ri, tbl_repo_sales rs
        SET ri.status='REGISTERED', rs.repo_registration_id = {$repo_registration_id}
        WHERE ri.repo_inventory_id = rs.repo_inventory_id AND rs.repo_sales_id = {$repo_sales_id}
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

    if ($this->db->trans_status() && $upload_status) {
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

    // REPO SALES
    if ($this->db->insert('tbl_repo_sales', $sales['repo_sale'])) {
      $this->insert_history(
        $sales['repo_sale']['repo_inventory_id'],
        'REPO_SALES',
        $this
          ->db
          ->select("
            c.*, ri.*, rs.repo_sales_id,
            rs.rsf_num, rs.ar_num, rs.ar_amt,
            rs.date_sold, DATE_FORMAT(rs.date_created, '%Y-%m-%d') AS date_created
          ")
          ->join('tbl_customer c', 'c.cid = rs.customer_id', 'join')
          ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rs.repo_inventory_id', 'join')
          ->get_where(
            'tbl_repo_sales rs',
            [ 'rs.repo_sales_id' => $this->db->insert_id() ]
          )
          ->row_array()
      );
    }
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
        ') AS ''
      ")
      ->from('tbl_repo_batch rb')
      ->where('rb.bcode = '.$_SESSION["branch_code"])
      ->order_by('rb.repo_batch_id', 'DESC')
      ->get();
    return $this->table->generate($result);
  }

  public function batch($repo_batch_id) {
    $data = $this->db->select('reference, misc_expenses')->get_where('tbl_repo_batch', 'repo_batch_id='.$repo_batch_id)->row_array();
    $this->table->set_template(["table_open" => "<table class='table'>"]);
    $result = $this->db
      ->select('
        TRIM(CONCAT(IF(c.last_name IS NULL, " " , CONCAT(c.last_name,",")), " ", IFNULL(c.first_name, " "), " ", IFNULL(c.middle_name, " "))) AS "Customer Name",
        c.cust_code AS "Customer Code",
        e.engine_no AS "Engine #",
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
      ->join('tbl_repo_inventory ri', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_repo_registration rr', 'rs.repo_registration_id = rr.repo_registration_id', 'left')
      ->join('tbl_engine e', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_customer c', 'rs.customer_id = c.cid', 'left')
      ->where('rb.repo_batch_id = '.$repo_batch_id .' AND rs.bcode = '.$_SESSION['branch_code'])
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
      ->from('tbl_repo_inventory ri')
      ->join('tbl_repo_sales rs', 'ri.repo_inventory_id = rs.repo_inventory_id', 'inner')
      ->join('tbl_engine e', 'e.eid = ri.engine_id', 'inner')
      ->join('tbl_plate p', 'p.plate_id = e.plate_id', 'left')
      ->join('tbl_customer c', 'c.cid = rs.customer_id', 'inner')
      ->where('rs.bcode = '.$_SESSION["branch_code"].' AND rs.repo_batch_id IS NULL')
      ->order_by('rs.date_sold', 'DESC')
      ->get();

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
      WHERE rb.doc_no IS NULL
    ";
    $result = $this->db->query($sql);
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
        'IFNULL(sop_hpg_pnp_clearance,0) AS sop_hpg_pnp_clrearance', 'IFNULL(rbb.insurance,0) AS insurance',
        'IFNULL(rbb.emission,0) AS emission', 'IFNULL(rbb.unreceipted_renewal_tip,0) AS unreceipted_renewal_tip',
        'IFNULL(rbb.unreceipted_transfer_tip,0) AS unreceipted_transfer_tip',
        'IFNULL(rbb.unreceipted_macro_etching_tip,0) AS unreceipted_macro_etching_tip',
        'IFNULL(rbb.unreceipted_hpg_pnp_clearance_tip,0) AS unreceipted_hpg_pnp_clearance_tip',
        'IFNULL(rbb.unreceipted_plate_tip,0) AS unreceipted_plate_tip',
      ])
      ->get_where('tbl_repo_branch_budget rbb', 'rbb.bcode = '. $branch_code)->row_array();
  }

  public function check_registration(string $type, array $data = []) {
    switch ($type) {
      case 'GET_REFERENCE':
        return $this->db
          ->select('rb.repo_batch_id, rb.reference')
          ->get_where('tbl_repo_batch rb', 'rb.status = "DEPOSITED"')
          ->result_array();
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
        $repo_batch = $this->db->query($sql)->row_array();

        $this->table->set_template(["table_open" => "<table class='table'>"]);
        $this->table->set_heading('', '#', 'Branch', 'Date Sold', 'Engine #', 'AR #', 'AR Amt', 'Status', '');
        foreach (json_decode($repo_batch['repo_registration'], 1) as $i => $repo_regn) {
          $this->table->add_row(
            "<input type='checkbox' id='cb-{$repo_regn['repo_registration_id']}' value='{$repo_regn['repo_registration_id']}'>", ++$i,
            $repo_regn['branch'], $repo_regn['date_sold'], $repo_regn['engine_no'], $repo_regn['ar_num'], $repo_regn['ar_amt'], $repo_regn['status'],
            "<button class='btn btn-primary view' style='float:right;' type='button' name='REPO_UNIT' value='{$repo_regn['repo_registration_id']}'>View</button>"
          );
        }
        $repo_batch_table = $this->table->generate();

        $this->table->clear();

        $misc_exp_raw = json_decode($repo_batch['misc_expenses'], 1);
        if (isset($misc_exp_raw)) {
          $this->table->set_template(["table_open" => "<table class='table'>"]);
          $this->table->set_heading('', '#', 'OR Date', 'OR No.', 'Expense Type', 'Amount', 'Status', '');
          $misc_count = 0;
          foreach ($misc_exp_raw as $id => $misc) {
            if ($misc['is_deleted'] !== "1") {
              $misc_count++;
              $this->table->add_row(
                form_checkbox("misc_exp_id", $id), $misc_count, $misc['or_date'], $misc['or_no'] ,
                $misc['expense_type'], $misc['amount'], $misc['status'],
                form_button(["class"=>"btn btn-primary view", "style"=>"float:right", "name"=>"MISC_EXP", "value"=>$id, "content"=>"View"])
              );
            }
          }
          $repo_batch_table .= $this->table->generate();
        }

        return json_encode([
          "table" =>  $repo_batch_table
        ]);
        break;

      case 'VIEW_ATTACHMENT':
        switch ($data['type']) {
          case 'MISC_EXP':
            return $this->db->select('misc_expenses->>\'$."'.$data['misc_expense_id'].'"\' AS misc_expenses')
            ->get_where('tbl_repo_batch rb', 'repo_batch_id='.$data['repo_batch_id'])
            ->row_array()['misc_expenses'];
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
}
