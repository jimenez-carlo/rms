<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Repo_Misc_model extends CI_Model{

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

        public function load_list($param)
        {
                $branch = (empty($param->branch))  ? "" : " AND y.bcode = '$param->branch'";
                // $da_status = "AND s.da_reason > 0 AND s.da_reason != 11";

                // switch ($this->session->position_name) {
                //   case 'Accounts Payable Clerk':
                //   case 'RRT National Registration Manager':
                //     $condition = ($this->session->company_code === 'MDI') ? 's.bcode >= 8000' : 's.bcode < 8000';
                //     break;
                //   case 'RRT Supervisor':
                //   case 'RRT Branch Secretary':
                //     $condition = "s.region = '$param->region'";
                //     break;
                //   default:
                //     $condition = 's.bcode = '.$this->session->branch_code;
                //     $da_status = 'AND s.da_reason IN(2,3,10)';
                //     $branch = '';
                //     break;
                // }

                $where = "";
                switch ($_SESSION['position']) {
                        case 156:
                        case 109:
                        case 108: // if rrt, set region
                                break;
                        case 72:
                        case 73:
                        case 81: // if ccn, set branch
                        $where = "and x.region = '{$this->session->region_id}'";
                                break;
                }
                $result = $this->db->query(" SELECT x.mid,y.reference,UPPER(CONCAT(y.bcode,' ',y.bname)) as branch,UPPER(z.region) as region,x.date,x.or_no,x.amount,x.type from tbl_repo_misc x inner join tbl_repo_batch y on x.ca_ref = y.repo_batch_id inner join tbl_region z on x.region = z.rid where x.status_id = 5 {$where} {$branch}")->result_object();
                return $result;
        }

        


}
