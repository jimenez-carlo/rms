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

                return $this->db->query("
                  SELECT
                    v.*, f.region,
                    c.company_code AS companyname,
                    COUNT(DISTINCT s.sid) AS sales_count,
                    IFNULL(SUM(CASE WHEN s.status < 3 THEN 1200 ELSE 0 END), 0) AS rrt_pending,
                    IFNULL(SUM(CASE WHEN s.status = 3 THEN registration ELSE 0 END), 0) AS lto_pending,
                    IFNULL(SUM(CASE WHEN s.status = 4 THEN registration+tip ELSE 0 END), 0) AS for_liquidation,
                    IFNULL(SUM(CASE WHEN s.status = 5 THEN registration+tip ELSE 0 END), 0) AS liquidated,
                    (SELECT
                      SUM(amount)
                    FROM
                      tbl_misc m
                    LEFT JOIN
                      tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
                    LEFT JOIN
                      tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
                    WHERE
                      ca_ref = vid AND mxh2.id IS NULL AND mxh1.status > 1 AND mxh1.status < 4
                    ) AS misc_for_liq,
                    (SELECT
                      SUM(amount)
                    FROM
                      tbl_misc m
                    LEFT JOIN
                      tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
                    LEFT JOIN
                      tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
                    WHERE
                      ca_ref = vid AND mxh2.id IS NULL AND mxh1.status = 4
                    ) AS misc_liquidated,
                    (
                      SELECT
                        SUM(rf.amount)
                      FROM
                        tbl_return_fund rf
                      JOIN
                        tbl_return_fund_history rfh_1 USING(rfid)
                      LEFT JOIN
                        tbl_status st ON rfh_1.status_id = st.status_id AND status_type = 'RETURN_FUND'
                      LEFT JOIN
                        tbl_return_fund_history rfh_2 ON rfh_2.rfid = rfh_1.rfid AND rfh_1.return_fund_history_id < rfh_2.return_fund_history_id
                      WHERE
                        rf.liq_date IS NULL AND rf.fund = v.vid AND rfh_2.return_fund_history_id IS NULL AND st.status_name = 'For Liquidation'
                    ) AS return_for_liq,
                    (
                      SELECT
                        SUM(amount)
                      FROM
                        tbl_return_fund
                      WHERE
                        fund = vid AND liq_date IS NOT NULL
                    ) AS return_liquidated
                  FROM tbl_voucher v
                  INNER JOIN tbl_fund f ON fid = v.fund
                  INNER JOIN tbl_sales s ON s.fund = vid
                  INNER JOIN tbl_company c ON c.cid = s.company
                  WHERE LEFT(transfer_date, 10) BETWEEN '".$date_from."' AND '".$date_to."' ".$region." ".$this->companyQry."
                  GROUP BY v.vid, c.cid
                  ORDER BY transfer_date DESC
                ")->result_object();
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
