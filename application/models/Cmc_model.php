<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Cmc_model extends CI_Model{

  public $global;

  public function __construct()
  {
    parent::__construct();

    $this->global = $this->load->database('global', TRUE);
  }

  public function get_departments() {
    $result = $this->global->query("SELECT
      *
      FROM tbl_departments");
    return $result->result_object();
  }

  public function get_areas() {
    $result = $this->global->query("SELECT
      *
      FROM tbl_areas");
    return $result->result_object();
  }

  public function get_branches() {
    $result = $this->global->query("SELECT
      a.*,
      b.code AS company,
      c.name AS area
      FROM tbl_branches a
      INNER JOIN tbl_companies b ON a.company=b.cid
      INNER JOIN tbl_areas c ON a.area=c.aid");
    return $result->result_object();
  }

  public function get_full_branch_name($bid) {
    $result = $this->global->query("SELECT
      CONCAT(a.b_code,' ',b.code,' ',a.name) as branch_name
      FROM tbl_branches a
      INNER JOIN tbl_companies b ON a.company=b.cid
      WHERE bid=$bid");
    return $result->result_object();
  }

  public function get_companies() {
    $result = $this->global->query("SELECT
      *
      FROM tbl_companies");
    return $result->result_object();
  }

  public function rms_companies() {
    if ($_SESSION['company_code'] === 'MDI') {
      $where_company = 'cid = 8';
    } else {
      $where_company = 'cid != 8';
    }
    $companies = $this->db->get_where('tbl_company', $where_company)->result_array();
    return $companies;
  }

  public function get_users_info($positions) {
    $results = array();

    foreach ($positions as $position) {
      $result = $this->global->query("SELECT
        a.*,
        b.name,
        c.username
        FROM tbl_users_info a
        INNER JOIN tbl_positions b ON position=pid
        INNER JOIN tbl_users c ON a.uid=c.uid
        WHERE
        b.name LIKE '$position'
        ORDER BY c.username");
      array_push($results, $result->result_object());
    }

    return $results;
  }

  public function get_users_tbl($department="",$username="",$name="") {
    $result = $this->global->query("SELECT
      a.username,
      a.status,
      a.flag,
      a.role,
      CONCAT(b.firstname,' ',b.lastname) as full_name,
      b.*
      FROM tbl_users a
      INNER JOIN tbl_users_info b ON a.uid=b.uid
      WHERE
      username LIKE '%$username%' AND
      b.department LIKE '%$department%' AND
      (
        b.firstname LIKE '%$name%' OR
        b.lastname LIKE '%$name%'
    )");
    return $result->result_object();
  }

  public function get_branches_tbl($area="",$company="",$bcode="",$rm="",$am_ccod="",$am_csod="", $region="", $bid="") {
    if($area!="") $area_clause = "a.area = $area AND ";
    else $area_clause = "";
    if($region!="") $region_clause = "a.ph_region = $region AND ";
    else $region_clause = "";
    if($bid!="") $bid_clause = "a.bid = $bid AND ";
    else $bid_clause = "";

    $result = $this->global->query("SELECT
      a.*,
      b.code AS company,
      c.name AS area
      FROM tbl_branches a
      LEFT JOIN tbl_companies b ON a.company=b.cid
      LEFT JOIN tbl_areas c ON a.area=c.aid
      WHERE
      ".$bid_clause."
      ".$area_clause."
      ".$region_clause."
      a.company LIKE '%$company%' AND
      a.b_code LIKE '%$bcode%' AND
      a.rm LIKE '%$rm%' AND
      a.am_ccod LIKE '%$am_ccod%' AND
      a.am_csod LIKE '%$am_csod%'");
    return $result->result_object();
  }

  public function get_branches_bcode_tbl($bcode="") {
    $result = $this->global->query("SELECT
      a.*,
      b.code AS company,
      c.name AS area
      FROM tbl_branches a
      INNER JOIN tbl_companies b ON a.company=b.cid
      INNER JOIN tbl_areas c ON a.area=c.aid
      WHERE
      a.b_code = '$bcode'");
    return $result->result_object();
  }

  public function get_positions_tbl($department="",$name="") {
    if($department!="") $dept_clause = "a.department = $department AND";
    else $dept_clause = "";

    $result = $this->global->query("SELECT
      a.*,
      b.description AS department_name,
      c.name AS parent
      FROM tbl_positions a
      LEFT JOIN tbl_departments b ON a.department=b.did
      LEFT JOIN tbl_positions c ON a.parent = c.pid
      WHERE
      ".$dept_clause."
      a.name LIKE '%$name%' ");
    return $result->result_object();
  }

  public function get_deparments_tbl($name="") {
    $result = $this->global->query("SELECT
      *
      FROM tbl_departments
      WHERE
      description LIKE '%$name%' ");
    return $result->result_object();
  }

  public function get_areas_tbl($name="") {
    $result = $this->global->query("SELECT
      *
      FROM tbl_areas
      WHERE
      name LIKE '%$name%' ");
    return $result->result_object();
  }

  public function get_roles_tbl($name="") {
    $result = $this->global->query("SELECT
      *
      FROM tbl_roles
      WHERE
      name LIKE '%$name%' ");
    return $result->result_object();
  }

  public function get_regions_tbl($name="") {
    $result = $this->global->query("SELECT
      *
      FROM tbl_ph_regions
      WHERE
      name LIKE '%$name%' ");
    return $result->result_object();
  }

  public function count_users() {
    $result = $this->global->query("SELECT
      uid
      FROM tbl_users ");
    return $result->num_rows();
  }

  public function count_positions() {
    $result = $this->global->query("SELECT
      pid
      FROM tbl_positions ");
    return $result->num_rows();
  }

  public function count_systems() {
    $result = $this->global->query("SELECT
      sid
      FROM tbl_systems ");
    return $result->num_rows();
  }

  public function count_branches() {
    $result = $this->global->query("SELECT
      bid
      FROM tbl_branches ");
    return $result->num_rows();
  }

  // -- START HERE -- //

  public function get_branch($bid) {
    $branch = $this->global->query("select * from tbl_branches where bid = ".$bid)->row();
    if($branch->company == 4) { $branch->company = 1; }
    return $branch;
  }

  public function get_region_branches($region)
  {
    return $this->global->query("select group_concat(bid) as bid
      from tbl_branches
      where ph_region = ".$region."
      order by ph_region")->row()->bid;
  }

  public function get_company_branches($region, $bcode)
  {
    return $this->global->query("select group_concat(bid) as bid
      from tbl_branches
      where ph_region = ".$region."
      and left(b_code, 1) = '".$bcode."'
      order by ph_region")->row()->bid;
  }

  public function get_user_info($uid)
  {
    return $this->global->query("SELECT
      a.*,
      b.name,
      c.username
      FROM tbl_users_info a
      INNER JOIN tbl_positions b ON position=pid
      INNER JOIN tbl_users c ON a.uid=c.uid
      where a.uid = ".$uid)->row();
  }

  public function get_region($id)
  {
    return $this->global->query("select * from tbl_ph_regions where phrid = ".$id)->row();
  }

  public function get_company($id)
  {
    return $this->global->query("select * from tbl_companies where cid = ".$id)->row();
  }

}
