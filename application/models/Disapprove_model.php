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
                $sql = "SELECT * FROM tbl_status WHERE status_id != 11 AND status_type = 'DA'";
                $da_status = $this->db->query($sql)->result_array();
                $status = [];
                foreach ($da_status as $da) {
                  $status[$da['status_id']] = $da['status_name'];
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
                $da_status = 'AND s.da_reason > 0 AND s.da_reason != 11';

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
                    $da_status = 'AND s.da_reason IN(2,3,10)';
                    $branch = '';
                    break;
                }

                $result = $this->db->query("
                  SELECT
                    s.*, e.*, c.*, t.trans_no
                  FROM
                    tbl_sales s
                  INNER JOIN
                    tbl_engine e ON engine = eid
                  INNER JOIN
                    tbl_customer c ON customer = cid
                  LEFT JOIN
                    tbl_topsheet t ON topsheet = tid
                  WHERE
                    {$condition} {$branch} {$da_status}
		  ORDER BY s.bcode")->result_object();

                return $result;
	}

        public function get_da_resolve()
        {

                return $this->db->query("
                  SELECT
                    s.*, e.*, c.*, t.trans_no, ds.status_id AS id, ds.status_name AS da_status
                  FROM
                    tbl_sales s
                  INNER JOIN
                    tbl_engine e ON engine = eid
                  INNER JOIN
                    tbl_customer c ON customer = cid
                  INNER JOIN
                    tbl_topsheet t ON topsheet = tid
                  INNER JOIN
                    tbl_status ds ON s.da_reason = ds.status_id AND ds.status_type = 'DA'
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
          //Return Cash On Hand
          $this->db->query("
            UPDATE
              tbl_misc m, tbl_fund f
            SET
              f.cash_on_hand = f.cash_on_hand + m.amount
            WHERE
              m.region = f.region AND m.mid = {$misc['mid']}
          ");

          // Insert history
          $misc['status'] = 5;
          $misc['uid'] = $_SESSION['uid'];
          return $this->db->insert('tbl_misc_expense_history', $misc);
        }

}
