<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Lto_pending_model extends CI_Model{

  public $cust_type = array(
    0 => 'Individual',
    1 => 'Organizational'
  );

  public $status = array(
    0 => 'New',
    1 => 'Incomplete',
    2 => 'Done'
  );

  public function __construct()
  {
    parent::__construct();
  }

  public function load_list($region)
  {
    $result = $this->db->query("
      SELECT
          t.*, LEFT(t.date, 10) AS date, COUNT(*) AS sales
          ,c.company_code AS company
      FROM
          tbl_lto_transmittal t
      INNER JOIN
          tbl_sales s ON lto_transmittal = ltid
      INNER JOIN
          tbl_company c ON c.cid = s.company
      WHERE
          t.region = ".$region." AND registration_type != 'Self Registration'
          AND status < 2 AND voucher = 0 AND electronic_payment = 0
      GROUP BY ltid, c.cid
      ORDER BY t.date DESC
    ")->result_object();

    return $result;
  }

  public function load_transmittal($ltid)
  {
    $transmittal = $this->db->query("SELECT * FROM tbl_lto_transmittal WHERE ltid = ".$ltid)->row();
    $transmittal->sales = $this->db->query("
      SELECT
        sid, bcode, bname, region, company,
        DATE_FORMAT(date_sold, '%Y-%m-%d') AS date_sold,
        registration_type, status, acct_status,
        DATE_FORMAT(transmittal_date, '%Y-%m-%d') AS transmittal_date,
        lto_transmittal, lto_reason, engine_no,
        first_name, middle_name, last_name
      FROM tbl_sales
      INNER JOIN tbl_engine on engine = eid
      INNER JOIN tbl_customer on customer = cid
      WHERE lto_transmittal = ".$ltid."
      AND registration_type != 'Self Registration'
      AND status < 2
      ORDER BY bcode DESC
    ")->result_object();
    return $transmittal;
  }
}
