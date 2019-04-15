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
<<<<<<< HEAD
	public function get_projected_funds()
	{
		$result = $this->db->query("select f.*,
				ifnull(sum(case when left(bcode, 1) = '1' and s.voucher = 0 then 900 else 0 end), '0.00') as voucher_1,
				ifnull(sum(case when left(bcode, 1) = '1' and s.voucher > 0 then 900 else 0 end), '0.00') as transfer_1,
				ifnull(sum(case when left(bcode, 1) = '3' and s.voucher = 0 then 900 else 0 end), '0.00') as voucher_3,
				ifnull(sum(case when left(bcode, 1) = '3' and s.voucher > 0 then 900 else 0 end), '0.00') as transfer_3,
				ifnull(sum(case when left(bcode, 1) = '6' and s.voucher = 0 then 900 else 0 end), '0.00') as voucher_6,
				ifnull(sum(case when left(bcode, 1) = '6' and s.voucher > 0 then 900 else 0 end), '0.00') as transfer_6
			from tbl_fund f
			left join tbl_sales s
				on s.region = f.region
				and s.fund = 0
				and registration_type != 'Self Registration'
			where fid < 11
			group by fid")->result_object();

		foreach ($result as $key => $fund)
		{
			$fund->region = $this->region[$fund->region];
			$fund->company = $this->company[$fund->company];
			$result[$key] = $fund;
		}

		return $result;
=======
	public function get_projected_funds() {

                // FOR MNC fid < 11
                // FOR MDI fid >= 11
          if ($_SESSION['company'] != 8) {

            $result = $this->db->query("
              SELECT f.*,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '1' AND s.voucher = 0 THEN 900 ELSE 0 END), '0.00') AS voucher_1,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '1' AND s.voucher > 0 THEN 900 ELSE 0 END), '0.00') AS transfer_1,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '3' AND s.voucher = 0 THEN 900 ELSE 0 END), '0.00') AS voucher_3,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '3' AND s.voucher > 0 THEN 900 ELSE 0 END), '0.00') AS transfer_3,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '6' AND s.voucher = 0 THEN 900 ELSE 0 END), '0.00') AS voucher_6,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '6' AND s.voucher = 0 THEN 900 ELSE 0 END), '0.00') AS transfer_6
              FROM
                tbl_fund f
              LEFT JOIN
                tbl_sales s ON s.region = f.region AND s.fund = 0 AND registration_type != 'Self Registration'
              WHERE
               f.fid < 11
              GROUP BY f.fid
            ")->result_object();

          } else {
            $result = $this->db->query("
              SELECT f.*,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '8' AND s.voucher = 0 THEN 1200 ELSE 0 END), '0.00') AS voucher_8,
                IFNULL(SUM(CASE WHEN LEFT(bcode, 1) = '8' AND s.voucher = 0 THEN 1200 ELSE 0 END), '0.00') AS transfer_8
              FROM
                tbl_fund f
              LEFT JOIN
                tbl_sales s ON s.region = f.region AND s.fund = 0 AND registration_type != 'Self Registration'
              WHERE
                f.fid >= 11
              GROUP BY f.fid")->result_object();
          }

          foreach ($result as $key => $fund) {
            $fund->region  = $this->region[$fund->region];
            $fund->company = $this->company[$fund->company];
            $result[$key] = $fund;
          }

          return $result;
>>>>>>> production.50
	}

  /**
   * Accounting to Create Voucher
   */
