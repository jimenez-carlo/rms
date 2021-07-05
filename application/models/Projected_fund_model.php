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

    $this->table->set_template(["table_open" => "<table class='table'>"]);
    $regn_exp_ttl  = "rr.orcr_amt+rr.renewal_amt+rr.transfer_amt+rr.hpg_pnp_clearance_amt";
    $regn_exp_ttl .= "+rr.insurance_amt+rr.emission_amt+rr.macro_etching_amt+rr.renewal_tip";
    $regn_exp_ttl .= "+rr.transfer_tip+rr.hpg_pnp_clearance_tip+rr.macro_etching_tip+rr.plate_tip";
    $url_batch_view = base_url('repo/batch_view/');


    $additional = "";
    if ($_SESSION['position'] != 34) {
      // $additional = "IFNULL(rb.amount - SUM({$regn_exp_ttl}), rb.amount) AS 'LTO Pending',
      //                 SUM(IF(ri.status = 'REGISTERED',({$regn_exp_ttl}),0)) AS 'For Checking',
      //                 SUM(IF(ri.status = 'DISAPPROVED',({$regn_exp_ttl}),0)) AS 'Disapproved',
      //                 0 AS 'SAP Uploading',
      //                 SUM(IF(ri.status = 'LIQUIDATED',({$regn_exp_ttl}),0)) AS 'Liquidated',";

       // 'Registration: ', '<span style=float:right>', FORMAT(IFNULL({$regn_exp_ttl}, 0),2), '</span>',
       //                '<br>Miscellaneous: ', '<span style=float:right>', FORMAT(IFNULL(misc_exp_for_checking,0),2), '</span>',
       //                '<br>Return Fund: ', '<span style=float:right>', FORMAT(IFNULL(return_fund_for_checking, 0),2), '</span>'

      $additional = " 
                      IFNULL(rb.amount - SUM({$regn_exp_ttl}), rb.amount) AS 'LTO Pending',
                     CONCAT(
                      'Registration: ', '<span style=float:right>', FORMAT(IFNULL(SUM(rr.orcr_amt+rr.renewal_amt+rr.transfer_amt), 0),2), '</span>',
                      '<br>Miscellaneous: ', '<span style=float:right>', FORMAT(IFNULL(misc_exp_for_checking,0),2), '</span>',
                      '<br>Return Fund: ', '<span style=float:right>', FORMAT(IFNULL(return_fund_for_checking, 0),2), '</span>'
                    ) AS 'For Checking',
                      SUM(IF(ri.status = 'DISAPPROVED',({$regn_exp_ttl}),0)) AS 'Disapproved',
                      0 AS 'SAP Uploading',
                      CONCAT(
                        'Registration: ', '<span style=float:right>', FORMAT(IFNULL(SUM(rr.orcr_amt+rr.renewal_amt+rr.transfer_amt), 0),2), '</span>',
                        '<br>Miscellaneous: ', '<span style=float:right>', FORMAT(IFNULL(misc_exp_for_checking,0),2), '</span>',
                        '<br>Return Fund: ', '<span style=float:right>', SUM(IF(ri.status = 'LIQUIDATED',({$regn_exp_ttl}),0)), '</span>'
                      )
                      AS 'Liquidated',
                    
";
    }

    $sql    = <<<SQL
    SELECT
        CONCAT('<a href=\"{$url_batch_view}',rb.repo_batch_id,'\" target=\"_blank\">',rb.reference,'</a>') AS 'Reference #',
        rb.doc_no AS 'Document #',
        rb.debit_memo AS 'Debit Memo',
        CONCAT(DATE_FORMAT(rb.date_created, '%Y-%m-%d'), ' / ',rb.date_deposited) AS 'Entry Date / Date Deposited',
        CONCAT(r.region, ' / ', rb.bcode, ' ', rb.bname) AS 'Region / Branch',
        COUNT(*) AS '# of Units',
        FORMAT(rb.amount, 2) AS 'Amount',
        $additional
        rb.status AS 'Status'
        FROM tbl_repo_batch rb
        INNER JOIN tbl_repo_sales rs ON rb.repo_batch_id = rs.repo_batch_id
        INNER JOIN tbl_repo_inventory ri ON ri.repo_inventory_id = rs.repo_inventory_id
        LEFT JOIN tbl_repo_registration rr ON rr.repo_registration_id = rs.repo_registration_id
        LEFT JOIN tbl_region r ON r.rid = rb.region_id
        LEFT JOIN (
                    SELECT
                      m.ca_ref,
                      IFNULL(SUM(IF(st.status_name = 'For Approval', m.amount ,0)), 0) AS misc_exp_for_checking,
                      IFNULL(SUM(IF(st.status_name = 'For Liquidation', m.amount ,0)), 0) AS misc_exp_sap_upload,
                      IFNULL(SUM(IF(st.status_name = 'Liquidated', m.amount ,0)), 0) AS misc_exp_liquidated,
                      IFNULL(SUM(IF(st.status_name = 'Disapproved', m.amount ,0)), 0) AS misc_exp_da
                    FROM tbl_repo_misc m
                    INNER JOIN tbl_repo_misc_expense_history mxh1 ON m.mid = mxh1.mid
                    INNER JOIN tbl_status st ON st.status_id = mxh1.status AND st.status_type = 'MISC_EXP'
                    LEFT JOIN tbl_repo_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
                    WHERE mxh2.id IS NULL GROUP BY m.ca_ref
                  ) AS misc ON misc.ca_ref = rb.repo_batch_id 
        LEFT JOIN (
                    SELECT
                      x.repo_batch_id,
                      IFNULL(SUM(IF(y.status_id = 1, x.amount ,0)), 0) AS return_fund_for_checking,
                      IFNULL(SUM(IF(y.status_id = 30, x.amount ,0)), 0) AS return_fund_liquidated,
                      IFNULL(SUM(IF(y.status_id NOT IN(1,30,90), x.amount ,0)), 0) AS return_fund_da
                  FROM tbl_repo_return_fund x
                  INNER JOIN tbl_status y ON x.status_id = y.status_id AND y.status_type = 'RETURN_FUND'
                  GROUP BY x.repo_batch_id
                  ) AS return_fund ON return_fund.repo_batch_id = rb.repo_batch_id 

        WHERE rb.date_created BETWEEN '{$param->date_from} 00:00:00' AND '{$param->date_to} 23:59:59' {$region} {$status}
        GROUP BY rb.repo_batch_id
        LIMIT 1000 

