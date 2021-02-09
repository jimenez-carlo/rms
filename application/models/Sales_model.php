<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model{

        public $sales_type = array(
                0 => 'Brand New (Cash)',
                1 => 'Brand New (Installment)'
        );

        public $status = array(
                0 => 'Ongoing Transmittal',
                1 => 'LTO Rejected',
                2 => 'LTO Pending',
                3 => 'NRU Paid',
                4 => 'Registered',
                5 => 'Liquidated',
        );

        public $lto_reason = array(
                0 => 'N/A',
                1 => 'Affidavit of Change Body Type',
                2 => 'Closed Item',
                3 => 'COC Does Not Exist',
                4 => 'DIY Reject',
                5 => 'Expired Accre',
                6 => 'Expired Insurance',
                7 => 'Lost Docs',
                8 => 'Need Affidavit of Lost Docs',
                9 => 'No Date on SI',
                10 => 'No Sales Report',
                11 => 'No TIN #',
                12 => 'Self Registration',
                13 => 'Unreadable SI',
                14 => 'Wrong CSR Attached',
        );

        public $topsheet_region;

        public function __construct()
        {
                parent::__construct();
                $this->load->helper('directory');
                $this->load->model('Login_model', 'login');
                $this->load->model('Transmittal_model', 'transmittal');
                $this->load->model('Fund_model', 'fund');

                $this->topsheet_region = $this->reg_code;
                $this->company = ($_SESSION['company'] != 8) ? ' company != 8 ' : ' company = 8 ';
        }

        public function dd_branches() {
          $result = $this->db->query(
            'SELECT DISTINCT bcode, bname
              FROM tbl_sales
             WHERE '.$this->company.'ORDER BY bcode'
          )->result_object();

          $branches = array();
          foreach ($result as $branch) {
            $branches[$branch->bcode] = $branch->bcode.' '.$branch->bname;
          }
          return $branches;
        }

        public function dd_status()
        {
                $id = $this->db->query("select sid from tbl_sales
                        left join tbl_engine on engine=eid
                        where engine_no='".$engine_no."'")->row();
                if(!empty($id)) $id = $id->sid;
                return $id;
        }

        public function get_id_by_engine($engine_no)
        {
                $id = $this->db->query("select sid from tbl_sales
                        left join tbl_engine on engine=eid
                        where engine_no='".$engine_no."'")->row();
                if(!empty($id)) $id = $id->sid;
                return $id;
        }

        public function get_customer($where)
        {
          return $this->db
            ->select()
            ->from('tbl_customer c')
            ->where($where)
            ->get()->row_array();
        }

        public function get_sales($select, $where)
        {
                $result = $this->db->select($select)
                                   ->from('tbl_sales s')
                                   ->join('tbl_engine e', 'e.eid = s.engine', 'inner')
                                   ->join('tbl_customer c', 'c.cid = s.customer', 'inner')
                                   ->where($where);
                return $result->get()->row_array();
        }

        public function get_sales_by_transmittal($tid)
        {
                $sales = $this->db->query("select * from tbl_sales
                                inner join tbl_transmittal_sales on sales=sid
                                where transmittal= ".$tid)->result_object();
                foreach ($sales as $key => $sale) {
                        $sales[$key]->remarks = $this->db->query("select * from tbl_transmittal_remarks
                                where transmittal = ".$tid." and sales = ".$sale->sid)->result_object();
                }
                return $sales;
        }

        public function get_sales_by_topsheet($tid)
        {
                return $this->db->query("select * from tbl_sales
                                where topsheet = ".$tid)->result_object();
        }

        public function get_sales_by_branch_type($tid)
        {
                return $this->db->query("select bcode, sales_type, group_concat(sid) as sid from tbl_sales
                                        where topsheet = ".$tid."
                                        group by bcode, sales_type")->result_object();
        }

        public function get_sr_with($date_sold)
        {
                $branches = $this->cmc->get_region_branches($_SESSION['region_id']);
                $sales = $this->db->query("select * from tbl_sales
                        left join tbl_engine on engine = eid
                        left join tbl_customer on customer = cid
                        where registration_type = 'Self Registration' and
                        transmittal_date IS NULL and
                        branch in (".$branches.") and
                        LEFT(date_sold,10) like '$date_sold%'
                        limit 1000")->result_object();

                if (!empty($sales))
                {
                        foreach ($sales as $key => $sale) {
                                $sales[$key]->status = $this->status[$sale->status];
                                $sales[$key]->branch = $this->cmc->get_branch($sale->branch);
                        }
                }

                return $sales;
        }

        public function get_sr_without($date_sold)
        {
                $branches = $this->cmc->get_region_branches($_SESSION['region_id']);
                $sales = $this->db->query("select * from tbl_sales
                        left join tbl_engine on engine = eid
                        left join tbl_customer on customer = cid
                        where registration_type = 'Self Registration' and
                        transmittal_date IS NOT NULL and
                        branch in (".$branches.") and
                        LEFT(date_sold,10) like '$date_sold%'
                        limit 1000")->result_object();

                if (!empty($sales))
                {
                        foreach ($sales as $key => $sale) {
                                $sales[$key]->status = $this->status[$sale->status];
                                $sales[$key]->branch = $this->cmc->get_branch($sale->branch);
                        }
                }

                return $sales;
        }

        // -- START HERE -- //

        public function load_sales($sid)
        {
                $this->load->model('Cmc_model', 'cmc');
                $this->load->model('Fund_model', 'fund');

                $sales = $this->db->query("
                  SELECT
                    s.*, e.*, c.*, p.plate_number,
                    CONCAT(
                      IFNULL(c.first_name,''), ' ',
                      IFNULL(c.middle_name,''), ' ',
                      IFNULL(c.last_name,'')
                    ) AS customer_name,
                    DATE_FORMAT(s.date_sold, '%Y-%m-%d') as date_sold
                  FROM tbl_sales s
                  INNER JOIN tbl_engine e ON s.engine = e.eid
                  INNER JOIN tbl_customer c ON s.customer = c.cid
                  LEFT JOIN tbl_plate p ON p.plate_id = e.plate_id
                  WHERE s.sid = ".$sid)->row();
                $sales->fund = $this->fund->get_company_cash($sales->region, $sales->company);
                $sales->sales_type = $this->sales_type[$sales->sales_type];
                $sales->status = $this->status[$sales->status];

                $sales->files = directory_map('./rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/', 1);
                return $sales;
        }

        public function customer_status_report($param)
        {
                $branch = (!empty($param->branch) && is_numeric($param->branch))
                        ? " AND s.bcode = '".$param->branch."'" : '';
                $status = (is_numeric($param->status))
                        ? ' AND s.status = '.$param->status : '';
                $name = (!empty($param->name))
                        ? " AND CONCAT(c.first_name, c.middle_name, c.last_name) LIKE '%".$param->name."%'" : '';
                $engine_no = (!empty($param->engine_no))
                        ? " AND e.engine_no REGEXP '".$param->engine_no."'" : '';

                $result = $this->db->query("
                  SELECT
                    s.sid, s.bcode, s.bname, s.file, DATE_FORMAT(s.date_sold, '%Y-%m-%d') AS date_sold,
                    e.*, c.*, st.status_name AS status, reject.status_name AS lto_reason
                  FROM tbl_sales s
                  INNER JOIN tbl_engine e ON s.engine = e.eid
                  INNER JOIN tbl_customer c ON s.customer = c.cid
                  INNER JOIN tbl_status st ON st.status_id = s.status AND st.status_type = 'SALES'
                  INNER JOIN tbl_status reject ON reject.status_id = s.lto_reason AND reject.status_type = 'LTO_REASON'
                  WHERE 1=1 ".$branch.$status.$name.$engine_no." AND ".$this->company."
                  ORDER BY sid DESC LIMIT 1000
                ")->result_object();

                return $result;
        }

        public function load_sales_by_engine($engine_no)
        {
                $sales = $this->db->query("select * from tbl_sales
                        inner join tbl_engine on engine = eid
                        inner join tbl_customer on customer = cid
                        where engine_no = '".$engine_no."'
                        limit 1000")->row();

                if (!empty($sales))
                {
                        $sales->edit = ($_SESSION['position'] == 108
                                && $sales->status == 3
                                && substr($sales->registration_date, 0, 10) == date('Y-m-d'));
                        $sales->status = $this->status[$sales->status];
                }

                return $sales;
        }

        public function save_lto_pending($sales)
        {
                $engine = $this->get_engine($sales->sid);

                if ($sales->status == 2)
                {
                        $sales->lto_reason = 0;
                        $sales->pending_date = date('Y-m-d H:i:s');

                        $this->login->saveLog('Marked sale ['.$sales->sid.'] with Engine # '.$engine.' as PENDING at LTO');
                }
                else
                {
                        $this->login->saveLog('Marked sale ['.$sales->sid.'] with Engine # '.$engine.' as REJECTED at LTO with reason: '.$this->lto_reason[$sales->lto_reason]);
                }

                $this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));
        }

        public function save_status($sid,$status)
        {
                $this->db->query("update tbl_sales set status = '".$status."' where sid = ".$sid);
        }

        public function save_registration($sales)
        {
                if (!empty($sales->plate_no)) {
                  $this->load->model('Plate_model', 'plate');
                  $plate_no_exist = $this->db->query("SELECT * FROM tbl_plate WHERE plate_number = '{$sales->plate_no}'")->result();
                  if (!$plate_no_exist) {
                    $this->plate->add_platenumber($sales->sid, $sales->plate_no, $sales->bcode);
                  }
                }

                $uid = $_SESSION['uid'] ?? 0;
                $this->db->query("
                  UPDATE
                    tbl_sales s, tbl_engine e
                  SET
                    s.status = {$sales->status},
                    s.registration = {$sales->registration},
                    s.penalty = {$sales->penalty},
                    s.is_penalty_for_ric = {$sales->is_penalty_for_ric},
                    s.tip = {$sales->tip},
                    s.cr_date = '{$sales->cr_date}',
                    s.cr_no = '{$sales->cr_no}',
                    s.file = {$sales->file},
                    s.registration_date = NOW(),
                    s.user = {$uid},
                    e.mvf_no = '{$sales->mvf_no}'
                  WHERE
                    s.sid = {$sales->sid} AND e.eid = s.engine
                ");

                $query = <<<SQL
                  SELECT
                    sid, bcode, bname, region,
                    company, registration, rerfo, eid, engine_no,
                    DATE_FORMAT(registration_date, '%Y-%m-%d') AS registration_date
                  FROM
                    tbl_sales
                  INNER JOIN
                    tbl_engine ON engine = eid
                  WHERE
                    sid = {$sales->sid}
SQL;
                $sales = $this->db->query($query)->row();

                // rerfo
                if ($sales->rerfo == 0)
                {
                        $rerfo = $this->db->query("
                          SELECT
                            *
                          FROM
                            tbl_rerfo
                          WHERE bcode = ".$sales->bcode." AND date = '".$sales->registration_date."'"
                        )->row();

                        if (empty($rerfo))
                        {
                                $rerfo = new Stdclass();
                                $rerfo->region = $sales->region;
                                $rerfo->bcode = $sales->bcode;
                                $rerfo->bname = $sales->bname;
                                $rerfo->date = $sales->registration_date;
                                $rerfo->trans_no = 'R-'.$sales->bcode.'-'
                                        .substr($rerfo->date, 2, 2)
                                        .substr($rerfo->date, 5, 2)
                                        .substr($rerfo->date, 8, 2);
                                $this->db->insert('tbl_rerfo', $rerfo);
                                $rerfo->rid = $this->db->insert_id();
                        }

                        $sales->rerfo = $rerfo->rid;
                        $this->db->query("UPDATE tbl_sales SET rerfo = ".$sales->rerfo." WHERE sid = ".$sales->sid);
                }

                $this->login->saveLog('Saved Registration Expense [Php '.$sales->registration.'] for Engine # '.$sales->engine_no.' ['.$sales->sid.']');
        }

        public function get_engine($sid)
        {
                return $this->db->query("select engine_no from tbl_engine
                        inner join tbl_sales on engine = eid
                        where sid = ".$sid)->row()->engine_no;
        }

        public function search_engine($engine_no)
        {
                $sales = $this->db->query("select * from tbl_sales
                        inner join tbl_engine on engine = eid
                        where engine_no = '".$engine_no."'
                        and status = 3")->row();

                if (empty($sales)) return null;
                else return $sales->sid;
        }

        public function get_cr_no($sid)
        {
                return $this->db->query("select cr_no from tbl_sales where sid = ".$sid)->row()->cr_no;
        }

        public function update_acct_status($sid,$status)
        {
                $this->db->query("update tbl_sales set acct_status = $status where sid = $sid");
        }

        public function get_orcr_by_engine($engine_no)
        {
                $sales = $this->db->query("select * from tbl_sales
                        inner join tbl_engine on engine = eid
                        where engine_no = '".$engine_no."'")->row();

                // load files
                $this->load->helper('directory');
                $sales->files = directory_map('./rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/', 1);

                return $sales;
        }

        public function print_orcr($sid)
        {
                $sales = $this->db->query("select * from tbl_sales
                        inner join tbl_engine on engine = eid
                        where sid = ".$sid)->row();

                // load files
                $this->load->helper('directory');
                $sales->files = directory_map('./rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/', 1);

                return $sales;
        }

        public function overview()
        {
                $table = array();
                $table[1] = array(-1 => 'Pending in RRT');
                $table[2] = array(-1 => 'Completed in DIY');
                $table[3] = array(-1 => 'Registered');
                $table[4] = array(-1 => 'Self Registration');

                for ($i = 0; $i <= 10; $i++) {
                        $table[1][$i] = 0;
                        $table[2][$i] = 0;
                        $table[3][$i] = 0;
                        $table[4][$i] = 0;
                }

                $dev_rms = $this->load->database('dev_rms', TRUE);
                $result = $dev_rms->query("select
                                case rrt_class
                                        when 'NCR' then '1'
                                        when 'REGION 1' then '2'
                                        when 'REGION 2' then '3'
                                        when 'REGION 3' then '4'
                                        when 'REGION 4' then '5'
                                        when 'REGION 4 B' then '6'
                                        when 'REGION 5' then '7'
                                        when 'REGION 6' then '8'
                                        when 'REGION 7' then '9'
                                        when 'REGION 8' then '10'
                                        else 0
                                        end as region,
                                ifnull(sum(case when not regn_status = 'Self Registration' then 1 else 0 end), 0) as pending,
                                ifnull(sum(case when regn_status = 'Self Registration' then 1 else 0 end), 0) as self_reg
                        from customer_tbl
                        inner join rrt_reg_tbl r on branch_code = branch
                        inner join regn_status on engine_nr = engine_no
                        where transmittal_no is null
                        and rrt_class in ('NCR', 'REGION 1', 'REGION 2', 'REGION 3', 'REGION 4', 'REGION 4 B', 'REGION 5', 'REGION 6', 'REGION 7', 'REGION 8')
                        group by rrt_class
                        order by 1")->result_object();
                foreach ($result as $row) {
                        $table[1][0] += $row->pending;
                        $table[4][0] += $row->self_reg;
                        $table[1][$row->region] += $row->pending;
                        $table[4][$row->region] += $row->self_reg;
                }

                $result = $this->db->query("select region,
                        ifnull(sum(case when status < 2 and not registration_type = 'Self Registration' then 1 else 0 end), 0) as pending,
                        ifnull(sum(case when status between 2 and 3 then 1 else 0 end), 0) as diy,
                        ifnull(sum(case when status > 3 then 1 else 0 end), 0) as regn,
                        ifnull(sum(case when registration_type = 'Self Registration' then 1 else 0 end), 0) as self_reg
                        from tbl_sales
                        where region < 11
                        group by region
                        order by 1")->result_object();
                foreach ($result as $row) {
                        $table[1][0] += $row->pending;
                        $table[2][0] += $row->diy;
                        $table[3][0] += $row->regn;
                        $table[4][0] += $row->self_reg;
                        $table[1][$row->region] += $row->pending;
                        $table[2][$row->region] += $row->diy;
                        $table[3][$row->region] += $row->regn;
                        $table[4][$row->region] += $row->self_reg;
                }

                return $table;
        }
}
