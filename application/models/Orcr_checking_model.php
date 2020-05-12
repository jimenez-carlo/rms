<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking_model extends CI_Model{

        public $status = array(
        	0 => 'New',
        	1 => 'Incomplete',
        	2 => 'Done',
        );

        public $sales_type = array(
        	0 => 'Brand New (Cash)',
        	1 => 'Brand New (Installment)',
        );

        public function __construct()
        {
        	parent::__construct();
                if ($_SESSION['company'] != 8) {
                  $this->companyQry = ' AND t.company != 8';
                  $this->andSalesCompany = ' AND s.company != 8';
                } else {
                  $this->region  = $this->mdi_region;
                  $this->company = $this->mdi;
                  $this->companyQry = ' AND t.company = 8';
                  $this->andSalesCompany = ' AND s.company = 8';
                }
        }

        public function get_ca_for_checking()
        {
          return $this->db->query("
            SELECT
              id, reference, region, budget_type
            FROM (
              (SELECT
                v.vid AS id, v.reference, s.region, 'CA' AS budget_type
              FROM
                tbl_voucher v
              INNER JOIN
                tbl_sales s ON v.vid = s.voucher AND v.vid = s.fund
              WHERE
                s.status = 4 $this->andSalesCompany AND v.vid IS NOT NULL
              GROUP BY
                v.vid, s.region
              ORDER BY
                v.reference DESC )
              UNION
              (SELECT
                l.lpid AS id, l.reference, s.region, 'EPP' as budget_type
              FROM
                tbl_lto_payment l
              INNER JOIN
                tbl_sales s ON l.lpid = s.lto_payment
              WHERE
                s.status = 4 $this->andSalesCompany AND l.lpid IS NOT NULL
              GROUP BY
                l.lpid, s.region
              ORDER BY
                l.lpid DESC )
            ) AS result
            ORDER BY region
          ")->result_array();

          //$this->db->select('v.vid, v.reference');
          //$this->db->from('tbl_voucher v');
          //$this->db->join('tbl_sales s','v.vid = s.voucher AND v.vid = s.fund', 'inner');
          //$this->db->where('s.status = 4 '.$this->andSalesCompany.' AND v.vid IS NOT NULL');
          //$this->db->group_by(array('v.vid', 's.region'));
          //$this->db->order_by('s.region ASC, v.reference DESC');
          //return $this->db->get()->result_array();
        }

        public function get_list_for_checking($date)
        {
                if($date != "") $date = " and left(post_date,10) = '".date('Y-m-d')."' ";
                $result = $this->db->query("
                  select t.* from tbl_topsheet t
                  inner join tbl_sales s on topsheet = tid and batch = 0
                  where t.status < 3
                  and da_reason <= 0
                  ".$date."
                  ".$this->companyQry."
                  group by tid
                  order by date desc
                  limit 1000
                ")->result_object();

                foreach ($result as $key => $topsheet)
                {
                        $topsheet->region = $this->region[$topsheet->region];
                        $topsheet->company = $this->company[$topsheet->company];
                        $topsheet->status = $this->status[$topsheet->status];
                        $topsheet->date = substr($topsheet->date, 0, 10);
                        $topsheet->alert = $this->db->query("
                          select count(*) as count from tbl_sales where acct_status = 2 and topsheet = ".$topsheet->tid
                        )->row()->count;
                        $result[$key] = $topsheet;
                }

                return $result;
        }

        public function get_sales($data)
        {

          if (isset($data['CA'])) {
            $where_param = " AND vid = {$data['CA']} ";

            // This condition is for ORCR Checking Preview Summary

            $select_param = <<<SEL
              v.vid, v.fund, SUBSTR(v.date, 1, 10) AS date, v.reference,
              v.voucher_no, v.dm_no, v.amount, v.transfer_date,
              v.process_date, v.offline, v.status AS voucher_status, v.process_timestamp,
              v.transfer_timestamp
SEL;
            $table_param = <<<TBL
              LEFT JOIN tbl_voucher v ON s.voucher = v.vid
              LEFT JOIN tbl_fund f ON v.fund = f.fid
              LEFT JOIN tbl_region r ON f.region = r.rid
              LEFT JOIN tbl_company c ON v.company = c.cid
TBL;
            $groupby_param = <<<GBY
              v.vid , v.fund, v.date, v.reference, v.voucher_no, v.dm_no,
              v.amount, v.transfer_date, v.process_date, v.offline, v.status,
              v.process_timestamp, v.transfer_timestamp, r.region, v.company
GBY;
          }

          if (isset($data['EPP'])) {
            $where_param = " AND lp.lpid = {$data['EPP']} ";

            $select_param = <<<SEL
              lp.*, lp.ref_date AS date
SEL;
            $table_param = <<<VOC
              LEFT JOIN tbl_lto_payment lp ON s.lto_payment = lp.lpid
              LEFT JOIN tbl_region r ON lp.region = r.rid
              LEFT JOIN tbl_company c ON lp.company = c.cid
VOC;
            $groupby_param = <<<GBY
              lp.lpid, r.region, lp.company;
GBY;
          }

          if (isset($data['sid'])) {
            $where_param .=  ' AND s.sid IN ('.implode(',', $data['sid']).') ';
          }

          $sql = <<<SQL
            SELECT
              {$select_param},
              CASE r.rid
                WHEN 1  THEN 'NCR'
                WHEN 2  THEN 'R1'
                WHEN 3  THEN 'R2'
                WHEN 4  THEN 'R3'
                WHEN 5  THEN 'R4A'
                WHEN 6  THEN 'R4B'
                WHEN 7  THEN 'R5'
                WHEN 8  THEN 'R6'
                WHEN 9  THEN 'R7'
                WHEN 10 THEN 'R8'
                WHEN 11 THEN 'IX'
                WHEN 12 THEN 'X'
                WHEN 13 THEN 'XI'
                WHEN 14 THEN 'XII'
                WHEN 15 THEN 'XIII'
              END AS region_initial,
              r.region, c.company_code AS company,
              CONCAT(
                '[',
                  GROUP_CONCAT(
                    JSON_OBJECT(
                      'sid', s.sid, 'engine_no', e.engine_no,
                      'bcode', s.bcode, 'bname', s.bname,
                      'date_sold', SUBSTR(s.date_sold, 1, 10), 'sales_type', st.sales_type, 'registration_type', s.registration_type,
                      'si_no', s.si_no, 'ar_no', s.ar_no, 'amount', s.amount,
                      'insurance', s.insurance, 'registration', s.registration, 'status', ss.status_name,
                      'disapprove',
                      CASE
                        WHEN sub.subid IS NOT NULL AND ss.status_name = 'Registered' THEN 'For Sap Uploading'
                        WHEN ss.status_name = 'Liquidated' THEN 'Done'
                        ELSE sts.status_name
                      END,
                      'selectable', CASE WHEN ss.status_name = 'Registered' AND sub.subid IS NULL THEN true ELSE false END
                    )
                    ORDER BY FIELD(s.status, 4, 5, 3, 2, 1, 0), FIELD(s.da_reason, 0, 11, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
                    SEPARATOR ','
                  ),
                  ']'
              ) AS sales
            FROM tbl_sales s
            {$table_param}
            LEFT JOIN tbl_status ss ON s.status = ss.status_id AND ss.status_type = 'SALES'
            LEFT JOIN tbl_sap_upload_sales_batch sub ON s.sid = sub.sid
            LEFT JOIN tbl_sales_type st ON s.sales_type = st.stid
            LEFT JOIN tbl_status sts ON s.da_reason = sts.status_id AND sts.status_type = 'DA'
            LEFT JOIN tbl_engine e ON e.eid = s.engine
            WHERE 1=1 {$where_param}
            GROUP BY {$groupby_param}
SQL;
          $this->db->simple_query("SET SESSION group_concat_max_len=18446744073709551615");
          $sales_result = $this->db->query($sql)->row_array();

          return $sales_result;
        }

        public function get_misc_expense($data)
        {
          if (isset($data['CA'])) {
            $where_param = "v.vid = {$data['CA']}";
            $groupby_param = "v.vid";
          }

          if (isset($data['EPP'])) {
            $where_param = "lp.lpid = {$data['EPP']}";
            $groupby_param = "lp.lpid";
          }

          $sql = <<<SQL
            SELECT
              -- v.reference,
              CONCAT(
                '[',
                GROUP_CONCAT(
                  JSON_OBJECT(
                    'mid', m.mid, 'region', m.region,
                    'date', m.date, 'or_no', m.or_no,
                    'or_date', SUBSTR(m.or_date, 1, 10),
                    'amount', FORMAT(m.amount, 2), 'type', mt.type,
                    'remarks', mxh1.remarks, 'status', sts.status_name,
                    'other', m.other, 'topsheet', m.topsheet,
                    'batch', m.batch, 'ca_ref', m.ca_ref
                    )
                  ),
                ']'
              ) AS misc_expense
            FROM tbl_misc m
            LEFT JOIN tbl_voucher v ON v.vid = m.ca_ref
            LEFT JOIN tbl_fund f ON v.fund = f.fid
            LEFT JOIN tbl_region r ON f.region = r.rid
            LEFT JOIN tbl_company c ON v.company = c.cid
            LEFT JOIN tbl_misc_type mt ON m.type = mt.mtid
            LEFT JOIN tbl_misc_expense_history mxh1 ON mxh1.mid = m.mid
            LEFT JOIN tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
            LEFT JOIN tbl_status sts ON mxh1.status = sts.status_id AND sts.status_type = 'MISC_EXP'
            WHERE {$where_param} AND (sts.status_id IN (2, 3, 4, 5, 6) OR m.mid IS NULL) AND mxh2.mid IS NULL
            GROUP BY {$groupby_param}
SQL;
          $this->db->simple_query("SET SESSION group_concat_max_len=18446744073709551615");
          $misc_expense_result =  $this->db->query($sql)->row_array();

          return $misc_expense_result['misc_expense'];
        }

        public function load_ca($data)
        {

          $vid = $data['CA'];
          $sid = (!empty($data['sid'])) ? ' AND s.sid IN ('.implode(',', $data['sid']).')' : '';
          $mid = (!empty($data['mid'])) ? ' AND m.mid in ('.implode(',', $data['mid']).')' : '';
          // 'Brand New (Cash)',
          // 'Brand New (Installment)'
          $sql = <<<SQL
            SELECT
              vid, fund, date,
              reference, voucher_no, dm_no,
              amount, transfer_date, process_date,
              offline, voucher_status, process_timestamp,
              transfer_timestamp, region, company,
              CONCAT('[', GROUP_CONCAT(DISTINCT misc_expense ORDER BY mid SEPARATOR ','), ']') AS misc_expense, sales
            FROM (
              SELECT
                v.vid, v.fund, SUBSTR(v.date, 1, 10) AS date, v.reference,
                v.voucher_no, v.dm_no, v.amount, v.transfer_date,
                v.process_date, v.offline, v.status AS voucher_status, v.process_timestamp,
                v.transfer_timestamp, r.region, c.company_code AS company, m.mid,
                CONCAT(
                  '[',
                    GROUP_CONCAT(
                      JSON_OBJECT(
                        'sid', s.sid, 'engine_no', e.engine_no,
                        'bcode', s.bcode, 'bname', s.bname,
                        'date_sold', SUBSTR(s.date_sold, 1, 10), 'sales_type', st.sales_type, 'registration_type', s.registration_type,
                        'si_no', s.si_no, 'ar_no', s.ar_no, 'amount', s.amount,
                        'insurance', s.insurance, 'registration', s.registration,
                        'status', ss.status, 'disapprove', ds.da_status
                      )
                      ORDER BY FIELD(s.status, 4, 5, 3, 2, 1, 0), FIELD(s.da_reason, 0, 11, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
                      SEPARATOR ','
                    ),
                    ']'
                ) AS sales,
                JSON_OBJECT(
                  'mid', m.mid, 'region', m.region, 'date', m.date,
                  'or_no', m.or_no, 'or_date', SUBSTR(m.or_date, 1, 10), 'amount', m.amount,
                  'type', mt.type, 'remarks', mxh1.remarks, 'status', ms.status_name,
                  'other', m.other, 'topsheet', m.topsheet, 'batch', m.batch, 'ca_ref', m.ca_ref
                ) AS misc_expense
              FROM tbl_voucher v
              LEFT JOIN tbl_fund f ON v.fund = f.fid
              LEFT JOIN tbl_region r ON f.region = r.rid
              LEFT JOIN tbl_company c ON v.company = c.cid
              LEFT JOIN tbl_misc m ON v.vid = m.ca_ref
              LEFT JOIN tbl_misc_type mt ON m.type = mt.mtid
              LEFT JOIN tbl_misc_expense_history mxh1 ON mxh1.mid = m.mid
              LEFT JOIN tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
              LEFT JOIN tbl_misc_status ms ON mxh1.status = ms.id
              LEFT JOIN tbl_sales s ON s.voucher = v.vid
              LEFT JOIN tbl_sales_status ss ON s.status = ss.ssid
              LEFT JOIN tbl_sales_type st ON s.sales_type = st.stid
              LEFT JOIN tbl_da_status ds ON s.da_reason = ds.id
              LEFT JOIN tbl_engine e ON e.eid = s.engine
              WHERE v.vid = $vid $sid $mid AND (ms.id IN (2,5,6) OR m.mid IS NULL) AND mxh2.mid IS NULL
              GROUP BY v.vid , v.fund , v.date , v.reference , v.voucher_no , v.dm_no , v.amount , v.transfer_date,
                       v.process_date , v.offline , v.status , v.process_timestamp , v.transfer_timestamp , r.region,
                       v.company, m.mid, mxh1.id
            ) AS first_select
            GROUP BY vid, fund, date, reference, voucher_no, dm_no, amount, transfer_date, process_date,
                     offline, voucher_status, process_timestamp, transfer_timestamp, company, sales
SQL;
          $this->db->simple_query("SET SESSION group_concat_max_len=18446744073709551615");
          $result =  $this->db->query($sql)->result_array();

          return $result[0];
        }

        public function load_topsheet($data)
        {
        	$sid = (!empty($data['sid'])) ? ' and sid in ('.implode(',', $data['sid']).')' : '';
        	$mid = (!empty($data['mid'])) ? ' and mid in ('.implode(',', $data['mid']).')' : '';
        	$mid = (!empty($data['summary']) && empty($mid)) ? ' and 1 = 2' : $mid;

        	$topsheet = $this->db->query("SELECT * FROM tbl_topsheet WHERE tid = ".$data['tid'])->row();
        	$topsheet->region  = $this->region[$topsheet->region];
        	$topsheet->company = $this->company[$topsheet->company];
        	$topsheet->date = substr($topsheet->date, 0, 10);

        	$topsheet->total_expense = 0;
        	$topsheet->total_credit = 0;
        	$topsheet->check = 0;
                $topsheet->sales = $this->db->query("
                  SELECT
                    *,
                    CASE
                      WHEN registration_type = 'Free Registration' THEN si_no
        	      WHEN registration_type = 'With Regn. Subsidy' THEN concat(si_no, '<br>', ar_no)
                      ELSE ar_no
                    END as ar_no
                    ,tbl_voucher.reference
        	  FROM tbl_sales
        	  INNER JOIN tbl_engine on engine = eid
        	  INNER JOIN tbl_customer on customer = cid
        	  INNER JOIN tbl_voucher on vid = tbl_sales.fund
        	  WHERE topsheet = ".$topsheet->tid." AND batch = 0 AND da_reason <= 0 ".$sid."
                  ORDER by reference DESC, bcode ASC
                ")->result_object();
        	foreach ($topsheet->sales as $key => $sales)
        	{
        		$sales->date_sold = substr($sales->date_sold, 0, 10);
        		$sales->sales_type = $this->sales_type[$sales->sales_type];
        		$topsheet->sales[$key] = $sales;

        		$topsheet->total_expense += ($sales->registration + $sales->tip);
        		$topsheet->total_credit += $sales->amount;
        	}

        	$this->load->model('Expense_model', 'misc');
                $topsheet->misc = $this->db->query("
                        SELECT
                          *
        		  ,LEFT(or_date, 10) as or_date
                          ,v.reference
                          ,CASE
                            m.status
                            WHEN 0 THEN 'For Approval'
        	            WHEN 1 THEN 'Rejected'
        	            WHEN 2 THEN 'Approved'
        	            WHEN 3 THEN 'For Liquidation'
        	            WHEN 4 THEN 'Liquidated'
                          END AS status
                        FROM
                          tbl_misc m
                        INNER JOIN
                          tbl_voucher v ON v.vid = m.ca_ref
                        WHERE
                          topsheet = ".$topsheet->tid."
                        AND batch = 0 ".$mid
                )->result_object();
        	foreach ($topsheet->misc as $misc)
        	{
        		$misc->or_date = substr($misc->or_date, 0, 10);
        		$misc->type = $this->misc->type[$misc->type];
        	}

        	// batch for miscellaneous
        	$topsheet->batch = $this->db->query("select * from tbl_batch
        		where status = 0 and topsheet = ".$topsheet->tid."
        		and left(post_date, 10) = '".date('Y-m-d')."'")->row();

        	return $topsheet;
        }

        public function sales_attachment($sid)
        {
                $sales = $this->db->query("select *,
                	case when registration_type = 'Free Registration' then si_no
                		when registration_type = 'With Regn. Subsidy' then concat(si_no, '<br>', ar_no)
                		else ar_no end as ar_no
                	from tbl_sales
                	inner join tbl_customer on cid = customer
                	inner join tbl_engine on eid = engine
                	where sid = ".$sid)->row();

                $this->load->helper('directory');
                $folder = './rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/';

                if (is_dir($folder)) {
                	$sales->files = directory_map($folder, 1);
                }
                else {
                	$sales->files = null;
                }

                return $sales;
        }

        public function misc_attachment($mid)
        {
                $misc = $this->db->query("
                  SELECT
                    m.mid, m.region, m.date, m.or_no, m.or_date,
                    FORMAT(m.amount, 2) AS amount, m.offline, m.other, m.topsheet, m.batch,
                    m.ca_ref, mxh1.*, mt.type, v.reference
                  FROM
                    tbl_misc m
                  INNER JOIN
                    tbl_voucher v ON v.vid = m.ca_ref
                  INNER JOIN
                    tbl_misc_type mt ON m.type = mt.mtid
                  INNER JOIN
                    tbl_misc_expense_history mxh1 USING (mid)
                  LEFT JOIN
                    tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
                  WHERE
                    m.mid = $mid AND mxh2.id IS NULL
                ")->row();

        	$this->load->helper('directory');
        	$folder = './rms_dir/misc/'.$misc->mid.'/';

        	if (is_dir($folder)) {
        		$misc->files = directory_map($folder, 1);
        	}
        	else {
        		$misc->files = null;
        	}

        	return $misc;
        }

        public function check_sales($sid)
        {
        	$sales = $this->db->query('select * from tbl_sales
        				inner join tbl_engine on engine = eid
        				where sid = '.$sid)->row();

        	$batch = $this->db->query("select * from tbl_batch
        				where status = 0 and topsheet = ".$sales->topsheet."
        				and left(post_date, 10) = '".date('Y-m-d')."'")->row();
        	if (empty($batch))
        	{
        		// generate batch
        		$topsheet = $this->db->query("select * from tbl_topsheet
        			where tid = ".$sales->topsheet)->row();
        		$count = $this->db->query("select count(*)+1 as count from tbl_batch
        			where topsheet = ".$sales->topsheet)->row()->count;

        		$batch = new Stdclass();
        		$batch->topsheet = $sales->topsheet;
        		$batch->trans_no = $topsheet->trans_no.'-B'.$count;

        		$this->db->insert('tbl_batch', $batch);
        		$batch->bid = $this->db->insert_id();
        	}

        	// save to batch, remove alert
        	$this->db->query("update tbl_sales set batch = ".$batch->bid.", acct_status = 3 where sid = ".$sid);

                $this->load->model('Login_model', 'login');
        	$this->login->saveLog('marked ORCR ['.$sid.'] with Engine # '.$sales->engine_no.' as checked');

        	// for message
        	// $sales->trans_no = $batch->trans_no;
        	// return $sales;
        }

        public function check_misc($mid, $tid)
        {
        	$batch = $this->db->query("select * from tbl_batch
        				where status = 0 and topsheet = ".$tid."
        				and left(post_date, 10) = '".date('Y-m-d')."'")->row();
        	if (!empty($batch))
        	{
        		// save to batch, remove alert
        		$this->db->query("update tbl_misc set batch = ".$batch->bid." where mid = ".$mid);
        	}

        }

        public function hold_sales($param)
        {
        	$sales = $this->db->query('select * from tbl_sales
        				inner join tbl_engine on engine = eid
        				inner join tbl_customer on customer = cid
        				where sid = '.$param->sid)->row();
        	$sales->reason = $param->reason;
        	$sales->remarks = $param->remarks;

        	$this->db->query("update tbl_sales set acct_status = 1 where sid = ".$sales->sid);

        	foreach ($sales->reason as $value)
        	{
        		$reason = new Stdclass();
        		$reason->topsheet = $sales->topsheet;
        		$reason->sales = $sales->sid;
        		$reason->reason = $value;
        		$this->db->insert('tbl_topsheet_reason', $reason);
        	}

        if (in_array('0', $sales->reason))
        {
        		$remarks = new Stdclass();
        		$remarks->topsheet = $sales->topsheet;
        		$remarks->sales = $sales->sid;
        		$remarks->user = $_SESSION['uid'];
        		$remarks->remarks = $sales->remarks;
        		$this->db->insert('tbl_topsheet_remarks', $remarks);
        }

        	$this->load->model('Login_model', 'login');
        	$this->login->saveLog('marked ORCR ['.$sales->sid.'] with Engine # '.$sales->engine_no.' as hold with remarks ['.$sales->remarks.']');

        	return $sales;
        }

        public function hold_misc($param)
        {
        	$topsheet = $this->db->query('select * from tbl_topsheet where tid = '.$param->tid)->row();
        	$topsheet->reason = $param->reason;
        	$topsheet->remarks = $param->remarks;

        	$this->db->query("update tbl_topsheet set misc_status = 1 where tid = ".$topsheet->tid);

        	foreach ($topsheet->reason as $value)
        	{
        		$reason = new Stdclass();
        		$reason->topsheet = $topsheet->tid;
        		$reason->sales = 0;
        		$reason->reason = $value;
        		$this->db->insert('tbl_topsheet_reason', $reason);
        	}

                if (in_array('0', $topsheet->reason))
                {
                        $remarks = new Stdclass();
                        $remarks->topsheet = $topsheet->tid;
                        $remarks->sales = 0;
                        $remarks->user = $_SESSION['uid'];
                        $remarks->remarks = $topsheet->remarks;
                        $this->db->insert('tbl_topsheet_remarks', $remarks);
                }

        	$this->load->model('Login_model', 'login');
        	$this->login->saveLog('marked ORCR ['.$sales->sid.'] with Engine # '.$sales->engine_no.' as hold with remarks ['.$sales->remarks.']');

        	return $topsheet;
        }

        public function sap_upload_process_all($region, $company, $sales, $misc_expenses) {
          switch ($company) {
            case 'MNC':
              $slug = 'B1';
              break;

            case 'HPTI':
              $slug = 'B2';
              break;

            case 'MTI':
              $slug = 'B3';
              break;

            case 'MDI':
              $slug = 'B4';
              break;
          }

          $batch_name = 'T-'.$region.'-'.date('ymd').'-'.$slug;
          $batch = $this->db->query("
            SELECT
              a.*, b.batch_count
            FROM
              tbl_sap_upload_batch a
            INNER JOIN (
              SELECT
                MAX(subid) AS subid, COUNT(*) AS batch_count
              FROM
            	tbl_sap_upload_batch
              WHERE
            	trans_no LIKE '".$batch_name."%'
            ) b ON a.subid = b.subid
          ")->row_array();

          if ($batch['is_uploaded'] === "0") {
            $subid = $batch['subid'];
          } else {
            $data['subid'] = NULL;

            if ($batch === NULL) {
              $data['trans_no'] =  $batch_name.'-1';
            } elseif($batch['is_uploaded'] === "1") {
              $new_batch_count = $batch['batch_count']+1;
              $data['trans_no'] = $batch_name.'-'.$new_batch_count;
            }

            $this->db->insert('tbl_sap_upload_batch', $data);
            $subid = $this->db->insert_id();
          }

          foreach ($sales as $sale) {
            $sales_batch = array(
              'subid' => $subid,
              'sid' => $sale
            );
            $this->db->insert('tbl_sap_upload_sales_batch', $sales_batch);
          }

          if (isset($misc_expenses)) {
            foreach ($misc_expenses as $misc_expense) {
              $this->db->query("
                INSERT INTO
                  tbl_misc_expense_history(id, mid, remarks, status, uid)
                VALUES
                  (NULL, ".$misc_expense.", NULL, 3, ".$_SESSION['uid'].")"
              );
            }
          }

          return $batch_name;
        }
}