SQL;
// -- is_deleted = 0
// (
//   SELECT
//     rf.repo_batch_id,
//     IFNULL(SUM(IF(st.status_name = 'For Liquidation', rf.amount ,0)), 0) AS return_fund_for_checking,
//     IFNULL(SUM(IF(st.status_name = 'Liquidated', rf.amount ,0)), 0) AS return_fund_liquidated,
//     IFNULL(SUM(IF(st.status_name NOT IN('For Liquidation', 'Liquidated', 'Deleted'), rf.amount ,0)), 0) AS return_fund_da
//   FROM tbl_repo_return_fund rf
//   LEFT JOIN tbl_status st ON rf.status_id = st.status_id AND status_type = 'RETURN_FUND'
//   WHERE rfh_2.return_fund_history_id IS NULL
//   GROUP BY rf.repo_batch_id
// ) AS return_fund ON return_fund.repo_batch_id = rb.repo_batch_id 

    $result = $this->db->query($sql);
    // $result = $this->db
    //   ->select("
    //     CONCAT('<a href=\"{$url_batch_view}',rb.repo_batch_id,'\" target=\"_blank\">',rb.reference,'</a>') AS 'Reference #',
    //     rb.doc_no AS 'Document #',
    //     rb.debit_memo AS 'Debit Memo',
    //     CONCAT(DATE_FORMAT(rb.date_created, '%Y-%m-%d'), ' / ',rb.date_deposited) AS 'Entry Date / Date Deposited',
    //     CONCAT(r.region, ' / ', rb.bcode, ' ', rb.bname) AS 'Region / Branch',
    //     COUNT(*) AS '# of Units',
    //     FORMAT(rb.amount, 2) AS 'Amount',
    //     IFNULL(rb.amount - SUM({$regn_exp_ttl}), rb.amount) AS 'LTO Pending',
    //     SUM(IF(ri.status = 'REGISTERED',({$regn_exp_ttl}),0)) AS 'For Checking',
    //     SUM(IF(ri.status = 'DISAPPROVED',({$regn_exp_ttl}),0)) AS 'Disapproved',
    //     0 AS 'SAP Uploading',
    //     SUM(IF(ri.status = 'LIQUIDATED',({$regn_exp_ttl}),0)) AS 'Liquidated',
    //     rb.status AS 'Status'
    //   ")
    //   ->from('tbl_repo_batch rb')
    //   ->join('tbl_repo_sales rs', 'rb.repo_batch_id = rs.repo_batch_id', 'inner')
    //   ->join('tbl_repo_inventory ri', 'ri.repo_inventory_id = rs.repo_inventory_id', 'inner')
    //   ->join('tbl_repo_registration rr', 'rr.repo_registration_id = rs.repo_registration_id', 'left')
    //   ->join('tbl_region r', 'r.rid = rb.region_id', 'left')
    //   ->where("rb.date_created BETWEEN '{$param->date_from} 00:00:00' AND '{$param->date_to} 23:59:59' {$region} {$status}")
    //   ->group_by('rb.repo_batch_id')
    //   ->limit(1000)
    //   ->get();
    return $this->table->generate($result);
  }


}
