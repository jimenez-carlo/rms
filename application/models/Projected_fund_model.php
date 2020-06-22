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
                $company = 'f.company != 8';
              break;
            case 'MDI':
                $company = 'f.company = 8';
              break;
          }

          $query = <<<SQL
            SELECT
              fid, fund, cash_on_hand, region,
              CONCAT('[',
                GROUP_CONCAT('{
                  "cid" : "', cid, '", "company" : "', company, '",
                  "for_ca" : ', for_ca, ', "for_deposit" : ', for_deposit, '}'
                  ORDER BY cid ASC
                  SEPARATOR ','
                ),
              ']') AS company_ca_amount
            FROM (
              SELECT
                  f.fid,
                  f.fund AS fund,
                  f.cash_on_hand,
                  r.region, c.cid,
                  c.company_code AS company,
                  SUM(IF(s.voucher = 0, CASE WHEN s.company = '8' THEN 1200 ELSE 900 END, 0)) AS for_ca,
                  SUM(IF(s.voucher > 0 AND s.fund = 0, CASE WHEN s.company = '8' THEN 1200 ELSE 900 END, 0)) AS for_deposit
              FROM
                  tbl_fund f
                      LEFT JOIN
                  tbl_sales s ON s.region = f.region AND s.fund = 0 AND registration_type != 'Self Registration'
                      LEFT JOIN
                  tbl_company c ON s.company = c.cid
                      LEFT JOIN
                  tbl_region r ON s.region = r.rid
              WHERE
                  f.company != 8 AND s.payment_method = 'CASH'
              GROUP BY f.fid , c.cid , c.company_code
              ) AS first_select
              GROUP BY fid, fund, cash_on_hand, region
SQL;
          $get_fund_per_region = $this->db->query($query)->result_object();

          return $get_fund_per_region;
	}

  /**
   * Accounting to Create Voucher
   */
        public function create_voucher($fid, $cid) {
          $fund = $this->db->query("SELECT * FROM tbl_fund WHERE fid = ".$fid)->row();

          $region = $this->reg_code[$fund->region];
          $company = ($fund->company == 2) ? 6 : $fund->company;
          $fund->reference = 'CA-'.$region.'-'.date('ymd');

          $ref_code = $this->db->query("
            SELECT
              count(*) AS c
            FROM
              tbl_voucher
            WHERE
              reference LIKE '".$fund->reference."%'"
          )->row()->c;

          $fund->reference .= ($ref_code == 0) ? '' : '-'.($ref_code++);

          $budget = ((int) ($_SESSION['company']) == 8) ? 1200 : 900;

          $fund->transmittal = $this->db->query("
            SELECT
              t.ltid, t.code, t.region, t.company
              ,LEFT(t.date, 10) AS date
              ,SUM($budget) AS amount
              ,COUNT(*) AS sales
            FROM
              tbl_lto_transmittal t
            INNER JOIN
              tbl_sales s ON s.lto_transmittal = t.ltid
            WHERE
              t.region = ".$fund->region." AND LEFT(s.bcode, 1) = '".$cid."'
              AND voucher = 0 AND registration_type != 'Self Registration'
              AND s.payment_method = 'CASH'
            GROUP BY t.date, t.company, t.ltid
          ")->result_object();

          return $fund;
        }

	public function print_projected($fid, $ltid)
	{
		$fund = $this->db->query("SELECT * FROM tbl_fund WHERE fid = ".$fid)->row();

		$region = $this->reg_code[$fund->region];
		$company = ($fund->company == 2) ? 6 : $fund->company;
		$fund->reference = 'CA-'.$region.'-'.date('ymd');

                $ref_code = $this->db->query("
                  SELECT
                    COUNT(*) as c
                  FROM
                    tbl_voucher
                  WHERE
                    reference LIKE '".$fund->reference."%'
                ")->row()->c;
		$fund->reference .= ($ref_code == 0) ? '' : '-'.($ref_code++);

                $fund->sales = $this->db->query("
                  SELECT
                    s.bcode, s.bname, count(*) as units
		  FROM
                    tbl_sales s
                  INNER JOIN
                    tbl_engine e on e.eid = s.engine
                  INNER JOIN
                    tbl_customer c on c.cid = s.customer
                  WHERE
                    s.lto_transmittal in (".$ltid.")
                    AND s.voucher = 0 AND s.registration_type != 'Self Registration' AND s.payment_method = 'CASH'
                  GROUP BY s.bcode, s.bname
                ")->result_object();

		$fund->region = $this->region[$fund->region];
		return $fund;
	}

	public function save_voucher($voucher, $ltid)
	{
                $this->db->simple_query('SET SESSION group_concat_max_len=15000');
                $batch = $this->db->query("
                  SELECT
                    GROUP_CONCAT(s.sid SEPARATOR ',') AS sids, lt.company
                  FROM
                    tbl_sales s
                  INNER JOIN
                    tbl_lto_transmittal lt ON s.lto_transmittal = lt.ltid
                  WHERE
                    lt.ltid IN (".$ltid.") AND voucher = 0 AND s.registration_type != 'Self Registration' AND s.payment_method = 'CASH'
                  GROUP BY
                    lt.company
                ")->row();

                if (!empty($batch)) {
                  $voucher->company = $batch->company;
                }

		$this->db->insert('tbl_voucher', $voucher);
		$voucher->vid = $this->db->insert_id();

                $this->db->query("
                  UPDATE
                    tbl_sales
                  SET
                    voucher = ".$voucher->vid.",
                    user = ".$_SESSION['uid']."
                  WHERE
                    sid IN (".$batch->sids.")
                ");

                $fund = $this->db->query("
                  SELECT
                    *
                  FROM
                    tbl_fund
                  WHERE
                    fid = ".$voucher->fund
                )->row();

		$voucher->region  = $this->region[$fund->region];
		$voucher->company = $this->company[$fund->company];

		return $voucher;
	}

  /**
   * Accounting to view list of Voucher
   */
        public function list_voucher($param) {
          $status = (is_numeric($param->status))
            ? ' AND status = '.$param->status : '';
          $region = (is_numeric($param->region))
            ? ' AND region = '.$param->region : '';

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
}
