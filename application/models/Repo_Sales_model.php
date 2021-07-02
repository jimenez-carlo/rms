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
                $da_status = 'AND s.da_id > 0 AND s.da_id != 11';
                switch ($this->session->position_name) {
                  case 'Accounts Payable Clerk':
                  case 'RRT National Registration Manager':
                    $condition = ($this->session->company_code === 'MDI') ? 's.bcode >= 8000' : 's.bcode < 8000';
                    break;
                  case 'RRT Supervisor':
                  case 'RRT Branch Secretary':
                    $condition = "1=1";//"s.region_id = '$param->region'";
                    break;
                  default:
                    $condition = 's.bcode = '.$this->session->branch_code;
                    $da_status = 'AND s.da_id IN(1,2)';
                    $branch = '';
                    break;
                }
                
                $result = $this->db->query("
                  SELECT
                    s.repo_sales_id,reg.repo_registration_id,
                    s.date_sold,s.bcode,s.bname, e.engine_no, c.last_name,c.first_name,c.middle_name, t.trans_no,
                    type.status_name as reg_type,
                    s.ar_amt,s.ar_num,
                    reg.orcr_amt,reg.renewal_amt,transfer_amt,
                    st.status_name as da
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
                  LEFT JOIN
                    tbl_sap_upload_sales_batch susb ON susb.sid = s.sid
                  WHERE
                    s.status = 4 AND s.da_reason = 11
                    AND susb.sid IS NULL {$this->and_company}
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
          $this->db->trans_start();
          //Return Cash On Hand
          $this->db->query("
            UPDATE
              tbl_misc m, tbl_fund f
            SET
              f.cash_on_hand = f.cash_on_hand + m.amount
            WHERE
              m.region = f.region AND m.mid = {$misc['mid']}
          ");

          $this->db->update(
            'tbl_misc',
            ['da_reason'=>$misc['da_reason']],
            ['mid'=>$misc['mid']]
          );

          // Insert history
          $misc_history = [
            'mid' => $misc['mid'],
            'remarks' => $misc['remarks'],
            'uid' => $_SESSION['uid'],
            'status' => 5
          ];
          $this->db->insert('tbl_misc_expense_history', $misc_history);

          $this->db->trans_complete();
          return $this->db->trans_status();
        }

}
