<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Si_model extends CI_Model {
  function __construct() {
    parent::__construct();
    $this->is_printed = 'is_printed';
    $this->date_printed = 'date_printed';
    $this->pnp_status = 'pnp_status';
    $this->date_pnp_tag = 'date_pnp_tag';
  }

  public function get_rrt_class($branch_code = "") {
    $result = $this->db->query("SELECT rrt_class from rrt_reg_tbl where branch_code = ".$_SESSION['b_code']);
    return $result->row();
  }

  public function for_print_si($b_code){
    return $this->db
      ->select('bobj.bobj_sales_id, bobj.si_custname, bobj.si_engin_no, bobj.si_dsold')
      ->from('tbl_bobj_sales bobj')
      ->join('tbl_engine e', 'bobj.si_engin_no = e.engine_no', 'inner')
      ->join('tbl_si si', 'si.eid = e.eid', 'left')
      ->join('tbl_region r', 'r.rid = bobj.rrt_region', 'left')
      ->where('(si.is_printed = 0 OR si.eid IS NULL) AND regn_status <> "Self Registration"')
      ->where("bobj.si_bcode = '{$b_code}' AND r.region = '{$_SESSION['rrt_region_name']}'")
      ->get()
      ->result_array();
  }

  public function si_print_data($bobj_sales_ids) {
    if (isset($bobj_sales_ids)) {
      $bobj_sales_id_array = array_keys($bobj_sales_ids);
      $select = array(
        'e.eid', 'bobj_sales_id', 'si_bcode', 'si_bname',
        'si_vatregtin', 'si_baddress', 'si_sino', 'si_custname',
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
        ->from('tbl_bobj_sales bobj')
        ->join('tbl_engine e', 'e.engine_no = bobj.si_engin_no', 'inner')
        ->join('tbl_si si', 'e.eid = si.eid', 'left')
        ->where_in('bobj_sales_id', $bobj_sales_id_array)
        ->get()->result_array();
    }
  }

  public function reprint($ltid, $bcode) {
      return $this->db
        ->select('lt.code, s.bcode, bobj.bobj_sales_id, bobj.si_custname, bobj.si_engin_no, bobj.si_dsold')
        ->from('tbl_lto_transmittal lt')
        ->join('tbl_sales s', 's.lto_transmittal = lt.ltid', 'inner')
        ->join('tbl_engine e', 'e.eid = s.engine', 'inner')
        ->join('tbl_bobj_sales bobj', 'bobj.si_engin_no = e.engine_no', 'inner')
        ->where('regn_status <> "Self Registration"')
        ->where('lt.ltid', $ltid)
        ->where('s.bcode', $bcode)
        ->get()->result_array();
  }


  public function tag_data($engine_no){
    $result =$this->db->query("UPDATE customer_tbl SET si_date_received = CURDATE() WHERE engine_no = '$engine_no'");
    return $result;
  }

  public function update_si($si){
    if (isset($si['eid'])) {
      $eid = $si['eid'];
      $is_printed = $si['is_printed'] ?? $this->is_printed;
      $date_printed = $si['date_printed'] ?? $this->date_printed;
      $pnp_status = $si['pnp_status'] ?? $this->pnp_status;
      $date_pnp_tag = $si['date_pnp_tag'] ?? $this->date_pnp_tag;

      return $this->db->query("
        REPLACE INTO tbl_si
        SELECT e.eid, IFNULL({$is_printed}, 0), {$date_printed}, IFNULL({$pnp_status},0), {$date_pnp_tag}
        FROM tbl_engine e
        LEFT JOIN tbl_si si ON e.eid = si.eid
        WHERE e.eid = {$eid}
      ");
    }
  }

  public function get_transmittal($filter){
    $branch_code = (!empty($filter['bcode'])) ? "s.bcode = {$filter['bcode']}" : "s.bcode <> 0";
    $transmittal_date = (!empty($filter['date_from']) && !empty($filter['date_to']))
      ? "lt.date BETWEEN '{$filter['date_from']} 00:00:00' AND '{$filter['date_to']} 23:59:59'"
      : 'lt.date BETWEEN DATE_FORMAT(NOW()-INTERVAL 3 DAY, "%Y-%m-%d 00:00:00") AND NOW()';

    return $this->db
      ->select("
        DATE_FORMAT(lt.date, '%Y-%m-%d') AS 'Date Transmittal', lt.code AS 'Transmittal#',
        CONCAT(s.bcode, ' ', ANY_VALUE(s.bname)) AS Branch, COUNT(*) AS 'Engine Count',
        CONCAT('<a class=\"btn btn-success\" href=\"".base_url('si/reprint/')."',lt.ltid,'/',s.bcode,'\" target=\"_blank\">Reprint</a>') AS '',

      ")
      ->from('tbl_lto_transmittal lt')
      ->join('tbl_sales s', 'lt.ltid = s.lto_transmittal','inner')
      ->where('s.registration_type <> "Self Registration"')
      ->where($branch_code)
      ->where($transmittal_date)
      ->where('lt.region', $_SESSION['rrt_region_id'])
      ->where('s.date_sold >= "2021-01-01 00:00:00"')
      ->group_by(['lt.ltid','s.bcode'])
      ->order_by('lt.date desc, s.bcode ASC')
      ->get();
  }

  public function self_regn() {
    return $this->db
      ->select('
        CONCAT("<input type=checkbox name=engine_ids[] value=",e.eid,">") AS "", c.cust_code AS "Customer Code",
        CONCAT(IFNULL(c.last_name,""), ", ", IFNULL(c.first_name,""), IFNULL(c.middle_name,"")) AS "Customer Name",
        e.engine_no AS "Engine#", DATE_FORMAT(s.date_sold, "%Y-%m-%d") AS "Date Sold",
        s.registration_type AS "Registration Type"'
      )
      ->from('tbl_sales s')
      ->join('tbl_customer c', 'c.cid = s.customer', 'inner')
      ->join('tbl_engine e', 'e.eid = s.engine', 'inner')
      ->join('tbl_si si', 'e.eid = si.eid', 'left')
      ->where('s.region', $_SESSION['rrt_region_id'])
      ->where('s.date_sold >= "2021-01-01 00:00:00"')
      ->where('s.registration_type = "Self Registration"')
      ->where('(si.eid IS NULL OR si.pnp_status = 0)')
      ->order_by('s.date_sold ASC, c.last_name ASC')
      ->get();
  }

}
