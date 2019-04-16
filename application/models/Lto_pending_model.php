<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Lto_pending_model extends CI_Model{

	public $cust_type = array(
		0 => 'Individual',
		1 => 'Organizational'
	);

	public $status = array(
		0 => 'New',
		1 => 'Incomplete',
		2 => 'Done'
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function load_list($region)
	{
          $result = $this->db->query("
	    SELECT
	        t.*, LEFT(t.date, 10) AS date, COUNT(*) AS sales
	    FROM
	        tbl_lto_transmittal t
	            INNER JOIN
	        tbl_sales ON lto_transmittal = ltid
	    WHERE
	        t.region = ".$region."
	            AND registration_type != 'Self Registration'
	            AND status < 2
	    GROUP BY ltid
	    ORDER BY t.date DESC
          ")->result_object();

	  return $result;
	}

	public function load_transmittal($ltid)
	{
		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$ltid)->row();
		$transmittal->sales = $this->db->query("select *, left(date_sold, 10) as date_sold
			from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where lto_transmittal = ".$ltid."
			and registration_type != 'Self Registration'
			and status < 2
			order by bcode desc")->result_object();
		return $transmittal;
	}
}
