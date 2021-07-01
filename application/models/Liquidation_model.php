<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Liquidation_model extends CI_Model{

        public $status = array(
                0 => 'For Transmittal',
                1 => 'LTO Rejected',
                2 => 'LTO Pending',
                3 => 'LTO Pending',
                4 => 'Registered',
                5 => 'Liquidated',
        );

        public function __construct()
        {
                parent::__construct();
                if ($_SESSION['company'] != 8) {
                  $this->companyQry = ' AND v.company != 8';
                } else {
                  $this->region = $this->mdi_region;
                  $this->companyQry = ' AND v.company = 8';
                }
        }

        public function load_list($param)
        {
                $date_from = (empty($param->date_from)) ? date('Y-m-d', strtotime('-3 days')) : $param->date_from;
                $date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
                $region = (is_numeric($param->region)) ? ' AND f.region = '.$param->region : '';

                $sql = <<<SQL
                  SELECT
                    CONCAT('<a class="vid" data-vid="',v.vid,'" href="#">',v.reference,'</a>') AS 'Reference #',
                    v.dm_no AS 'Document #',
                    DATE_FORMAT(v.transfer_date, '%Y-%m-%d') AS 'Date Deposited',
                    CONCAT(c.company_code, ' ',r.region) AS 'Company/Region',
                    FORMAT(sales_count,0) AS '# of Units',
                    FORMAT(v.amount,2) AS 'CA Amount',
                    FORMAT(
                      v.amount - (
                        IFNULL(sales_for_checking,0) + IFNULL(misc_exp_for_checking,0) +
                        IFNULL(return_fund_for_checking,0) + IFNULL(sales_sap_upload,0) +
                        IFNULL(misc_exp_sap_upload,0) + IFNULL(sales_liquidated,0) +
                        IFNULL(misc_exp_liquidated,0) + IFNULL(return_fund_liquidated,0) +
                        IFNULL(lto_pending,0)
                      ), 2
                    ) AS 'Pending Amount',
                    FORMAT(lto_pending, 2) AS 'LTO Pending',
                    CONCAT(
                      'Registration: ', '<span style="float:right">', FORMAT(IFNULL(sales_for_checking, 0),2), '</span>',
                      '<br>Miscellaneous: ', '<span style="float:right">', FORMAT(IFNULL(misc_exp_for_checking,0),2), '</span>',
                      '<br>Return Fund: ', '<span style="float:right">', FORMAT(IFNULL(return_fund_for_checking, 0),2), '</span>'
                    ) AS 'For Checking',
                    CONCAT(
                      'Registration: ', '<span style="float:right">', FORMAT(IFNULL(sales_da, 0),2), '</span>',
                      '<br>Miscellaneous: ', '<span style="float:right">', FORMAT(IFNULL(misc_exp_da,0),2), '</span>',
                      '<br>Return Fund: ', '<span style="float:right">', FORMAT(IFNULL(return_fund_da, 0),2), '</span>'
                    ) AS 'Disapproved',
                    CONCAT(
                      'Registration: ', '<span style="float:right">', FORMAT(IFNULL(sales_sap_upload, 0),2), '</span>',
                      '<br>Miscellaneous: ', '<span style="float:right">', FORMAT(IFNULL(misc_exp_sap_upload, 0),2), '</span>'
                    ) AS 'SAP Uploading',
                    CONCAT(
                      'Registration: ', '<span style="float:right">', FORMAT(IFNULL(sales_liquidated, 0),2), '</span>',
                      '<br>Miscellaneous: ', '<span style="float:right">', FORMAT(IFNULL(misc_exp_liquidated,0),2), '</span>',
                      '<br>Return Fund: ', '<span style="float:right">', FORMAT(IFNULL(return_fund_liquidated, 0),2), '</span>'
                    ) AS 'Liquidated'
                  FROM tbl_voucher v
                  INNER JOIN tbl_fund f ON fid = v.fund
                  INNER JOIN (
                    SELECT
                      s.voucher,
                      COUNT(*) AS sales_count,
                      IFNULL(SUM(IF(st.status_id < 3, 900, 0)), 0) AS rrt_pending,
                      IFNULL(SUM(IF(st.status_name = 'NRU Paid' , s.registration+s.penalty+s.tip, 0)), 0) AS lto_pending,
                      IFNULL(SUM(IF(st.status_name = 'Registered' AND susb.sid IS NULL AND s.da_reason NOT IN (0, 11), s.registration+s.penalty+s.tip, 0)), 0) AS sales_da,
                      IFNULL(SUM(IF(st.status_name = 'Registered' AND susb.sid IS NULL AND s.da_reason IN (0, 11), s.registration+s.penalty+s.tip, 0)), 0) AS sales_for_checking,
                      IFNULL(SUM(IF(st.status_name = 'Registered' AND susb.sid IS NOT NULL, s.registration+s.penalty+s.tip, 0)), 0) AS sales_sap_upload,
                      IFNULL(SUM(IF(st.status_name = 'Liquidated', s.registration+s.penalty+s.tip, 0)), 0) AS sales_liquidated
                    FROM tbl_sales s
                    LEFT JOIN tbl_sap_upload_sales_batch susb ON susb.sid = s.sid
                    INNER JOIN tbl_status st ON st.status_id = s.status AND st.status_type = 'SALES'
                    GROUP BY s.voucher
                  ) AS sales ON sales.voucher = v.vid
                  LEFT JOIN (
                    SELECT
                      m.ca_ref,
                      IFNULL(SUM(IF(st.status_name IN ('Approved', 'Resolved'), m.amount ,0)), 0) AS misc_exp_for_checking,
                      IFNULL(SUM(IF(st.status_name = 'For Liquidation', m.amount ,0)), 0) AS misc_exp_sap_upload,
                      IFNULL(SUM(IF(st.status_name = 'Liquidated', m.amount ,0)), 0) AS misc_exp_liquidated,
                      IFNULL(SUM(IF(st.status_name = 'Disapproved', m.amount ,0)), 0) AS misc_exp_da
                    FROM tbl_misc m
                    INNER JOIN tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
                    INNER JOIN tbl_status st ON st.status_id = mxh1.status AND st.status_type = 'MISC_EXP'
                    LEFT JOIN tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
                    WHERE mxh2.id IS NULL GROUP BY m.ca_ref
                  ) AS misc ON misc.ca_ref = v.vid
                  LEFT JOIN (
                    SELECT
                      rf.fund,
                      IFNULL(SUM(IF(st.status_name = 'For Liquidation', rf.amount ,0)), 0) AS return_fund_for_checking,
                      IFNULL(SUM(IF(st.status_name = 'Liquidated', rf.amount ,0)), 0) AS return_fund_liquidated,
                      IFNULL(SUM(IF(st.status_name NOT IN('For Liquidation', 'Liquidated', 'Deleted'), rf.amount ,0)), 0) AS return_fund_da
                    FROM tbl_return_fund rf
                    JOIN tbl_return_fund_history rfh_1 USING(rfid)
                    LEFT JOIN tbl_status st ON rfh_1.status_id = st.status_id AND status_type = 'RETURN_FUND'
                    LEFT JOIN tbl_return_fund_history rfh_2 ON rfh_2.rfid = rfh_1.rfid AND rfh_1.return_fund_history_id < rfh_2.return_fund_history_id
                    WHERE rfh_2.return_fund_history_id IS NULL
                    GROUP BY rf.fund
                  ) AS return_fund ON return_fund.fund = v.vid
                  INNER JOIN tbl_company c ON c.cid = v.company
                  INNER JOIN tbl_region r ON r.rid = f.region
                  WHERE v.transfer_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to} 23:59:59' {$region} {$this->companyQry}
                  ORDER BY v.transfer_date ASC, v.vid ASC
SQL;
                $this->table->set_template([
                  "table_open" => "<table id='table_liq' class='table'>"
                ]);
                
                return $this->table->generate($this->db->query($sql));
        }

        public function load_sales($vid)
        {
                $result = $this->db->query("select * from tbl_sales
                        inner join tbl_engine on engine = eid
                        inner join tbl_customer on customer = cid
                        where fund = ".$vid."
                        order by bcode")->result_object();

                foreach ($result as $key => $sales)
                {
                        $sales->status = $this->status[$sales->status];
                        $result[$key] = $sales;
                }

                return $result;
        }
}
