<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Rms_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_type($type_group) {
		$result = $this->db->query("SELECT
																	a.tid,
																	a.name,
																	b.name AS type_group
																FROM tbl_type a
																LEFT JOIN tbl_type_group b ON tgid=type_group
																WHERE b.name LIKE '%$type_group%'");
		return $result->result_object();
	}

	public function get_topsheet_stat_count($status) {
		if($_SESSION['position']!=98) $region = ' AND region = '.$_SESSION['region_id'];
		else $region = '';

		$result = $this->db->query("SELECT
				status
			FROM (select batch, max(status) as status
				from tbl_batch_status
				group by batch) tbl
			INNER JOIN tbl_batch on bid = batch
			INNER JOIN tbl_topsheet on tid = topsheet
			WHERE
				status='$status'
				$region");
		return $result->num_rows();
	}

	public function get_diy_pending_count() {
		$result = $this->db->query("SELECT
																	transmittal_date
																FROM tbl_sales
																WHERE transmittal_date LIKE '".date('Y-m-d')."%'");
		return $result->num_rows();
	}

	public function get_diy_rejected_count() {
		$result = $this->db->query("SELECT
																	status_date
																FROM tbl_sales_status
																WHERE status_date LIKE '".date('Y-m-d')."%'");
		return $result->num_rows();
	}

	public function get_diy_nomatch_count() {
		$result = $this->db->query("SELECT
																	match_date
																FROM tbl_no_match
																WHERE match_date LIKE '".date('Y-m-d')."%'");
		return $result->num_rows();
	}

	public function get_for_transmittal_table() {
		if ($_SESSION['position'] == 27) {
			$branch_clause = "";
		}
		else {
			$branch_clause = " AND branch = " . $_SESSION['branch'];
		}
		$result = $this->db->query("SELECT
																	a.sid,
																	LEFT(a.date_sold, 10) AS date_sold,
																	b.engine_no,
																	a.ar_no,
																	a.si_no,
																	a.amount,
																	a.sales_type,
																	a.registration_type,
                                  CONCAT(c.first_name,' ',c.last_name) AS customer
																FROM tbl_sales a
																LEFT JOIN tbl_engine b ON engine=eid
																LEFT JOIN tbl_customer c ON customer=cid
																WHERE
																	status = 'New'
																" . $branch_clause . "
																	and left(a.sales_type, 4) = 'Repo'
																ORDER BY date_sold desc");
		return $result->result_object();
	}

	public function get_for_transfer_table() {
		if ($_SESSION['position'] == 27) {
			$branch_clause = "";
		}
		else {
			$branch_clause = " AND branch = " . $_SESSION['branch'];
		}
		$result = $this->db->query("SELECT
																	tfid,
																	LEFT(a.date_sold, 10) AS date_sold,
																	b.engine_no,
																	a.ar_no,
																	a.si_no,
																	a.amount,
																	a.sales_type,
																	a.registration_type,
																	a.plate_no,
                                  CONCAT(c.first_name,' ',c.last_name) AS customer
																FROM tbl_transfer
																INNER JOIN tbl_sales a ON sales=sid
																LEFT JOIN tbl_engine b ON engine=eid
																LEFT JOIN tbl_customer c ON customer=cid
																WHERE
																	transmittal is null
																" . $branch_clause . "
																ORDER BY date_sold desc");
		return $result->result_object();
	}

	public function get_for_sales_table($status = "", $date_from = "", $date_to = "") {
		$where = "";
		if (!empty($status))
		{
			$where = " and status = '".$status."' ";
		}
		if (!empty($date_from) && !empty($date_to))
		{
			$where = " and date_sold between '".$date_from."' and '".$date_to."' ";
		}

		$result = $this->db->query("SELECT
																	*
																FROM tbl_sales
																INNER JOIN tbl_customer on cid = customer
																INNER JOIN tbl_engine on eid = engine
																WHERE 1=1 ".$where);
		return $result->result_object();
	}

	public function get_for_reimbursement_table() {
		$result = $this->db->query("SELECT
								  a.sid,
                                  a.insurance,
                                  a.emission,
								  a.registration,
                                  a.tip,
								  b.engine_no,
                                  CONCAT(c.first_name,' ',c.last_name) AS customer
																FROM tbl_sales a
																LEFT JOIN tbl_engine b ON engine=eid
																LEFT JOIN tbl_customer c ON customer=cid
																WHERE
																	is_registered=1");
		return $result->result_object();
	}

	public function get_users($username) {
	  $global = $this->load->database('global', TRUE);
		$result = $global->query("SELECT
																a.*,
																b.name,
																CONCAT(a.firstname,' ',a.lastname) as fullname,
																c.username
															FROM tbl_users_info a
																INNER JOIN tbl_positions b ON position=pid
																INNER JOIN tbl_users c ON a.uid=c.uid
														  WHERE
														  	c.username LIKE '%$username%'
														  ORDER BY c.username");
	  return $result->result_object();
	}

	public function get_misc($exp_date,$branch) {
		$result = $this->db->query("SELECT
																	*
																FROM tbl_misc
																WHERE
																	exp_date = '$exp_date' AND
																	branch = $branch");
		$row = $result->row();

		if (empty($row))
		{
			$row = new Stdclass();
			$row->meal = 0.00;
			$row->photocopy = 0.00;
			$row->transportation = 0.00;
		}

		return $row;
	}

	public function count_new($month,$year) {
		$result = $this->db->query("SELECT
																	sid
																FROM tbl_sales
																WHERE
																	LEFT(status,1)='N' AND
																	YEAR(post_date) = $year AND
																	MONTH(post_date) = $month");
		return $result->num_rows();
	}

	public function count_total_new($year) {
		$result = $this->db->query("SELECT
																	sid
																FROM tbl_sales
																WHERE
																	LEFT(status,1)='N' AND
																	YEAR(post_date) = $year");
		return $result->num_rows();
	}

	public function count_pending($month,$year) {
		$result = $this->db->query("SELECT
																	stid
																FROM tbl_sales_transmittal
																INNER JOIN tbl_transmittal ON transmittal = tmid
																WHERE
																	YEAR(trans_date) = $year AND
																	MONTH(trans_date) = $month");
		return $result->num_rows();
	}

	public function count_total_pending($year) {
		$result = $this->db->query("SELECT
																	stid
																FROM tbl_sales_transmittal
																INNER JOIN tbl_transmittal ON transmittal = tmid
																WHERE
																	YEAR(trans_date) = $year");
		return $result->num_rows();
	}

	public function count_transmittal($month,$year) {
		$result = $this->db->query("SELECT
																	tmid
																FROM tbl_transmittal
																WHERE
																	YEAR(trans_date) = $year AND
																	MONTH(trans_date) = $month");
		return $result->num_rows();
	}

	public function count_total_transmittal($year) {
		$result = $this->db->query("SELECT
																	tmid
																FROM tbl_transmittal
																WHERE
																	YEAR(trans_date) = $year");
		return $result->num_rows();
	}

	public function count_registered($month,$year) {
		$result = $this->db->query("SELECT
																	sid
																FROM tbl_sales
																WHERE
																	LEFT(status,1)='R' AND
																	YEAR(registration_date) = $year AND
																	MONTH(registration_date) = $month");
		return $result->num_rows();
	}
}
