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
                switch ($this->session->position_name) {
                  case 'Accounts Payable Clerk':
                  case 'RRT National Registration Manager':
                    $condition = ($this->session->company_code === 'MDI') ? 'bcode >= 8000 AND da_reason > 0' : ' bcode < 8000 AND da_reason > 0';
                    break;
                  case 'RRT Supervisor':
                  case 'RRT Branch Secretary':
                    $condition = "region = '$param->region' AND da_reason > 0";
                    break;
                  default:
                    $condition = 'bcode = '.$this->session->branch_code;
                    break;
                }

                $result = $this->db->query("
                  SELECT
                    DISTINCT bcode, bname
                  FROM
                    tbl_sales
                  WHERE
                    $condition
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
		$branch = (empty($param->branch))  ? "" : " AND s.bcode = '$param->branch'";

                switch ($this->session->position_name) {
                  case 'Accounts Payable Clerk':
                  case 'RRT National Registration Manager':
                    $condition = ($this->session->company_code === 'MDI') ? 's.bcode >= 8000' : 's.bcode < 8000';
                    break;
                  case 'RRT Supervisor':
                  case 'RRT Branch Secretary':
                    $condition = "s.region = '$param->region'";
                    break;
                  default:
                    $condition = 's.bcode = '.$this->session->branch_code;
                    $branch = '';
                    break;
                }

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
                    {$condition} {$branch} AND s.da_reason > 0 AND s.da_reason != 11
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
}
