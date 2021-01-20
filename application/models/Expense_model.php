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
		$reference = (!empty($param->reference)) ? "AND v.reference LIKE '%{$param->reference}%'" : '';
		$type = (!empty($param->type) && is_numeric($param->type)) ? ' AND m.type = '.$param->type : '';
		$status = (is_numeric($param->status)) ? ' AND mxh1.status = '.$param->status : '';
                $region = (!empty($param->region)) ? "AND m.region = {$param->region}" : '';
                $company = ($param->company === "MDI") ? "AND v.company = 8" : "AND v.company != 8";

                $result = $this->db->query("
                  SELECT
                    m.mid, m.region, m.date, m.or_no, SUBSTR(m.or_date, 1, 10) AS or_date,
                    FORMAT(m.amount, 2) AS amount, mt.type, m.other, m.topsheet,
                    m.batch, m.ca_ref, mxh1.id, mxh1.remarks,
                    v.reference, s.status_name AS status,
                    CASE
                      WHEN mxh1.status < 2 THEN true
                      WHEN mxh1.status = 5 THEN true
                      ELSE false
                    END AS edit
                  FROM
                    tbl_misc m
                  LEFT JOIN
                    tbl_voucher v ON v.vid = m.ca_ref
                  LEFT JOIN
                    tbl_misc_type mt ON m.type = mt.mtid
                  JOIN
                    tbl_misc_expense_history mxh1 USING(mid)
                  LEFT JOIN
                    tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
                  INNER JOIN
                    tbl_status s ON mxh1.status = s.status_id AND s.status_type = 'MISC_EXP'
                  WHERE
                    mxh2.id IS NULL AND mxh1.status != 90
                    {$company} {$region} {$status} {$reference}
                  ORDER BY or_date DESC LIMIT 1000
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
                    m.batch, m.ca_ref, s.status_name AS status,
                    v.reference as ca_ref, mxh1.remarks, m.da_reason,
                    CASE WHEN mxh1.status = 0 THEN true ELSE false END AS approval
                  FROM
                    tbl_misc m
                  LEFT JOIN
                    tbl_misc_type mt ON m.type = mt.mtid
                  JOIN
                    tbl_misc_expense_history mxh1 USING(mid)
                  LEFT JOIN
                    tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
                  INNER JOIN
                    tbl_status s ON mxh1.status = s.status_id AND s.status_type = 'MISC_EXP'
                  INNER JOIN
                    tbl_voucher v ON m.ca_ref = v.vid
                  WHERE
                    m.mid = $mid AND mxh2.id IS NULL
                ")->row();

		$misc->files = directory_map('./rms_dir/misc/'.$mid.'/', 1);
		return $misc;
	}

	public function edit_misc($mid)
	{
		$this->load->helper('directory');
                $misc = $this->db->query("
                  SELECT
                    m.*, mxh1.*
                  FROM
                    tbl_misc m
                  JOIN
                    tbl_misc_expense_history mxh1 USING (mid)
                  LEFT JOIN
                    tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
                  WHERE m.mid = $mid AND mxh2.id IS NULL
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
