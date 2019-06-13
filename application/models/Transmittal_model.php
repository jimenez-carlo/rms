<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Transmittal_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function load_transmittal($tid, $branch)
	{
		$bcode = (!empty($branch)) ? " and bcode = '".$branch."'" : '';

		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$tid)->row();

		$topsheet->sales = $this->db->query("select * from tbl_sales
			inner join tbl_customer on customer = cid
			where topsheet = ".$tid.$bcode."
			order by bcode")->result_object();
		foreach ($topsheet->sales as $key => $sales)
		{
			$last_remarks = $this->db->query("select * from tbl_transmittal_remarks
				where sales = ".$sales->sid."
				order by trid desc limit 1")->row();
			$sales->last_user = (!empty($last_remarks))
				? $this->cmc->get_user_info($last_remarks->user)
				: null;

			$sales->status = (empty($sales->received_date)) ? 'Not Received' : 'Received';
			$topsheet->sales[$key] = $sales;
		}

		return $topsheet;
	}

	public function get_branch_transmittal($branch)
	{
		$result = $this->db->query("select t.*, count(*) as sales from tbl_sales
			inner join tbl_topsheet t on topsheet = tid
			where bcode = '".$branch."'
			and t.transmittal_date is not null
			group by tid")->result_object();
		return $result;
	}

	public function get_rrt_transmittal($region) {

          $result = $this->db->query("
            SELECT
                bcode,
                bname,
                SUM(CASE WHEN t.transmittal_date IS NULL THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN t.transmittal_date IS NOT NULL AND received_date IS NULL THEN 1 ELSE 0 END) AS intransit,
                SUM(CASE WHEN received_date IS NOT NULL THEN 1 ELSE 0 END) AS received
            FROM
                tbl_sales
                    INNER JOIN
                tbl_topsheet t ON topsheet = tid
            WHERE
                t.region = '".$region."'
            GROUP BY
                bcode, bname, 1
          ")->result_object();

          return $result;

	}

	public function get_rrt_intransit($bcode)
	{
		$result = $this->db->query("select s.*, c.*, e.*, t.trans_no, t.date as topsheet_date, t.transmittal_date as transmittal_date
			from tbl_sales s
			inner join tbl_customer c on customer = cid
			inner join tbl_engine e on engine = eid
			inner join tbl_topsheet t on topsheet = tid
			where bcode = '".$bcode."'
			and t.transmittal_date is not null
			and received_date is null
			order by t.transmittal_date desc limit 1000")->result_object();
		return $result;
	}

	public function get_rrt_received($bcode)
	{
		$result = $this->db->query("select s.*, c.*, e.*, t.trans_no, t.date as topsheet_date
			from tbl_sales s
			inner join tbl_customer c on customer = cid
			inner join tbl_engine e on engine = eid
			inner join tbl_topsheet t on topsheet = tid
			where bcode = '".$bcode."'
			and received_date is not null
			order by received_date desc limit 1000")->result_object();
		return $result;
	}
}
