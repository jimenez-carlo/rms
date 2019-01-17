<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Diy_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function load_customers()
	{
		$date = date('Y-m-d',(strtotime ( '-1 day' , strtotime (date('Y-m-d')) ) ));
		$result = $this->db->query('select distinct branch, left(transmittal_date,10) as tr_date, cust_type from tbl_sales')->result_object();

		if(!empty($result))
		{
			$in_branch = "";

			foreach ($result as $key => $tr) {
				$branches = explode(',',$in_branch);
				if (!in_array($tr->branch, $branches)) {
				  $in_branch .= $tr->branch.',';
				}
			}

			$in_branch = substr($in_branch,0,-1);

			$global = $this->load->database('global', TRUE);
			$branch = $global->query('select bid,concat(b_code," ",code," ",b.name) as name
																from tbl_branches b
																left join tbl_companies c on cid=company
																where bid in ('.$in_branch.')')->result_object();

			$arrBranch = array();

			foreach ($branch as $key => $br) {
				$arrBranch[$br->bid] = $br->name;
			}

			foreach ($result as $key => $tr) {
				$result[$key]->branch = $arrBranch[$tr->branch];
				if(($tr->cust_type)) $tr->cust_type = 'Organization';
				else $tr->cust_type = 'Individual';
			}
		}

		return $result;
	}

	public function load()
	{
		$date = date('Y-m-d',(strtotime ( '-1 day' , strtotime (date('Y-m-d')) ) ));
		$result = $this->db->query('select distinct branch, left(transmittal_date,10) as tr_date, customer, cust_type
																from tbl_sales
																left join tbl_customer on customer=cid
																where left(sales_type,9)="Brand New"
																group by branch,cust_type
																order by transmittal_date desc')->result_object();

		if(!empty($result))
		{
			$in_branch = "";

			foreach ($result as $key => $tr) {
				$branches = explode(',',$in_branch);
				if (!in_array($tr->branch, $branches)) {
				  $in_branch .= $tr->branch.',';
				}
			}

			$in_branch = substr($in_branch,0,-1);

			$global = $this->load->database('global', TRUE);
			$branch = $global->query('select bid,concat(b_code," ",code," ",b.name) as name
																from tbl_branches b
																left join tbl_companies c on cid=company
																where bid in ('.$in_branch.')')->result_object();

			$arrBranch = array();

			foreach ($branch as $key => $br) {
				$arrBranch[$br->bid] = $br->name;
			}

			foreach ($result as $key => $tr) {
				$result[$key]->bid = $tr->branch;
				$result[$key]->type = $tr->cust_type;
				$result[$key]->branch = $arrBranch[$tr->branch];
				if($tr->cust_type) $tr->cust_type = 'Organization';
				else $tr->cust_type = 'Individual';
			}
		}

		return $result;
	}
}