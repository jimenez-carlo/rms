<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  private function history($repo_inventory_id, $action, array $logs) {
    $data = [
      'repo_inventory_id' => $repo_inventory_id,
      'user' => $_SESSION['username'],
      'action' => $action,
      'data' => json_encode($logs)
    ];
    $this->db->insert('tbl_repo_history', $data);
  }

  public function all() {
    return $this->db->select("
      ri.repo_inventory_id, ri.bcode, ri.bname,
      ri.status AS repo_status, e.*,
      DATE_FORMAT(IFNULL(rr.date_registered, s.cr_date), '%Y-%m-%d') AS date_registered,
      CASE
        WHEN ri.status = 'Registered' THEN true
      END AS update_disabled,
      CASE
        WHEN ri.status != 'Registered' THEN true
      END AS view_disabled
    ")
    ->from('tbl_repo_inventory ri')
    ->join('tbl_engine e', 'e.eid = ri.engine_id', 'inner')
    ->join('tbl_repo_registration rr', 'rr.repo_registration_id = ri.repo_registration_id', 'left')
    ->join('tbl_sales s', 's.engine = e.eid', 'left')
    ->where('ri.bcode = '.$_SESSION['branch_code'])
    //->order_by('registration_date ASC')
    ->get()->result_array();
  }

  public function get_repo_in($select, $where) {
    $result = $this->db->select($select)
      ->from('tbl_engine e')
      ->join('tbl_sales s', 'e.eid = s.engine', 'left')
      ->join('tbl_customer sc', 'sc.cid = s.customer', 'left')
      ->join('tbl_repo_inventory ri', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_repo_registration rr', 'rr.repo_registration_id = ri.repo_registration_id', 'left')
      ->join('tbl_repo_sales rs', 'rs.repo_sales_id = ri.repo_sales_id', 'left')
      ->join('tbl_customer rsc', 'rsc.cid = rs.customer_id', 'left')
      ->where($where);
    return $result->get()->row_array();
  }

  public function engine_details($repo_inventory_id) {
    $engine_details = $this->db->select("
      ri.*, e.*, rt.repo_tip_id,
      rr.repo_registration_id, rr.repo_rerfo_id,
      DATE_FORMAT(IFNULL(rr.date_registered, s.cr_date), '%Y-%m-%d') AS date_registered,
      rr.date_uploaded, rr.registration_type,
      rr.registration_amt, rr.pnp_clearance_amt,
      rr.emission_amt, rr.insurance_amt,
      rr.macro_etching_amt, rr.attachment, c.*,
      rs.repo_sales_id, rs.customer_id,
      rs.rsf_num, rs.ar_num,
      rs.ar_amt, rs.date_sold,
      rr.tip_amt AS rr_tip_amt,
      rt.tip_amt AS rt_tip_amt
    ")
    ->from('tbl_repo_inventory ri')
    ->join('tbl_engine e', 'e.eid = ri.engine_id', 'left')
    ->join('tbl_repo_tip rt', 'rt.branch_code = ri.bcode', 'left')
    ->join('tbl_repo_registration rr', 'rr.repo_registration_id = ri.repo_registration_id', 'left')
    ->join('tbl_repo_sales rs', 'rs.repo_sales_id = ri.repo_sales_id', 'left')
    ->join('tbl_customer c', 'c.cid = rs.customer_id', 'left')
    ->join('tbl_sales s', 's.engine = e.eid', 'left')
    ->where('ri.repo_inventory_id = '.$repo_inventory_id)->get()->row_array();
    return $engine_details;
  }


  public function insert_history($engine_id) {
    $engine_count = $this->db->select()
      ->from('tbl_sales_history')
      ->where('eid = '.$engine_id)
      ->get()->num_rows();
    if ($engine_count === 0) {
      $result = $this->db->query("
        SELECT
          sid, engine AS eid, customer AS cid, bcode, bname, 'BNEW' AS sales_type,
          DATE_FORMAT(date_sold, '%Y-%m-%d') AS date_sold,
          DATE_FORMAT(registration_date, '%Y-%m-%d') AS date_registered
        FROM
          tbl_sales
        WHERE
          engine = {$engine_id}
        ORDER BY sid DESC LIMIT 1
      ")->row_array();

      if (!empty($result)) {
        $this->db->insert('tbl_sales_history', $result);
      } else {
        echo json_encode(['error' => 'Engine not found']); exit;
      }
    }

    $this->db->query("
      INSERT
        tbl_sales_history
        SELECT
          NULL, eid, cid, bcode, bname, 'REPO', NULL, NULL
        FROM
          tbl_repo_sales WHERE eid = {$engine_id}
    ");
  }

  public function claim($engine_id) {
    $repo = $this->session->flashdata('repo');
    $customer_id = $repo['customer_id'];
    $get_repo = <<<SQL
      SELECT
        *
      FROM
        tbl_repo_inventory ri
      LEFT JOIN
        tbl_repo_registration rr ON rr.repo_registration_id = ri.repo_registration_id
      LEFT JOIN
        tbl_repo_rerfo rrf ON rrf.repo_rerfo_id = rr.repo_rerfo_id
      WHERE
        ri.engine_id = {$engine_id} AND ri.status != 'Registered'
        AND rrf.repo_rerfo_id IS NULL
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
      if ($this->db->insert('tbl_repo_inventory', $data)) {
        $data['repo_inventory_id'] = $this->db->insert_id();
        $_SESSION['messages'][] = 'Success!';
      }
    }
    $this->history($data['repo_inventory_id'], 'REPO_CLAIM', $data);
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
    if ($date_expired > $now) {
      $interval = $now->diff($date_expired);
      $day   .= ($interval->d > 1) ? 's' : '';
      $month .= ($interval->m > 1) ? 's' : '';
      $year  .= ($interval->y > 1) ? 's' : '';
      if (($interval->y == 0 && in_array($interval->m, [0,1]) && $interval->d < 31)) {
        $expire['status'] = 'warning';
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
    $expire['message'] = ($message !== " ") ? $message : 'Will expire tomorrow' ;

    return $expire;
  }

  public function save_registration($inventory_id, $registration, $sales, $customer) {
    //echo '<pre>'; var_dump($sales); echo '</pre>'; die();
    $registration['repo_inventory_id'] = $inventory_id;
    $registration['date_uploaded'] = date('Y-m-d H:i:s');
    $registration['registration_type'] = 'RENEW & TRANSFER';
    $registration['tip_amt'] = $this->db->query("
      SELECT
        tip_amt
      FROM
        tbl_repo_tip
      WHERE
        branch_code = {$_SESSION['branch_code']}
    ")->row('tip_amt') ?? '0';

    // GENERATE RERFO
    $rerfo_num = $_SESSION['branch_code'].'-'.date('Ymd');
    // CHECK IF RERFO EXIST
    $this->db->trans_start();
    $query = "
      SELECT
        repo_rerfo_id
      FROM
        tbl_repo_rerfo
      WHERE
        rerfo_number = '{$rerfo_num}'
";
    $registration['repo_rerfo_id'] = $this->db->query($query)->row('repo_rerfo_id');

    // CREATE RERFO
    if (!isset($registration['repo_rerfo_id'])) {
      $rerfo = ['rerfo_number' => $rerfo_num, 'bcode', $_SESSION['branch_code'], 'status' => 'NEW'];
      $this->db->insert('tbl_repo_rerfo', $rerfo);
      $registration['repo_rerfo_id'] = $this->db->insert_id();
    }

    // Insert Registration
    if($this->db->insert('tbl_repo_registration', $registration)) {
      $registration['repo_registration_id'] = $this->db->insert_id();;
    }

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

    $this->db->update(
      'tbl_repo_registration',
      ['attachment' => json_encode($attachments)],
      ['repo_registration_id' => $registration['repo_registration_id']]
    );

    // Insert Customer
    $customer_id = $this->db->query("
      SELECT
        cid AS customer_id
      FROM
        tbl_customer
      WHERE
        cust_code = {$customer['cust_code']}
    ")->row('customer_id');

    if (empty($customer_id)) {
      if ($this->db->insert('tbl_customer', $customer)) {
        $customer_id = $this->db->insert_id();
      }
    }

    // Insert Sales
    $sales['customer_id'] = $customer_id;
    $sales['repo_inventory_id'] = $inventory_id;
    if($this->db->insert('tbl_repo_sales', $sales)) {
      $sales_id = $this->db->insert_id();
    }

    $inventory['repo_sales_id'] = $sales_id;
    $inventory['repo_registration_id'] = $registration['repo_registration_id'];
    $inventory['status'] = 'Registered';
    $this->db->update('tbl_repo_inventory', $inventory, ['repo_inventory_id' => $inventory_id]);
    // Update Repo Inventory

    $this->history(
      $inventory_id,
      'RENEW/TRANSFER/REPO_SALES',
      [
        'inventory' => $inventory,
        'registration' => $registration,
        'sales' => $sales
      ]
    );

    $this->db->trans_complete();
    return $registration['repo_registration_id'];
  }

  public function save_repo_sales($repo_sales_id, $data, $sold) {
    $data['date_upload'] = date('Y-m-d H:i:s');
    $rerfo_num = $_SESSION['branch_code'].'-'.date('Ymd');
    // CHECK EXISTING RERFO
    $query = "SELECT repo_rerfo_id FROM tbl_repo_rerfo WHERE rerfo_number = '{$rerfo_num}'";
    $data['repo_rerfo_id'] = $this->db->query($query)->row('repo_rerfo_id');
    if (!isset($data['repo_rerfo_id'])) {
      // CREATE RERFO
      $rerfo = ['rerfo_number' => $rerfo_num, 'status' => 'NEW'];
      $this->db->insert('tbl_repo_rerfo', $rerfo);
      $data['repo_rerfo_id'] = $this->db->insert_id();
    }

    if (!isset($data['regn_type']['transfer']) && $sold === 'no') {
      // INSERT EXPENSE
      if ($this->db->update('tbl_repo_sales', $data, "repo_sales_id = {$repo_sales_id}")) {
        $this->history($repo_sales_id, 'RENEW', json_encode($data));
      }
    }
  }

  public function rerfo_list() {
    return
      $this->db
      ->distinct()
      ->select('rrf.*')
      ->from('tbl_repo_rerfo rrf')
      ->join('tbl_repo_registration rr', 'rrf.repo_rerfo_id = rr.repo_rerfo_id', 'left')
      ->join('tbl_repo_inventory ri', 'rr.repo_registration_id = ri.repo_registration_id', 'left')
      ->where('ri.bcode = '.$_SESSION['branch_code'])
      ->order_by('rrf.repo_rerfo_id', 'DESC')
      ->get()->result_array();
  }

  public function rerfo($rerfo_id)
  {
    return
      $this->db
      ->select('rrf.*, rr.*, ri.*, rs.*, e.*, c.*')
      ->from('tbl_repo_rerfo rrf')
      ->join('tbl_repo_registration rr', 'rrf.repo_rerfo_id = rr.repo_rerfo_id', 'left')
      ->join('tbl_repo_inventory ri', 'rr.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_repo_sales rs', 'rs.repo_inventory_id = ri.repo_inventory_id', 'left')
      ->join('tbl_engine e', 'ri.engine_id = e.eid', 'left')
      ->join('tbl_customer c', 'rs.customer_id = c.cid', 'left')
      ->where('ri.bcode = '.$_SESSION['branch_code'])
      ->get()->result_array();
  }

  public function save_expense($data) {
    $data_json = json_encode($data['data']);
    $success = $this->db->query("
      UPDATE
        tbl_repo_rerfo
      SET
        misc_expenses = IF(misc_expenses IS NULL, '{$data_json}', JSON_MERGE_PATCH(misc_expenses, '{$data_json}'))
      WHERE
      repo_rerfo_id = {$data['repo_rerfo_id']}
    ");

    if ($success) {
      $_SESSION["message"][] = "Expense saved successfully.";
    }

    return $success;
  }

}
