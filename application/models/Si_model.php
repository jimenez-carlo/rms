<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Si_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }

  public function get_rrt_class($branch_code = "") {
    $result = $this->db->query("SELECT rrt_class from rrt_reg_tbl where branch_code = ".$_SESSION['b_code']);
    return $result->row();
  }

  public function for_print_si($b_code){
    return $this->db
      ->select('bobj.bobj_sales_id, bobj.si_custname, bobj.si_engin_no, bobj.si_dsold')
      ->from('tbl_bobj_sales bobj')
      ->join('tbl_region r', 'r.rid = bobj.rrt_region', 'left')
      ->where('is_si_printed = 0')
      ->where("bobj.si_bcode = '{$b_code}' AND r.region = '{$_SESSION['rrt_region_name']}'")
      ->get()
      ->result_array();
  }

  public function si_print_data($bobj_sales_ids) {
    if (isset($bobj_sales_ids)) {
      $bobj_sales_id_array = array_keys($bobj_sales_ids);
      $select = array(
        'bobj_sales_id', 'si_bcode', 'si_bname', 'si_vatregtin',
        'si_baddress', 'si_sino', 'si_custname',
        'DATE_FORMAT(si_dsold, "%b %d, %Y") AS si_dsold',
        'si_cust_tin', 'si_cust_add', 'si_brand', 'si_app_id',
        'si_birpermitno', 'si_billing_no', 'si_drno',
        'si_modelcode', 'si_engin_no', 'si_chassisno',
        'si_color', 'si_fidocno', 'si_sono', 'si_custcode',
        'FORMAT(si_discount, 2) AS si_discount',
        'FORMAT(si_price, 2) AS si_price', 'FORMAT(si_vatsale, 2) AS si_vatsale',
        'FORMAT(si_vatexp, 2) AS si_vatexp', 'FORMAT(si_vatzero, 2) AS si_vatzero',
        'FORMAT(si_val_val, 2) AS si_val_val','FORMAT(si_totalamt, 2) AS si_totalamt'
      );
      return $this->db
        ->select($select)
        ->from('tbl_bobj_sales')
        ->where_in('bobj_sales_id', $bobj_sales_id_array)
        ->get()->result_array();
    }
  }

  public function tag_data($engine_no){
    $result =$this->db->query("UPDATE customer_tbl SET si_date_received = CURDATE() WHERE engine_no = '$engine_no'");
    return $result;
  }

  public function tag_si_printed($bobj_sales_id){
    return $this->db->update(
      'tbl_bobj_sales',
      ['is_si_printed' => 1, 'date_printed' => date('Y-m-d')],
      'bobj_sales_id='.$bobj_sales_id
    );
  }

  public function get_reprint($transmittal_id) {
    if (isset($transmittal_id)) {
      $select = array(
        'stp.si_bcode', 'stp.si_bname', 'stp.si_vatregtin',
        'stp.si_baddress', 'stp.si_sino', 'stp.si_custname',
        'DATE_FORMAT(stp.si_dsold, "%b %d, %Y") AS si_dsold',
        'stp.si_cust_tin', 'stp.si_cust_add', 'stp.si_brand', 'stp.si_app_id',
        'stp.si_birpermitno', 'stp.si_billing_no', 'stp.si_drno',
        'stp.si_modelcode', 'stp.si_engin_no', 'stp.si_chassisno',
        'stp.si_color', 'stp.si_fidocno', 'stp.si_sono', 'stp.si_custcode',
        'FORMAT(stp.si_discount, 2) AS si_discount',
        'FORMAT(stp.si_price, 2) AS si_price', 'FORMAT(stp.si_vatsale, 2) AS si_vatsale',
        'FORMAT(stp.si_vatexp, 2) AS si_vatexp', 'FORMAT(stp.si_vatzero, 2) AS si_vatzero',
        'FORMAT(stp.si_val_val, 2) AS si_val_val','FORMAT(stp.si_totalamt, 2) AS si_totalamt'
      );
      return $this->db
        ->select($select)
        ->from('si_tbl_print stp')
        ->join('customer_tbl ct', 'ct.engine_no = stp.si_engin_no AND ct.customer_id AND stp.si_custcode', 'left')
        ->join('transmittal_tbl tt', 'tt.transmittal_code = ct.transmittal_no', 'left')
        ->where('tt.transmittal_id', $transmittal_id)
        ->get()->result_array();
    }
  }

  public function get_transmittal_no($transmittal_no, $rrt_class = ""){
    $result = $this->db->query("
      SELECT DISTINCT t.transmittal_id, a.transmittal_no
      FROM `dev_rms`.`customer_tbl` a
      LEFT JOIN `dev_rms`.`transmittal_tbl` t ON t.transmittal_code = a.transmittal_no
      LEFT JOIN `dev_rms`.`rrt_reg_tbl` e ON a.`branch` = e.`branch_code`
      WHERE rrt_class = '".$rrt_class."' AND a.transmittal_no LIKE '%".$transmittal_no."%'
      ORDER BY a.`transmittal_no` DESC LIMIT 100
    ");

    return $result->result_object();
  }
}
