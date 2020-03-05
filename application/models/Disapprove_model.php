<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Disapprove_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
                $this->load->model('Caching_model', 'caching');
                switch ($_SESSION['company']) {
                  case 8:
                    $this->and_company = " AND s.company = 8";
                    break;

                  default:
                    $this->and_company = " AND s.company != 8";
                    break;
                }
	}

        public function da_reason()
        {
                $delete = $this->caching->delete('DA_REASON');
                if ($delete) {
                  $this->db->cache_delete('disapprove','resolve');
                }

                $this->db->cache_on();
                $sql = "SELECT * FROM tbl_da_status WHERE id != 11";
                $da_status = $this->db->query($sql)->result_array();
                $status = [];
                foreach ($da_status as $da) {
                  $status[$da['id']] = $da['da_status'];
                }
                return $status;
        }

	public function branch_list($param)
	{
                $region = ($this->session->position === '3') ? '> 0' : "= '$param->region'";
                $result = $this->db->query("
                  SELECT
                    DISTINCT bcode, bname
                  FROM
                    tbl_sales
                  WHERE
                    region $region AND da_reason > 0
                  ORDER BY bcode
                ")->result_object();

		$branches = array();
		foreach($result as $row) {
			$branches[$row->bcode] = $row->bcode.' '.$row->bname;
		}

		return $branches;
	}

	public function load_list($param)
	{
                /* ---
                  Position 3 = Accounting | Position 108 = RRT | Position 107 = RRT Manager
                */
                if(in_array($this->session->position, ['3', '107'])) {
                  if ($this->session->company !== '8') {
                    $region = "s.region <= 10";
                  } else {
                    $region = "s.region >= 11";
                  }
                } else {
                  $region = "s.region = '$param->region'";
                }

		$branch = (empty($param->branch))  ? "" : " AND s.bcode = '$param->branch'";

                return $this->db->query("
                  SELECT
                    s.*, e.*, c.*, t.trans_no
                  FROM
                    tbl_sales s
                  INNER JOIN
                    tbl_engine e ON engine = eid
                  INNER JOIN
                    tbl_customer c ON customer = cid
                  INNER JOIN
                    tbl_topsheet t ON topsheet = tid
                  WHERE
                    {$region} {$branch}
                    AND s.da_reason > 0 AND s.da_reason != 11
		  ORDER BY s.bcode")->result_object();
	}

        public function get_da_resolve()
        {

                return $this->db->query("
                  SELECT
                    s.*, e.*, c.*, t.trans_no, ds.id, ds.da_status
                  FROM
                    tbl_sales s
                  INNER JOIN
                    tbl_engine e ON engine = eid
                  INNER JOIN
                    tbl_customer c ON customer = cid
                  INNER JOIN
                    tbl_topsheet t ON topsheet = tid
                  INNER JOIN
                    tbl_da_status ds ON s.da_reason = ds.id
                  WHERE
                    s.da_reason = 11 {$this->and_company}
                  ORDER BY s.bcode
                ")->result_object();
        }

	public function load_sales($sid)
	{
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where sid = ".$sid)->row();
		$sales->da_reason = $da_reason[$sales->da_reason];
		return $sales;
	}

        public function da_status_history($sale_id)
        {
          return [
            'sales_id' => $sale_id,
            'da_status_id' => '0',
            'uid' => $this->session->uid
          ];
        }

        public function da_misc_expense($misc)
        {
          //$this->db->update('tbl_misc', array('status' => 5), 'mid ='.$misc['mid']);
          $misc['status'] = 5;
          $misc['uid'] = $_SESSION['uid'];
          return $this->db->insert('tbl_misc_expense_history', $misc);
        }

}
