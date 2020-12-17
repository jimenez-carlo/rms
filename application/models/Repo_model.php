<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  public function inventory() {
    $select = [
      'ri.repo_inventory_id',
      'ri.bcode', 'ri.bname',
      'ri.status AS repo_status',
      'e.*',
      'IFNULL(rsc.cid,c.cid) AS customer_id',
      'IFNULL(rsc.first_name,c.first_name) AS first_name',
      'IFNULL(rsc.last_name,c.last_name) AS last_name',
      'DATE_FORMAT(
        IFNULL(rr.date_registered, s.cr_date),
        "%Y-%m-%d"
      ) AS date_registered',
      'DATE_FORMAT(
        IFNULL(rs.date_sold, s.date_sold),
        "%Y-%m-%d"
      ) AS date_sold',
      'CASE
        WHEN ri.status IN("New", "Registered")  THEN true
      END AS regn_disabled',
      'CASE
        WHEN rs.is_active = 1 THEN true
      END AS sale_disabled'
    ];

    return $this->db
    ->distinct()
    ->select($select, false)
    ->from('tbl_repo_inventory ri')
    ->join('tbl_engine e', 'e.eid = ri.engine_id', 'inner')
    ->join('tbl_repo_registration rr', 'rr.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_sales s', 's.engine = e.eid', 'left')
    ->join('tbl_customer c', 'c.cid = s.customer', 'left')
    ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_customer rsc', 'rsc.cid = rs.customer_id', 'left')
    ->where([
      'ri.bcode' => $_SESSION['branch_code']
    ])
    ->order_by('date_sold DESC')
    ->get()->result_array();
  }

  public function get_repo_in($select, $where) {
    $result = $this->db->select($select)
      ->from('tbl_engine e')
      ->join('tbl_sales s', 'e.eid = s.engine', 'left')
      ->join('tbl_customer sc', 'sc.cid = s.customer', 'left')
      ->join('tbl_repo_inventory ri', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_repo_registration rr', 'rr.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
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
      ri.*, e.*, rr.repo_registration_id, rr.repo_batch_id,
      DATE_FORMAT(
        IFNULL(rr.date_registered, s.cr_date),
        '%Y-%m-%d'
      ) AS date_registered,
      rr.date_uploaded, rr.registration_type,
      rr.registration_amt, rr.pnp_clearance_amt,
      rr.emission_amt, rr.insurance_amt,
      rr.macro_etching_amt, rr.attachment, c.*,
      rs.repo_sales_id, rs.customer_id,
      rs.rsf_num, rs.ar_num,
      rs.ar_amt, rs.date_sold,
      rb.reference
    ")
    ->from('tbl_repo_inventory ri')
    ->join('tbl_engine e', 'e.eid = ri.engine_id', 'left')
    ->join('tbl_repo_registration rr', 'rr.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
    ->join('tbl_repo_batch rb', 'rb.repo_batch_id = rs.repo_batch_id AND rb.repo_batch_id = rr.repo_batch_id', 'left')
    ->join('tbl_customer c', 'c.cid = rs.customer_id', 'left')
    ->join('tbl_sales s', 's.engine = e.eid', 'left')
    ->where('ri.repo_inventory_id = '.$repo_inventory_id.' '.$status)->get()->row_array();
    return $engine_details;
  }

  public function claim($engine_id) {
    $get_repo = <<<SQL
      SELECT
        *
      FROM
        tbl_repo_inventory ri
      LEFT JOIN
        tbl_repo_registration rr ON rr.repo_inventory_id = ri.repo_inventory_id
      LEFT JOIN
        tbl_repo_batch rb ON rb.repo_batch_id = rr.repo_batch_id
      WHERE
        ri.engine_id = {$engine_id}
        AND ri.status != 'Registered'
        AND rb.repo_batch_id IS NULL
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

  public function save_registration($repo_inventory_id, $registration) {
    $this->db->trans_start();
    // Attachments
    $attachments = [];
    foreach ($_FILES['attachments'] as $key => $files) {
      $dir = '/rms_dir/repo/registration/'.$registration['repo_registration_id'].'/';
      if ($key === 'name') {
        foreach ($files as $key => $file) {
          $attachments[$key] = $dir.$key.'_'.$file[0];
        }
      }
    }

    $registration['attachment'] = json_encode($attachments);

    $update_qry = <<<SQL
      UPDATE
        tbl_repo_registration rr,
        tbl_repo_inventory ri
      SET
        ri.status = 'REGISTERED',
        rr.date_registered = '{$registration['date_registered']}',
        rr.registration_amt = {$registration['registration_amt']},
        rr.pnp_clearance_amt = {$registration['pnp_clearance_amt']},
        rr.insurance_amt = {$registration['insurance_amt']},
        rr.emission_amt = {$registration['emission_amt']},
        rr.macro_etching_amt = {$registration['macro_etching_amt']},
        rr.or_tip = {$registration['or_tip']},
        rr.pnp_tip = {$registration['pnp_tip']},
        rr.date_uploaded = NOW(),
        rr.registration_type = 'RENEW & TRANSFER',
        rr.attachment = '{$registration['attachment']}'
      WHERE
        ri.repo_inventory_id = rr.repo_inventory_id AND
        rr.repo_registration_id = {$registration['repo_registration_id']} AND
        ri.repo_inventory_id = {$repo_inventory_id}
SQL;

    $this->db->query($update_qry);

    $this->insert_history(
      $repo_inventory_id,
      'REGISTERED',
      $this
        ->db
        ->select("
          ri.*, rr.repo_registration_id, rr.repo_batch_id,
          rr.date_registered, rr.date_uploaded, rr.registration_type,
          rr.registration_amt, rr.pnp_clearance_amt, rr.emission_amt,
          rr.insurance_amt, rr.macro_etching_amt, rr.attachment,
          DATE_FORMAT(rr.date_created, '%Y-%m-%d') AS date_created
        ")
        ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rr.repo_inventory_id', 'left')
        ->get_where(
          'tbl_repo_registration rr',
          [
            'rr.repo_registration_id' => $registration['repo_registration_id']
          ]
        )->row_array()
    );

    $this->db->trans_complete();

    if ($this->db->trans_status()) {
      return $registration['repo_registration_id'];
    } else {
      echo 'Error';
    }
  }

  public function save_sales($sales) {
    // CUSTOMER
    $customer_exist = $this->db->get_where('tbl_customer', [ 'cust_code' => $sales['customer']['cust_code'] ], 1)->row_array();
    if (!isset($customer_exist)) {
      $this->db->insert('tbl_customer', $sales['customer']);
      $sales['repo_sale']['customer_id'] = $this->db->insert_id();
    }

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
    return
      $this->db
      ->distinct()
      ->select("
        rb.repo_batch_id, rb.reference, rb.bcode,
        rb.bname, (rb.amount + rb.bank_amount) AS amount,
        rb.doc_no, rb.debit_memo, rb.status
      ")
      ->from('tbl_repo_batch rb')
      ->join('tbl_repo_registration rr', 'rb.repo_batch_id = rr.repo_batch_id', 'left')
      ->join('tbl_repo_sales rs', 'rb.repo_batch_id = rs.repo_batch_id', 'left')
      ->join('tbl_repo_inventory iri', 'rr.repo_inventory_id = iri.repo_inventory_id', 'left')
      ->join('tbl_repo_inventory sri', 'rs.repo_inventory_id = sri.repo_inventory_id', 'left')
      ->where('rb.status != "Liquidated" AND (iri.bcode = '.$_SESSION["branch_code"].' OR sri.bcode = '.$_SESSION["branch_code"].') AND (rs.is_active = 1 OR rr.is_active = 1)')
      ->order_by('rb.repo_batch_id', 'DESC')
      ->get()->result_array();
  }

  public function batch($repo_batch_id) {
    return
      $this->db
      ->select('rb.*, rr.*, ri.*, rs.*, e.*, c.*')
      ->from('tbl_repo_batch rb')
      ->join('tbl_repo_registration rr', 'rb.repo_batch_id = rr.repo_batch_id', 'left')
      ->join('tbl_repo_inventory ri', 'rr.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_engine e', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_customer c', 'rs.customer_id = c.cid', 'left')
      ->where('rb.repo_batch_id = '.$repo_batch_id .' AND ri.bcode = '.$_SESSION['branch_code'])
      ->get()->result_array();
  }

  public function save_expense($data) {
    $data_json = json_encode($data['data']);
    $success = $this->db->query("
      UPDATE
        tbl_repo_batch
      SET
        misc_expenses = IF(misc_expenses IS NULL, '{$data_json}', JSON_MERGE_PATCH(misc_expenses, '{$data_json}'))
      WHERE
      repo_batch_id = {$data['repo_batch_id']}
    ");

    if ($success) {
      $_SESSION["message"][] = "Expense saved successfully.";
    }

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
        rr.date_registered, rr.registration_type, rr.registration_amt,
        rr.pnp_clearance_amt, rr.emission_amt, rr.insurance_amt,
        rr.macro_etching_amt, c.*
      ")
      ->from('tbl_repo_inventory ri')
      ->join('tbl_engine e', 'e.eid = ri.engine_id','left')
      ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_customer c', 'c.cid = rs.customer_id','left')
      ->join('tbl_repo_registration rr', 'rr.repo_inventory_id = ri.repo_inventory_id','left')
      ->where('rs.repo_batch_id = '.$repo_batch_id.' OR rr.repo_batch_id ='.$repo_batch_id)
      ->get()
      ->result_array();

    return [ 'batch' => $batch, 'batch_engines' => $batch_engines ];
  }

  public function get_for_ca() {
    $sql = <<<SQL
      SELECT
        rrt_region,
        company_code,
        SUM(no_of_unit) total_no_of_unit,
        FORMAT( SUM(no_of_unit) * 3600, 2) AS total_amount
        ,CONCAT('[',
          GROUP_CONCAT(DISTINCT '{
            "repo_batch_id": ',repo_batch_id,',
            "reference": "',reference,'",
            "bcode":',bcode, ', "bname": "',bname,'",
            "no_of_unit":',no_of_unit, ',
            "amount":', (no_of_unit*3600),
          '}'),
        ']') AS batches
      FROM (
      	SELECT
          rb.*
          ,c.*
          ,r.rid, r.region AS rrt_region
          ,COUNT(*) AS no_of_unit
      	FROM
      	  tbl_repo_batch rb
      	LEFT JOIN
      	  tbl_company c ON c.cid = rb.company_id
      	LEFT JOIN
      	  tbl_region r ON r.rid = rb.region_id
      	LEFT JOIN
      	  tbl_repo_sales rs ON rs.repo_batch_id = rb.repo_batch_id
      	LEFT JOIN
          tbl_repo_inventory ri ON ri.repo_inventory_id = rs.repo_inventory_id
        WHERE
      	  rb.status = 'FOR CA' AND DATE_FORMAT(rb.date_created, '%Y-%m-%d') <= DATE_FORMAT(NOW() - INTERVAL 1 DAY, '%Y-%m-%d')
      	GROUP BY rb.repo_batch_id
      ) AS first_qry
      GROUP BY company_code, rid, company_id
      ORDER BY rid, company_id
SQL;

    $this->db->simple_query("SET SESSION group_concat_max_len = 18446744073709551615");
    return $this->db->query($sql)->result_array();
  }

  public function print_ca(array $repo_batch_ids) {
    return $this->db
      ->select('rb.reference, rb.bcode, rb.bname, COUNT(*) AS no_of_unit, COUNT(*) * 3600 AS amount')
      ->from('tbl_repo_batch rb')
      ->join('tbl_repo_sales rs', 'rs.repo_batch_id = rb.repo_batch_id','left')
      ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rs.repo_inventory_id','left')
      ->where_in('rb.repo_batch_id', $repo_batch_ids)
      ->group_by('rb.repo_batch_id')
      ->get()->result_array();
  }

  public function save_ca(array $batches) {
    $this->db->trans_start();
    foreach ($batches as $repo_batch_id => $doc_no) {
      $this->db->query("
	UPDATE
	  tbl_repo_batch rb
	INNER JOIN
	  tbl_region_budget rbgt ON rbgt.region_id = rb.region_id
	INNER JOIN (
	  SELECT
	    rb.repo_batch_id, COUNT(*) AS no_of_unit
	  FROM
	    tbl_repo_batch rb
	  LEFT JOIN
	    tbl_repo_sales rs ON rs.repo_batch_id = rb.repo_batch_id
	  LEFT JOIN
	    tbl_repo_inventory ri ON ri.repo_inventory_id = rs.repo_inventory_id
	  WHERE
	    rb.repo_batch_id = {$repo_batch_id}
	  GROUP BY
	    rb.repo_batch_id
	) AS count ON count.repo_batch_id = rb.repo_batch_id
	SET
	  rb.doc_no = '{$doc_no}',
	  rb.date_doc_no_encoded = NOW(),
	  rb.bank_amount = count.no_of_unit * rbgt.repo_bmi,
	  rb.amount = count.no_of_unit * rbgt.repo_cmc,
	  rb.status = 'FOR DEPOSIT'
	WHERE
	  rb.repo_batch_id = {$repo_batch_id}
      ");
    }
    $this->db->trans_complete();
    return $this->db->status();
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

  public function get_tip_matrix($region_id) {
    return $this->db->get_where('tbl_tip', 'region_id = '. $region_id)->row_array();
  }

}
