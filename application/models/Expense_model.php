<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model{

	public $type = array(
		1 => 'Meal',
		2 => 'Photocopy',
		3 => 'Transportation',
		4 => 'Others',
	);

	public $status = array(
		0 => 'For Approval',
		1 => 'Rejected',
		2 => 'Approved',
		3 => 'For Liquidation',
		4 => 'Liquidated',
		5 => 'Disapproved by Accounting'
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function list_misc($param)
	{
		$date_from = (empty($param->date_from)) ? date('Y-m-d') : $param->date_from;
		$date_to = (empty($param->date_to)) ? date('Y-m-d') : $param->date_to;
		$type = (!empty($param->type) && is_numeric($param->type))
			? ' AND m.type = '.$param->type : '';
		$status = (is_numeric($param->status))
			? ' AND mxh.status = '.$param->status : '';

                $result = $this->db->query("
                  SELECT
                    m.mid, m.region, m.date, m.or_no, SUBSTR(m.or_date, 1, 10) AS or_date,
                    m.amount, mt.type, m.other, m.topsheet,
                    m.batch, m.ca_ref, mxh.id, mxh.remarks,
                    ms.status_name AS status,
                    CASE
                      WHEN mxh.status < 2 THEN true
                      WHEN mxh.status = 5 THEN true
                      ELSE false
                    END AS edit
                  FROM
                    tbl_misc m
                  LEFT JOIN
                    tbl_misc_type mt ON m.type = mt.mtid
                  JOIN
                    tbl_misc_expense_history mxh USING(mid)
                  JOIN
                    tbl_misc_status ms ON mxh.status = ms.id
                  WHERE
                    m.region = ".$param->region." AND LEFT(m.or_date,10) BETWEEN '".$date_from."' AND '".$date_to."' ".$type."
                    AND mxh.id IN (
                      SELECT MAX(id) FROM tbl_misc_expense_history GROUP BY mid
                    ) $status
                  ORDER BY or_date
                    DESC LIMIT 1000
                ")->result_object();

		return $result;
	}

	public function load_misc($mid)
	{
		$this->load->helper('directory');
                $misc = $this->db->query("
                  SELECT
                    m.mid, m.region, m.date, m.or_no,
                    SUBSTR(m.or_date, 1, 10) AS or_date,
                    m.amount, mt.type, m.other, m.topsheet,
                    m.batch, m.ca_ref, ms.status_name AS status,
                    v.reference as ca_ref, mxh.remarks,
                    CASE WHEN mxh.status = 0 THEN true ELSE false END AS approval
                  FROM
                    tbl_misc m
                  LEFT JOIN
                    tbl_misc_type mt ON m.type = mt.mtid
                  JOIN
                    tbl_misc_expense_history mxh USING(mid)
                  JOIN
                    tbl_misc_status ms ON mxh.status = ms.id
                  INNER JOIN
                    tbl_voucher v ON m.ca_ref = v.vid
                  WHERE mid = $mid AND mxh.id = (
                    SELECT MAX(id) FROM tbl_misc_expense_history WHERE mid = $mid
                  )
                ")->row();

		$misc->files = directory_map('./rms_dir/misc/'.$mid.'/', 1);
		return $misc;
	}

	public function edit_misc($mid)
	{
		$this->load->helper('directory');
                $misc = $this->db->query("
                  SELECT
                    *
                  FROM
                    tbl_misc m
                  JOIN
                    tbl_misc_expense_history mxh USING (mid)
                  WHERE m.mid = $mid AND mxh.id = (
                    SELECT MAX(id) FROM tbl_misc_expense_history WHERE mid = $mid
                  )
                ")->row();
		$misc->files = directory_map('./rms_dir/misc/'.$mid.'/', 1);
		return $misc;
	}

	public function save_status($status,$mid)
	{
		$this->db->query("update tbl_misc set status = $status where mid = $mid");
	}

	public function update_hold($status,$cid)
	{
		$this->db->query("update tbl_check set hold = $status where cid = $cid");
	}
}
