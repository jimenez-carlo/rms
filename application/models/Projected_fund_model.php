<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Projected_fund_model extends CI_Model{

  public $status = array(
    0 => 'For Treasury Process',
    1 => 'For Deposit',
    2 => 'Deposited',
    3 => 'Liquidated',
  );

  public $sales_type = array(
    0 => 'Brand New (Cash)',
    1 => 'Brand New (Installment)'
  );

  public function __construct()
  {
    parent::__construct();
    if ($_SESSION['company'] == 8) {
      $this->region  = $this->mdi_region;
      $this->company = $this->mdi;
    }
  }

  /**
   * View RRT Funds with Projected Funds
   */
  public function get_projected_funds() {
    switch ($this->session->company_code) {
      case 'CMC':
          $company = 'AND f.company != 8';
        break;
      case 'MDI':
          $company = 'AND f.company = 8';
        break;
    }

    $query = <<<SQL
      SELECT
        IF(c.company_code IN ('HPTI', 'MTI'),'',r.region) AS Region,
        c.company_code AS Company,
        IF(c.company_code IN ('HPTI', 'MTI'),'-',FORMAT(f.fund, 2)) AS 'Cash In Bank',
        IF(c.company_code IN ('HPTI', 'MTI'),'-',FORMAT(f.cash_on_hand, 2)) AS 'Cash On Hand',
        FORMAT(SUM(IF(v.voucher_no IS NULL,v.amount,0)), 2) AS 'For CA',
        FORMAT(SUM(IF(v.voucher_no IS NOT NULL AND v.dm_no IS NULL,v.amount,0)), 2) AS 'For Deposit',
        CONCAT(
          '<button class="btn btn-success" onclick="create_voucher(',c.cid,' ,',r.rid,')"',
          IF(SUM(IF(v.voucher_no IS NULL,v.amount,0))=0 , ' disabled',''),
          '>Create CA</button>'
        ) AS ''
      FROM tbl_fund f
      LEFT JOIN tbl_voucher v ON v.fund = f.fid
      LEFT JOIN tbl_region r ON f.region = r.rid
      LEFT JOIN tbl_company c ON c.cid = v.company
      WHERE 1=1 {$company}
      GROUP BY f.fid, r.rid, c.cid
      ORDER BY r.rid ASC, c.cid ASC
SQL;
    $result = $this->db->query($query);
    $this->table->set_template([ "table_open" => "<table class='table'>" ]);
    return $this->table->generate($result);
  }

  /**
   * Accounting to Create Voucher
   */
  public function create_voucher($company_id, $region_id) {
    $result = $this->db->query("
      SELECT
        v.reference AS 'Reference#', r.region AS Region,
        c.company_code AS Company ,DATE_FORMAT(v.date, '%Y-%m-%d') AS 'CA Date'
        ,FORMAT(v.amount, 2) AS Amount, COUNT(*) AS '# of Units',
        CONCAT('<input id=\"voucher-',v.vid,'\" data-id=\"',v.vid,'\" type=\"text\" name=\"voucher[voucher_no]\" value=\"\">') AS 'Document#',
        CONCAT('
          <a class=\"btn btn-primary\" href=\"".base_url('projected_fund/sprint')."/',v.vid,'\" target=\"_blank\">Print</a>
          <button id=\"button-',v.vid,'\" class=\"btn btn-success save-voucher\" type=\"button\" name=\"voucher[vid]\" value=\"',v.vid,'\" disabled>Save</button>
        ') AS ''
      FROM tbl_voucher v
      INNER JOIN tbl_sales s ON s.voucher = v.vid
      INNER JOIN tbl_region r ON r.rid = s.region
      INNER JOIN tbl_company c ON c.cid = s.company
      WHERE
        s.region = ".$region_id." AND s.company = '".$company_id."'
        AND v.voucher_no IS NULL
      GROUP BY v.vid, r.rid, c.cid
    ");

    $this->table->set_template([ "table_open" => "<table class='table projected'>" ]);
    return $this->table->generate($result);
  }

  public function print_projected($vid)
  {
     $ca = $this->db->query("
       SELECT
         v.reference, r.region,
         c.company_code AS company
       FROM tbl_voucher v
       INNER JOIN tbl_fund f ON f.fid = v.fund
       INNER JOIN tbl_region r ON r.rid = f.region
       INNER JOIN tbl_company c ON c.cid = v.company
       WHERE v.vid = {$vid}
     ")->row();

     $ca->sales = $this->db->query("
       SELECT s.bcode, s.bname, count(*) as units
       FROM tbl_sales s
       INNER JOIN tbl_engine e on e.eid = s.engine
       INNER JOIN tbl_customer c on c.cid = s.customer
       WHERE s.voucher = {$vid}
       GROUP BY s.bcode, s.bname
     ")->result_object();

     return $ca;
  }

  public function save_voucher($voucher) {
    return $this->db->update('tbl_voucher', ['voucher_no' => $voucher['voucher_no']], 'vid='.$voucher['vid']);
  }

  /**
   * Accounting to view list of Voucher
   */
  public function list_voucher($param) {
    $status = (is_numeric($param->status)) ? ' AND status = '.$param->status : '';
    $region = (!in_array($param->region, ['_any', NULL])) ? ' AND region = '.$param->region : '';

    $company = ($_SESSION['company'] != 8) ? ' AND region < 11' : ' AND region >= 11';

    $result = $this->db->query("
      SELECT * FROM tbl_voucher v
      INNER JOIN tbl_fund on fid = v.fund
      WHERE date between '".$param->date_from." 00:00:00'
      AND '".$param->date_to." 23:59:59'
      ".$status.$region.$company)->result_object();
    foreach ($result as $key => $row)
    {
      $row->date = substr($row->date, 0, 10);
      $row->transfer_date = substr($row->transfer_date, 0, 10);
      $row->region  = $this->region[$row->region];
      $row->company = $this->company[$row->company];
      $row->status  = $this->status[$row->status];
      $result[$key] = $row;
    }
    return $result;
  }

  public function repo_ca_list($param) {
    $region = "";
    $status = "";
    if (!in_array($param->region,[NULL,"0"])) {
      $region = "AND r.rid = {$param->region}";
    }
    if (!in_array($param->status,[NULL,"0"])) {
      $status = "AND rb.status = '{$param->status}'";
    }

    return $this->db
      ->select("
        rb.reference, rb.doc_no, DATE_FORMAT(rb.date_created, '%Y-%m-%d') AS entry_date,
        rb.debit_memo, rb.date_deposited, (rb.amount + rb.bank_amount) AS amount,
        rb.status, r.rid, r.region
      ")
      ->from('tbl_repo_batch rb')
      ->join('tbl_region r', 'r.rid = rb.region_id', 'left')
      ->where("rb.date_created BETWEEN '{$param->date_from} 00:00:00' AND '{$param->date_to} 23:59:59' {$region} {$status}")
      ->limit(1000)
      ->get()
      ->result_object();
  }

}