<<<<<<< HEAD
	public function create_voucher($fid, $cid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();

		$region = $this->reg_code[$fund->region];
		$company = ($fund->company == 2) ? 6 : $fund->company;
		$fund->reference = 'CA-'.$region.'-'.date('ymd');

		$ref_code = $this->db->query("select count(*) as c from tbl_voucher
			where reference like '".$fund->reference."%'")->row()->c;
		$fund->reference .= ($ref_code == 0) ? '' : '-'.($ref_code++);
		
		$fund->transmittal = $this->db->query("select t.*,
				left(t.date, 10) as date,
				sum(900) as amount,
				count(*) as sales
			from tbl_lto_transmittal t
			inner join tbl_sales s on lto_transmittal = ltid
			where t.region = ".$fund->region."
			and left(s.bcode, 1) = '".$cid."'
			and voucher = 0
			and registration_type != 'Self Registration'
			group by t.date, t.company")->result_object();
		
		return $fund;
	}

	public function print_projected($fid, $ltid)
	{
		$fund = $this->db->query("select * from tbl_fund where fid = ".$fid)->row();
		
=======
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

          $fund->transmittal = $this->db->query(
            "SELECT
                t.ltid, t.code, t.region, t.company
                 ,LEFT(t.date, 10) AS date
                ,SUM($budget) AS amount
                ,COUNT(*) AS sales
            FROM
                tbl_lto_transmittal t
                    INNER JOIN
                tbl_sales s ON s.lto_transmittal = t.ltid
            WHERE  t.region = ".$fund->region."
                    AND LEFT(s.bcode, 1) = '".$cid."'
                    AND voucher = 0
                    AND registration_type != 'Self Registration'
            GROUP BY t.date, t.company, t.ltid"
          )->result_object();

          return $fund;
        }

	public function print_projected($fid, $ltid)
	{
		$fund = $this->db->query("SELECT * FROM tbl_fund WHERE fid = ".$fid)->row();

>>>>>>> production.50
		$region = $this->reg_code[$fund->region];
		$company = ($fund->company == 2) ? 6 : $fund->company;
		$fund->reference = 'CA-'.$region.'-'.date('ymd');

<<<<<<< HEAD
		$ref_code = $this->db->query("select count(*) as c from tbl_voucher
			where reference like '".$fund->reference."%'")->row()->c;
=======
		$ref_code = $this->db->query("SELECT count(*) as c FROM tbl_voucher
			WHERE reference like '".$fund->reference."%'")->row()->c;
>>>>>>> production.50
		$fund->reference .= ($ref_code == 0) ? '' : '-'.($ref_code++);

                $fund->sales = $this->db->query("
                  SELECT
                    bcode, bname, count(*) as units
		  FROM
                    tbl_sales
                  INNER JOIN
                    tbl_engine on eid = engine
                  INNER JOIN
                    tbl_customer on cid = customer
                  WHERE
                    lto_transmittal in (".$ltid.")
		  AND voucher = 0
		  AND registration_type != 'Self Registration'
                  GROUP BY bcode, bname
                ")->result_object();

		$fund->region = $this->region[$fund->region];
		$fund->company = $this->company[$fund->company];
		$fund->company = '';
		return $fund;
	}

	public function save_voucher($voucher, $ltid)
	{
		$company = $this->db->query("SELECT company FROM tbl_lto_transmittal WHERE ltid in (".$ltid.")")->row();
                if (!empty($company)) {
                  $voucher->company = $company->company;
                }

		$this->db->insert('tbl_voucher', $voucher);
		$voucher->vid = $this->db->insert_id();

		$this->db->query("update tbl_sales 
			set voucher = ".$voucher->vid."
			WHERE lto_transmittal in (".$ltid.")");

		$fund = $this->db->query("SELECT * FROM tbl_fund WHERE fid = ".$voucher->fund)->row();
		$voucher->region  = $this->region[$fund->region];
		$voucher->company = $this->company[$fund->company];
		return $voucher;
	}

  /**
   * Accounting to view list of Voucher
   */
<<<<<<< HEAD
	public function list_voucher($param)
	{
		$status = (is_numeric($param->status))
			? ' and status = '.$param->status : '';
		$region = (is_numeric($param->region))
			? ' and region = '.$param->region : '';

		$result = $this->db->query("select * from tbl_voucher v
			inner join tbl_fund on fid = v.fund
			where date between '".$param->date_from." 00:00:00' and '".$param->date_to." 23:59:59'
			".$status.$region)->result_object();
		foreach ($result as $key => $row)
		{
			$row->date = substr($row->date, 0, 10);
			$row->transfer_date = substr($row->transfer_date, 0, 10);
			$row->region = $this->region[$row->region];
			$row->company = $this->company[$row->company];
			$row->status = $this->status[$row->status];
			$result[$key] = $row;
		}
		return $result;
	}
=======
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
>>>>>>> production.50
}
