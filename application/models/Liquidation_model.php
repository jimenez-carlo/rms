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
		$date_from = (empty($param->date_from)) ? date('Y-m-d', strtotime('-15 days')) : $param->date_from;
		$date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
		$region = (is_numeric($param->region)) ? ' AND f.region = '.$param->region : '';

		return $this->db->query("SELECT v.*, f.region,
				CASE
				WHEN v.company = 1 THEN 'MNC'
				WHEN v.company = 2 THEN 'MTI'
				WHEN v.company = 3 THEN 'HPTI'
<<<<<<< HEAD
				END as companyname,
				count(distinct s.sid) as sales_count,
				ifnull(sum(case when s.status < 3 then 1200 else 0 end), 0) as rrt_pending,
				ifnull(sum(case when s.status = 3 then registration else 0 end), 0) as lto_pending,
				ifnull(sum(case when s.status = 4 then registration+tip else 0 end), 0) as for_liquidation,
				ifnull(sum(case when s.status = 5 then registration+tip else 0 end), 0) as liquidated,
				(select sum(amount) from tbl_misc where ca_ref = vid and status > 1 and status < 4) as misc_for_liq,
				(select sum(amount) from tbl_misc where ca_ref = vid and status = 4) as misc_liquidated,
				(select sum(amount) from tbl_return_fund where fund = vid and liq_date is null) as return_for_liq,
				(select sum(amount) from tbl_return_fund where fund = vid and liq_date is not null) as return_liquidated
			from tbl_voucher v
			INNER join tbl_fund f on fid = v.fund
			INNER join tbl_sales s on s.fund = vid
			where left(transfer_date, 10) between '".$date_from."' and '".$date_to."'
			".$region."
			group by vid
			order by transfer_date desc")->result_object();
=======
				WHEN v.company = 8 THEN 'MDI'
				END AS companyname,
				COUNT(DISTINCT s.sid) AS sales_count,
				IFNULL(SUM(CASE WHEN s.status < 3 THEN 1200 else 0 END), 0) AS rrt_pending,
				IFNULL(SUM(CASE WHEN s.status = 3 THEN registration ELSE 0 END), 0) AS lto_pending,
				IFNULL(SUM(CASE WHEN s.status = 4 THEN registration+tip ELSE 0 END), 0) AS for_liquidation,
				ifnull(SUM(CASE WHEN s.status = 5 THEN registration+tip ELSE 0 END), 0) AS liquidated,
				(SELECT SUM(amount) FROM tbl_misc WHERE ca_ref = vid AND status > 1 AND status < 4) AS misc_for_liq,
				(SELECT SUM(amount) FROM tbl_misc WHERE ca_ref = vid AND status = 4) AS misc_liquidated,
				(SELECT SUM(amount) FROM tbl_return_fund WHERE fund = vid AND liq_date IS NULL) AS return_for_liq,
				(SELECT SUM(amount) FROM tbl_return_fund WHERE fund = vid AND liq_date IS NOT NULL) AS return_liquidated
			FROM tbl_voucher v
			INNER JOIN tbl_fund f ON fid = v.fund
			INNER JOIN tbl_sales s ON s.fund = vid
			WHERE LEFT(transfer_date, 10) BETWEEN '".$date_from."' AND '".$date_to."'
			".$region." ".$this->companyQry."
			GROUP BY vid
			ORDER BY transfer_date DESC")->result_object();
>>>>>>> production.50
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
