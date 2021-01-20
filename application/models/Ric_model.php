<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Ric_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  public function get_batch_for_ric() {
    return $this->db
      ->select("
        ep.epid, ep.reference,
        FORMAT(SUM(IF(s.is_penalty_for_ric=1,s.penalty,0)),2) AS total_penalty,
        MAX(s.is_penalty_for_ric) AS is_penalty_for_ric,
        SUM(IF(s.is_penalty_for_ric=1,1,0)) AS number_of_engines,
        MAX(CASE WHEN susb.sid IS NULL THEN 1 ELSE 0 END) AS registration_incomplete,
        c.company_code AS company
      ", false)
      ->from('tbl_sales s')
      ->join('tbl_electronic_payment ep', 'ep.epid = s.electronic_payment', 'inner')
      ->join('tbl_company c', 'c.cid = ep.company', 'left')
      ->join('tbl_region r', 'r.rid = ep.region', 'left')
      ->join('tbl_sap_upload_sales_batch susb', 'susb.sid = s.sid', 'left')
      ->where('ep.ric_id IS NULL AND r.region="'.$_SESSION['rrt_region_name'].'"')
      ->group_by('ep.epid')
      ->having('registration_incomplete = 0 AND is_penalty_for_ric = 1')
      ->get()->result_array();
  }

  public function create_ric($data) {
    $this->db->trans_start();
    list($type) = array_keys($data);
    switch ($type) {
      case 'PENALTY':
        $e_payment_ids = $data['PENALTY']['epids'];
        $reference = $data['PENALTY']['ric_number'];
        $amount = $this->db
          ->select('SUM(s.penalty) AS total_amount_penalty')
          ->where_in('s.electronic_payment', $e_payment_ids)
          ->get('tbl_sales s')->row_array()['total_amount_penalty'];
        $new_ric = [
          "reference_num" => $reference,
          "type"  => $type,
          "amount" => $amount
        ];
        $this->db->insert('tbl_ric', $new_ric);

        $this->db
          ->set(['ric_id' => $this->db->insert_id()])
          ->where_in("epid", $e_payment_ids)
          ->update("tbl_electronic_payment");
        break;
    }
    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function ric_batch($filter) {
    $condition = '';
    if ($_SESSION['dept_name'] === 'Regional Registration') {
      $condition .= 'AND r.region = "'.$_SESSION['rrt_region_name'].'" ';
    }

    if (!empty($filter)) {
      switch ($filter['status']) {
        case 'For Doc Number':
          $condition .= "AND ric.doc_num IS NULL AND ric.debit_memo IS NULL ";
          break;
        case 'For Deposit':
          $condition .= "AND ric.doc_num IS NOT NULL AND ric.debit_memo IS NULL ";
          break;
        case 'Deposited':
          $condition .= "AND ric.doc_num IS NOT NULL AND ric.debit_memo IS NOT NULL ";
          break;
        default:
          $condition .= "";
      }

      $condition .= (in_array($filter['region'], range(1,15))) ? "AND r.rid = {$filter['region']} " : "";
      $condition .= (in_array($filter['company'], range(1,8))) ? "AND c.cid = {$filter['company']} " : "";
      $condition .= (!empty($filter['reference'])) ? "AND ric.reference_num LIKE '%{$filter['reference']}%' " : "";
    }

    return $this->db
      ->select('ric.*, r.region AS region_name, c.company_code AS company')
      ->from('tbl_ric ric')
      ->join('tbl_electronic_payment ep', 'ep.ric_id = ric.ric_id', 'inner')
      ->join('tbl_region r', 'r.rid = ep.region', 'inner')
      ->join('tbl_company c', 'c.cid = ep.company', 'inner')
      ->where('1=1 '.$condition)
      ->limit('100')->order_by('ric.ric_id', 'desc')
      ->get()->result_array();
  }

  public function company_count(array $epids) {
    return $this->db
      ->select('company')
      ->where_in('epid', $epids)
      ->group_by('company')
      ->get('tbl_electronic_payment')
      ->result_array();
  }

  public function download($ric_id) {
    return [
      $this->db
        ->select('reference_num')
        ->where('ric_id='.$ric_id)
        ->get('tbl_ric')
        ->row_array()['reference_num'],
      $this->db
        ->select([
          'comp.company_code AS company', 'CONCAT(cust.first_name, " ", cust.last_name) AS customer_name',
          'cust.cust_code', 's.penalty', 's.registration', 'ep.reference'
        ])
        ->from('tbl_ric ric')
        ->join('tbl_electronic_payment ep', 'ep.ric_id = ric.ric_id', 'inner')
        ->join('tbl_company comp', 'ep.company = comp.cid', 'left')
        ->join('tbl_sales s', 's.electronic_payment = ep.epid', 'left')
        ->join('tbl_customer cust', 'cust.cid = s.customer', 'left')
        ->where('s.is_penalty_for_ric = 1')
        ->get()
        ->result_array()
    ];
  }

  public function data_in_ric($ric_id) {
    $this->load->library('table');
    $this->table->set_template([
      "table_open" => "<table class='table table-bordered' cellpadding='4' cellspacing='0'>"
    ]);
    $title = $this->db->select('reference_num')->where('ric_id = '.$ric_id)->get('tbl_ric')->row()->reference_num;
    $table = $this->table->generate(
      $this->db
        ->select([
          'CONCAT(s.bcode, " ", s.bname) AS "Branch"',
          'CONCAT(c.last_name, " ", c.first_name) AS "Customer Name"',
          'c.cust_code AS "Cust Code"', 'e.engine_no AS "Engine#"', 'e.chassis_no AS "Chassis#"',
          's.registration AS "Registration Amount"', 's.penalty AS "Penalty Amount"'
        ])
      ->from('tbl_sales s')
      ->join('tbl_engine e', 'e.eid = s.engine', 'inner')
      ->join('tbl_customer c', 'c.cid = s.customer', 'inner')
      ->join('tbl_electronic_payment ep', 'ep.epid = s.electronic_payment', 'inner')
      ->join('tbl_ric ric', 'ric.ric_id = ep.ric_id', 'inner')
      ->where('s.is_penalty_for_ric = 1 AND ric.ric_id = '.$ric_id)
      ->get()
    );
    return [
      "title" => $title,
      "table" => $table
    ];
  }
}
