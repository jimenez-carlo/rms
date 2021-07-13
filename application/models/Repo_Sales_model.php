<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_Sales_model extends CI_Model{

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
        public function resolve_list($param)
        {
                $branch = (empty($param->branch))  ? "" : " AND s.bcode = '$param->branch'";
                $da_status = 'AND s.da_id > 0 AND s.da_id != 11';
                // $condition = ($this->session->company_code === 'MDI') ? 's.bcode >= 8000' : 's.bcode < 8000';
                $condition = "";
                if (in_array($this->session->position, array(72, 73, 83))) { 
                  $condition = 'AND s.bcode = '.$this->session->branch_code;
                  $branch = '';
                }

                $result = $this->db->query("
                  SELECT
                    s.repo_sales_id,reg.repo_registration_id,
                    s.date_sold,s.bcode,s.bname, e.engine_no, c.last_name,c.first_name,c.middle_name, t.trans_no,
                    type.status_name as reg_type,
                    s.ar_amt,s.ar_num,
                    reg.orcr_amt,reg.renewal_amt,transfer_amt,
                    UPPER(st.status_name) as da
                  FROM
                    tbl_repo_sales s
                  INNER JOIN
                    tbl_engine e ON s.engine_id = e.eid
                  INNER JOIN
                    tbl_customer c ON s.customer_id = cid
                  LEFT JOIN
                    tbl_repo_topsheet t ON s.top_sheet_id = t.tid
                  INNER JOIN
                    tbl_status st ON s.da_id = st.status_id and st.status_type = 'REPO_DA'
                  INNER JOIN
                    tbl_status type ON s.repo_reg_type = type.status_id AND type.status_type = 'REPO_REG_STATUS'
                  INNER JOIN
                    tbl_repo_registration reg on s.repo_registration_id = reg.repo_registration_id 
                  WHERE
                  1 = 1 AND s.da_id IN(3)
                    {$condition} {$branch} {$da_status}
                  ORDER BY s.bcode")->result_object();
                return $result;
        }
        public function disapprove_list($param)
        {
                $branch = (empty($param->branch))  ? "" : " AND s.bcode = '$param->branch'";
                $da_status = 'AND s.da_id > 0 AND s.da_id != 11';
                // $condition = ($this->session->company_code === 'MDI') ? 's.bcode >= 8000' : 's.bcode < 8000';
                $condition = "";
                if (in_array($this->session->position, array(72, 73, 83))) { 
                  $condition = 'AND s.bcode = '.$this->session->branch_code;
                  $branch = '';
                }

                $result = $this->db->query("
                  SELECT
                    s.repo_sales_id,reg.repo_registration_id,
                    s.date_sold,s.bcode,s.bname, e.engine_no, c.last_name,c.first_name,c.middle_name, t.trans_no,
                    type.status_name as reg_type,
                    s.ar_amt,s.ar_num,
                    reg.orcr_amt,reg.renewal_amt,transfer_amt,
                    UPPER(st.status_name) as da
                  FROM
                    tbl_repo_sales s
                  INNER JOIN
                    tbl_engine e ON s.engine_id = e.eid
                  INNER JOIN
                    tbl_customer c ON s.customer_id = cid
                  LEFT JOIN
                    tbl_repo_topsheet t ON s.top_sheet_id = t.tid
                  INNER JOIN
                    tbl_status st ON s.da_id = st.status_id and st.status_type = 'REPO_DA'
                  INNER JOIN
                    tbl_status type ON s.repo_reg_type = type.status_id AND type.status_type = 'REPO_REG_STATUS'
                  INNER JOIN
                    tbl_repo_registration reg on s.repo_registration_id = reg.repo_registration_id 
                  WHERE
                  1 = 1 AND s.da_id IN(1,2)
                    {$condition} {$branch} {$da_status}
                  ORDER BY s.bcode")->result_object();
                return $result;
        }


}
