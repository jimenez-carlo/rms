<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Actual_docs_model extends CI_Model {

  public function get_batch($param) {

    $w_company_1 = (isset($param['company']) && $param['company'] !== 'any') ? 'AND v.company = '.$param['company'] : '';
    $w_company_2 = (isset($param['company']) && $param['company'] !== 'any') ? 'AND lp.company = '.$param['company'] : '';

    $w_status = '';
    if (isset($param['status'])) {
      switch ($param['status']) {
        case 'Pending':
        case 'Incomplete':
        case 'Resend':
        case 'Complete':
          $w_status =  'AND ad.status = "'.$param['status'].'"';
          break;
        case 'New':
          $w_status =  'AND ad.status IS NULL';
          break;
        default:
          $w_status = '';
          break;
      }
    }

    $company = ($_SESSION['company'] === '8') ? 'AND cid = 8' : 'AND cid != 8';
    $where = 'WHERE batch_date_created >= "2020-01-01" '.$company;
    switch ($_SESSION['position_name']) {
      case 'Accounts Payable Clerk':
      case 'RRT National Registration Manager':
        $w_region = (isset($param['region']) && $param['region'] !== 'any') ? 'AND region = '.$param['region'] : '';
        break;
      case 'RRT General Clerk':
      case 'RRT Branch Secretary':
        $w_region = 'AND region = '.$_SESSION['region_id'];
        break;
    }

    $sql = <<<SQL
      SELECT
        actual_docs_id, transmittal_number, transmittal_id, id, reference,
        date_deposited, FORMAT(amount, 2) AS amount, payment_type, region,
        company_code AS company, deposit_slip, disable_deposit_slip,
        date_incomplete, date_completed, batch_date_created, status
      FROM (
        (SELECT
          ad.transmittal_number, actual_docs_id,
          DATE_FORMAT(date_incomplete, '%Y-%m-%d') AS date_incomplete,
          DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed,
          CONCAT(v.vid,LOWER(REPLACE(v.reference,'-',''))) AS transmittal_id,
          v.vid AS id, v.reference, DATE_FORMAT(v.transfer_date, '%Y-%m-%d') AS date_deposited,
          v.company AS cid, f.region AS rid, v.amount, 'CA' AS payment_type
          ,ad.deposit_slip, DATE_ADD(IFNULL(ad.date_completed, NOW()), INTERVAL 24 HOUR) < NOW() AS disable_deposit_slip
          ,v.date AS batch_date_created, IFNULL(ad.status, 'New') AS status
        FROM
          tbl_voucher v
        LEFT JOIN
          tbl_actual_docs ad
            ON ad.voucher_or_lto_payment_id = v.vid AND payment_method = "CA"
        INNER JOIN
          tbl_fund f ON f.fid = v.fund
        WHERE
          v.transfer_date IS NOT NULL {$w_region} {$w_company_1} ${w_status}
        ORDER BY transfer_date DESC LIMIT 500)
        UNION
        (SELECT
          transmittal_number, actual_docs_id,
          DATE_FORMAT(date_incomplete, '%Y-%m-%d') AS date_incomplete,
          DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed,
          CONCAT(lp.lpid,LOWER(REPLACE(lp.reference,'-',''))) AS transmittal_id,
          lp.lpid AS id, lp.reference, DATE_FORMAT(lp.deposit_date, '%Y-%m-%d') AS date_deposited,
          lp.company AS cid, lp.region AS rid, lp.amount, 'EPP' AS payment_type
          ,ad.deposit_slip, DATE_ADD(IFNULL(ad.date_completed, NOW()), INTERVAL 24 HOUR) < NOW() AS disable_deposit_slip
          ,lp.created AS batch_date_created, IFNULL(ad.status, 'New') AS status
        FROM
          tbl_lto_payment lp
        LEFT JOIN
          tbl_actual_docs ad
            ON ad.voucher_or_lto_payment_id = lp.lpid AND payment_method = "EPP"
        WHERE
          lp.deposit_date IS NOT NULL {$w_region} {$w_company_2} {$w_status}
        ORDER BY deposit_date DESC LIMIT 500)
      ) AS batch
      JOIN
        tbl_region USING(rid)
      JOIN
        tbl_company USING(cid)
      {$where}
      ORDER BY date_deposited DESC, region ASC, company ASC
SQL;
    $result = $this->db->query($sql)->result_array();
    return $result;
  }

  public function save($transmittal) {
    $this->db->trans_start();
    if (strlen($transmittal['actual_docs_id']) === 0) { // new actual_docs
      $transmittal['status'] = 'Pending';
      $this->db->insert('tbl_actual_docs', $transmittal);
      $actual_docs_id = $this->db->insert_id();
    } else { // resend actual docs
      $this->db->select('status');
      $check_status = $this->db->get_where('tbl_actual_docs', ['actual_docs_id' => $transmittal['actual_docs_id']])->row('status');

      if ($check_status !== 'Complete') {
        $this->db->query("
        UPDATE
          tbl_actual_docs
        SET
          transmittal_number = CONCAT(transmittal_number, ' | ', {$transmittal["transmittal_number"]}),
          status = 'Resend'
        WHERE
          actual_docs_id = {$transmittal["actual_docs_id"]}
        ");
        $actual_docs_id = $transmittal["actual_docs_id"];
      }
    }
    $this->db->trans_complete();
    $return = array(
      'data' => $this->db->get_where('tbl_actual_docs', "actual_docs_id = {$actual_docs_id}")->row_array()
    );

    $return['status'] = (isset($check_status) && $check_status === 'Complete') ?  false  : $this->db->trans_status();
    return $return;
  }

  public function update($actual_docs) {
    switch ($actual_docs['deposit_slip']) {
      case 'Not Original':
        $actual_docs['date_incomplete'] = date('Y-m-d H:i:s');
        $actual_docs['status'] = 'Incomplete';
        $actual_docs['date_completed'] = NULL;
        break;
      case 'Original':
        $actual_docs['date_completed'] = date('Y-m-d H:i:s');
        $actual_docs['status'] = 'Complete';
        break;
    }

    $this->db->update('tbl_actual_docs', $actual_docs, ['actual_docs_id' => $actual_docs["actual_docs_id"]]);

    $this->db->select("
      actual_docs_id, deposit_slip, transmittal_number,
      DATE_FORMAT(date_incomplete, '%Y-%m-%d') AS date_incomplete,
      DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed, status
    ");
    return $this->db->get_where('tbl_actual_docs', ['actual_docs_id' => $actual_docs["actual_docs_id"]])->row_array();
  }

}
