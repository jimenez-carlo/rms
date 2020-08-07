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

  public function claim($engine_id, $customer_id) {
    if (isset($engine_id) && isset($customer_id)) {
      $repo_engines = $this->session->flashdata('repo');
      $data['eid'] = $engine_id;
      $data['bcode'] = $_SESSION['branch_code'];
      $data['bname'] = $_SESSION['company_code'].' '.$_SESSION['branch_name'];
      //$data['date_registered'] = $repo_engines[$data['eid']]['registration_date'];
      //$data['cid'] = $repo_engines[$data['eid']]['cid'];
      if ($this->db->insert('tbl_repo_sales', $data)) {
        //unset($repo_engines[$data['eid']]);
        $this->session->set_flashdata(['repo' => $repo_engines]);
        echo json_encode(['branch' => $data['bname'], 'log' => $this->session->flashdata('repo')]);
      }
    }
  }

  public function inventory() {
    return null;
  }

}
