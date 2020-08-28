<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  public function all() {
    $slct = <<<SLCT
      rs.*, e.*,
      c.*
SLCT;
    return $this->db->select()
      ->from('tbl_repo_sales rs')
      ->join('tbl_engine e', 'e.eid = rs.eid', 'inner')
      ->join('tbl_customer c', 'c.cid = rs.cid', 'left')
      ->where('rs.bcode = '.$_SESSION['branch_code'])
      ->order_by('registration_date ASC')
      ->get()->result_array();
  }

  public function get_sales($select, $where) {
    $result = $this->db->select($select)
      ->from('tbl_engine e')
      ->join('tbl_sales s', 'e.eid = s.engine', 'left')
      ->join('tbl_customer sc', 'sc.cid = s.customer', 'left')
      ->join('tbl_repo_sales rs', 'rs.eid = e.eid', 'left')
      ->join('tbl_customer rsc', 'rsc.cid = rs.cid', 'left')
      ->where($where);
    return $result->get()->row_array();
  }

  public function insert_history($engine_id) {
    $engine_count = $this->db->select()
      ->from('tbl_sales_history')
      ->where('eid = '.$engine_id)
      ->get()->num_rows();
    if ($engine_count === 0) {
      $result = $this->db->query("
        SELECT
          engine AS eid, customer AS cid, bcode, bname, 'BNEW' AS sales_type,
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

  public function claim() {
    $repo = $this->session->flashdata('repo');
    $engine_id = $repo['eid'];
    $customer_id = $repo['cid'];
    $get_repo = <<<SQL
      SELECT
        *
      FROM
        tbl_repo_sales
      WHERE
        eid = {$engine_id}
SQL;
    $repo = $this->db->query($get_repo)->row_array();
    $data['registration_date'] = $this->input->post('regn-date');
    $data['bcode'] = $_SESSION['branch_code'];
    $data['bname'] = $_SESSION['branch_name'];
    $data['company_id'] = $_SESSION['company'];

    if (count($repo) > 0) {
      $data['repo_sales_id'] = $repo['repo_sales_id'];
      $this->db->update('tbl_repo_sales', $data);
      echo json_encode(['log' => $exist]);
    } else {
      $data['eid'] = $engine_id;
      $data['cid'] = $customer_id;
      $data['date_sold'] = $repo['date_sold'];
      if ($this->db->insert('tbl_repo_sales', $data)) {
        $_SESSION['messages'][] = 'Success!';
      }
    }
  }

  public function inventory() {
    return null;
  }

  public function expiration($date_registered) {
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
    $format .= ($interval->m !== 0) ? " %m {$month} " : '';
    $format .= ($interval->y !== 0 && $interval->m !== 0) ? " and " :'';
    $format .= ($interval->d !== 0) ? " %d {$day}" : '';
    $expire['message'] = $interval->format($format." ".$expired);

    return $expire;
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

  private function history($repo_sales_id, $action, $logs) {
    $data = [
      'repo_sales_id' => $repo_sales_id,
      'user' => $_SESSION['username'],
      'action' => $action,
      'data' => $logs
    ];
    $this->db->insert('tbl_repo_history', $data);
  }
}
