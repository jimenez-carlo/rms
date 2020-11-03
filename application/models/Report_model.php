<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model {
  function __construct() {
    parent::__construct();
    $this->load->dbutil();
    $this->load->helper('download');
  }

  public function acctg_epp($date) {
    $company = '';
    switch ($_SESSION['company']) {
      case '8':
        $company = "AND lp.company = 8";
        break;

      default:
        $company = "AND lp.company != 8";
    }

    $sql = <<<SQL
      SELECT
        lp.reference AS Reference,
        IFNULL(lp.doc_no, '') AS 'Document No',
        IFNULL(DATE_FORMAT(lp.ref_date, '%Y-%m-%d'), '') AS 'Date Entry LTO Ref',
        IFNULL(DATE_FORMAT(lp.deposit_date, '%Y-%m-%d'),'') AS 'Date Deposited',
        '' AS 'Expected Date Complete Liquidation',
        '' AS 'Days Late upon TAT', s.company_code AS Company,
        s.region AS Region, unit_count AS '# of Units', lp.amount AS 'LTO Ref Amount',
        lrsa AS 'Liquidated Registration',
        '0.00'  AS 'Liquidated Misc Expense',
        '0.00'  AS 'Liquidated Return Fund',
        flsra   AS 'For Liquidation Registration',
        '0.00'  AS 'For Liquidation Return Fund',
        ltopsra AS 'LTO Pending',
        '0.00'  AS 'Pending Amount',
        IFNULL(ad.status, '') AS 'Actual Docs Status',
        ad.transmittal_number AS 'ODMS Transmittal',
        DATE_FORMAT(ad.date_created, '%Y-%m-%d') AS 'Updated By RRT',
        DATE_FORMAT(IFNULL(ad.date_completed, ad.date_incomplete), '%Y-%m-%d') AS 'Updated By Accounting'
      FROM
        tbl_lto_payment lp
        LEFT JOIN
        tbl_actual_docs ad ON lp.lpid = ad.voucher_or_lto_payment_id AND ad.payment_method = 'EPP'
        LEFT JOIN (
          SELECT
            COUNT(DISTINCT s.sid) AS unit_count,
            SUM(IF(s.status = 5, s.registration+tip, 0)) AS lrsa,
            SUM(IF(s.status = 4, s.registration+tip, 0)) AS flsra,
            SUM(IF(s.status = 3, registration+tip, 0)) AS ltopsra,
            SUM(IF(s.status != 1, registration+tip, 0)) AS srpa,
            c.*,r.*, s.lto_payment
          FROM
            tbl_sales s
          LEFT JOIN
            tbl_region r ON r.rid = s.region
          LEFT JOIN
            tbl_company c ON c.cid = s.company
          WHERE s.company != 8
          GROUP BY c.cid, r.rid, s.lto_payment
        ) AS s ON lp.lpid = s.lto_payment
      WHERE
        DATE_FORMAT(lp.deposit_date, '%Y-%m-%d') BETWEEN '{$date['from']}' AND '{$date['to']}' {$company}
      GROUP BY
        lp.lpid, 7, 8, 9, 11, 13, 14, 16, 17, 18, 19, 20, 21
      ORDER BY lp.lpid;
SQL;

    $query_data = $this->db->query($sql);
    if (isset($query_data)) {
      $report_data = $this->dbutil->csv_from_result($query_data);
      force_download('Report_EPP_'.date('YmdHms').'.csv', $report_data, 'text/csv');
    }
  }

  public function acctg_cash($date) {
    switch ($_SESSION['company']) {
      case '8':
        $company = "AND v.company = 8";
        break;

      default:
        $company = "AND v.company != 8";
    }

    $sql = <<<SQL
      SELECT
        v.reference AS Reference,
        IFNULL(v.dm_no, '') AS 'Document No',
        IFNULL(DATE_FORMAT(v.process_date, '%Y-%m-%d'), '') AS 'Date CA Entry',
        IFNULL(DATE_FORMAT(transfer_date, '%Y-%m-%d'),'') AS 'Date Deposited',
        '' AS 'Expected Date Complete Liquidation',
        '' AS 'Days Late upon TAT', c.company_code AS Company,
        s.region AS Region, unit_count AS '# of Units', v.amount AS 'CA Amount',
        lrsa AS 'Liquidated Registration',
        IFNULL(lmxa,0) AS 'Liquidated Misc Expense', IFNULL(lrfa,0) AS 'Liquidated Return Fund',
        flsra AS 'For Liquidation Registration',
        IFNULL(flmxa,0) AS 'For Liquidation Misc Expense', IFNULL(flrfa,0) AS 'For Liquidation Return Fund',
        ltopsra AS 'LTO Pending'
        ,v.amount - IFNULL(srpa, 0) + IFNULL(lmxa, 0) + IFNULL(lrfa,0) + IFNULL(flmxa, 0) + IFNULL(flrfa, 0) AS 'Pending Amount'
        ,IFNULL(ad.status, '') AS 'Actual Docs Status',
        ad.transmittal_number AS 'ODMS Transmittal',
        DATE_FORMAT(ad.date_created, '%Y-%m-%d') AS 'Updated By RRT',
        DATE_FORMAT(IFNULL(ad.date_completed, ad.date_incomplete), '%Y-%m-%d') AS 'Updated By Accounting'
      FROM
        tbl_voucher v
      LEFT JOIN
        tbl_company c ON c.cid = v.company
      LEFT JOIN
        tbl_actual_docs ad ON v.vid = ad.voucher_or_lto_payment_id AND payment_method = 'CA'
      LEFT JOIN (
        SELECT
          COUNT(DISTINCT s.sid) AS unit_count,
          SUM(IF(s.status = 5, s.registration+tip, 0)) AS lrsa,
          SUM(IF(s.status = 4, s.registration+tip, 0)) AS flsra,
          SUM(IF(s.status = 3, registration+tip, 0)) AS ltopsra,
          SUM(IF(s.status != 1, registration+tip, 0)) AS srpa,
          r.*, s.voucher
        FROM
          tbl_sales s
        LEFT JOIN
          tbl_region r ON r.rid = s.region
        GROUP BY
          r.rid, s.voucher
      ) AS s ON v.vid = s.voucher
      LEFT JOIN (
        SELECT
          m.ca_ref, SUM(IF(mxh1.status = 4, m.amount, 0)) AS lmxa, SUM(IF(mxh1.status IN (3,6), m.amount, 0)) AS flmxa
        FROM
          tbl_misc m
        LEFT JOIN
          tbl_misc_expense_history mxh1 ON mxh1.mid = m.mid
        LEFT JOIN
          tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
        WHERE
          mxh2.id IS NULL
        GROUP BY m.ca_ref
      ) AS msc_xpns ON msc_xpns.ca_ref = v.vid
      LEFT JOIN (
        SELECT
          rf.fund, SUM(IF(rfh1.status_id = 30, rf.amount, 0)) AS lrfa, SUM(IF(rfh1.status_id = 1, rf.amount, 0)) AS flrfa
        FROM
          tbl_return_fund rf
        LEFT JOIN
          tbl_return_fund_history rfh1 ON rfh1.rfid = rf.rfid
        LEFT JOIN
          tbl_return_fund_history rfh2 ON rfh2.rfid = rfh1.rfid AND rfh1.return_fund_history_id < rfh2.return_fund_history_id
        WHERE
          rfh2.return_fund_history_id IS NULL
        GROUP BY rf.fund
      ) AS f ON f.fund = v.vid
      WHERE
        DATE_FORMAT(v.transfer_date, '%Y-%m-%d') BETWEEN '{$date['from']}' AND '{$date['to']}' {$company}
      GROUP BY
        v.vid, 7, 8, 9, 11, 13, 14, 16, 17, 18, 19, 20, 21, 22
      ORDER BY v.vid;
SQL;

    $query_data = $this->db->query($sql);
    if (isset($query_data)) {
      $report_data = $this->dbutil->csv_from_result($query_data);
      force_download('Report_CA_'.date('YmdHms').'.csv', $report_data, 'text/csv');
    }
  }
}
