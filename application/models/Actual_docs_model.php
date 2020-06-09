<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Actual_docs_model extends CI_Model {

  public function get_batch() {
    $where = 'WHERE batch_date_created >= "2020-01-01"';
    if ($_SESSION['dept_name'] === 'Accounting') {
      $where .= ' AND transmittal_number IS NOT NULL ';
    }
    $sql = <<<SQL
      SELECT
        actual_docs_id, transmittal_number, transmittal_id, id, reference,
        date_deposited, FORMAT(amount, 2) AS amount, payment_type, region,
        company_code AS company, deposit_slip, date_incomplete, date_completed,
        batch_date_created, status
      FROM (
        SELECT
          ad.transmittal_number, actual_docs_id,
          DATE_FORMAT(date_incomplete, '%Y-%m-%d') AS date_incomplete,
          DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed,
          CONCAT(v.vid,LOWER(REPLACE(v.reference,'-',''))) AS transmittal_id,
          v.vid AS id, v.reference, DATE_FORMAT(v.transfer_date, '%Y-%m-%d') AS date_deposited,
          v.company AS cid, f.region AS rid, v.amount, 'CA' AS payment_type
          ,IFNULL(ad.deposit_slip, 'select') AS deposit_slip
          ,v.date AS batch_date_created, ad.status
        FROM
          tbl_voucher v
        LEFT JOIN
          tbl_actual_docs ad
            ON ad.voucher_or_lto_payment_id = v.vid AND payment_method = "CA"
        INNER JOIN
          tbl_fund f ON f.fid = v.fund
        WHERE
          v.transfer_date IS NOT NULL
        UNION
        SELECT
          transmittal_number, actual_docs_id,
          DATE_FORMAT(date_incomplete, '%Y-%m-%d') AS date_incomplete,
          DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed,
          CONCAT(lp.lpid,LOWER(REPLACE(lp.reference,'-',''))) AS transmittal_id,
          lp.lpid AS id, lp.reference, DATE_FORMAT(lp.deposit_date, '%Y-%m-%d') AS date_deposited,
          lp.company AS cid, lp.region AS rid, lp.amount, 'EPP' AS payment_type
          ,IFNULL(ad.deposit_slip, 'select') AS deposit_slip
          ,lp.created AS batch_date_created ,ad.status
        FROM
          tbl_lto_payment lp
        LEFT JOIN
          tbl_actual_docs ad
            ON ad.voucher_or_lto_payment_id = lp.lpid AND payment_method = "EPP"
        WHERE
          lp.deposit_date IS NOT NULL
      ) AS batch
      JOIN
        tbl_region USING(rid)
      JOIN
        tbl_company USING(cid)
      {$where}
SQL;
    //echo '<pre>'; var_dump($result); echo '</pre>'; die();
    $result = $this->db->query($sql)->result_array();
    return $result;
  }

  public function save($new_transmittal) {
    $this->db->trans_start();
    $this->db->insert('tbl_actual_docs', $new_transmittal);
    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function update($actual_docs) {
    //echo '<pre>'; var_dump($actual_docs); echo '</pre>'; die();
    switch ($actual_docs['deposit_slip']) {
      case 'Not Original':
        $actual_docs['date_incomplete'] = date('Y-m-d H:i:s');
        $actual_docs['status'] = 'Incomplete';
        break;
      case 'Original':
        $actual_docs['status'] = 'Complete';
        $actual_docs['date_completed'] = date('Y-m-d H:i:s');
        break;
    }

    $this->db->update('tbl_actual_docs', $actual_docs, ['actual_docs_id' => $actual_docs["actual_docs_id"]]);

    $this->db->select("
      actual_docs_id, transmittal_number,
      DATE_FORMAT(date_incomplete, '%Y-%m-%d') AS date_incomplete,
      DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed, status
    ");
    return $this->db->get_where('tbl_actual_docs', ['actual_docs_id' => $actual_docs["actual_docs_id"]])->row_array();
  }

}
