<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Orcr_liquidation_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function ca_for_liquidation_list()
	{
		return $this->db->query("select v.*, count(sid) as sales from tbl_voucher v
				inner join tbl_sales s on voucher = vid
				where s.topsheet > 0 and s.batch = 0
				group by vid")->result_object();
	}
}
