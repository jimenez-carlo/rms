<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Actual_docs_model extends CI_Model {

  public function get_batch() {
    $sql = <<<SQL
      SELECT
        "" AS transmittal_number,
        CONCAT(v.vid,LOWER(REPLACE(v.reference,'-',''))) AS transmittal_id,
        v.vid AS id, v.reference, DATE_FORMAT(v.transfer_date, '%Y-%m-%d') AS date_deposited,
        c.company_code AS company, r.region, v.amount, 'CA' AS type
      FROM
        tbl_voucher v
      INNER JOIN
        tbl_company c ON c.cid = v.company
      INNER JOIN
        tbl_fund f ON f.fid = v.fund
      INNER JOIN
        tbl_region r ON r.rid = f.region
      WHERE v.transfer_date IS NOT NULL
      UNION
      SELECT
        "" AS transmittal_number,
        CONCAT(lp.lpid,LOWER(REPLACE(lp.reference,'-',''))) AS transmittal_id,
        lp.lpid AS id, lp.reference, DATE_FORMAT(lp.deposit_date, '%Y-%m-%d') AS date_deposited,
        c.company_code AS company, r.region, lp.amount, 'LTO_PAYMENT' AS type
      FROM
        tbl_lto_payment lp
      INNER JOIN
        tbl_company c ON c.cid = lp.company
      INNER JOIN
        tbl_region r ON r.rid = lp.region
      WHERE lp.deposit_date IS NOT NULL
SQL;
    //echo '<pre>'; var_dump($sql); echo '</pre>'; die();
    $result = $this->db->query($sql)->result_array();
    //echo '<pre>'; var_dump($result); echo '</pre>'; die();
    return $result;
  }

}
