<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Repo_Return_Fund_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
    if ($_SESSION['company'] != 8) {
      $this->companyQry = 'c.cid != 8';
    } else {
      $this->companyQry = 'c.cid = 8';
    }
  }

  public function load_list($param)
  {
    $company = (empty($param->company)) ? "" : "AND v.company_id = " . $param->company;
    $region = (empty($param->region)) ? "" : "AND v.region_id = " . $param->region;
    $status = (empty($param->status)) ? "" : "AND rf.status_id = " . $param->status;
    $reference = (empty($param->reference)) ? "" : " AND v.reference LIKE '%" . $param->reference . "%'";
    $rrt = ($_SESSION['dept_name'] === 'Regional Registration') ? "AND r.rid = {$_SESSION['region_id']}" : "";

    switch ($this->session->position) {
      case 72:
      case 73:
      case 81: // if ccn, set branch
        $condition = "AND rf.status_id in (3,4,5) AND v.bcode = " . $this->session->branch_code; // 2 wrong company
        // $region    = "AND v.region_id = " . $_SESSION['region_id'];
        $company   = "AND v.company_id = " . $_SESSION['company'];
        break;
      default:
        $condition = "";
        break;
    }
    $return_fund_list = $this->db->query("SELECT
                    rf.id as return_fund_id, DATE_FORMAT(rf.date_created, '%Y-%m-%d') AS created,
                    v.reference, c.company_code AS companyname,
                    r.region, rf.amount, rf.image_path,
                    DATE_FORMAT(rf.liquidated_date, '%Y-%m-%d') AS liq_date,
                    st.status_id, st.status_name AS status
                  FROM
                    tbl_repo_return_fund rf
                  INNER JOIN
                    tbl_status st ON st.status_id = rf.status_id AND st.status_type = 'RETURN_FUND'
                  INNER JOIN
                    tbl_repo_batch v ON v.repo_batch_id = rf.repo_batch_id
                  INNER JOIN
                    tbl_region r ON v.region_id = r.rid 
                  INNER JOIN
                    tbl_company c ON v.company_id = c.cid  
                  WHERE
                    rf.is_deleted = 0
                    {$condition}
                    AND {$this->companyQry} {$company} {$region} {$status} {$reference}
                    AND rf.date_created BETWEEN '{$param->date_from} 00:00:00' AND '{$param->date_to} 23:59:59'
                  ORDER BY rf.date_created DESC LIMIT 1000
                ")->result_object();
    return $return_fund_list;
  }
}
