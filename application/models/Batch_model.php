<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Batch_model extends CI_Model{

	public function __construct()
	{
          parent::__construct();
          $this->load->model('Cmc_model', 'cmc');

          // Check the value of $this->region and $this->company in application/core/MY_Controller
          if ($_SESSION['company'] == 8) {
            $this->region     = $this->mdi_region;
            $this->company    = $this->mdi;
            $this->companyQry = ' AND s.company = 8';
          } else {
            $this->companyQry = ' AND s.company != 8';
          }
	}

	public function list_for_upload() {
          $result = $this->db->query("
            SELECT
              sub.subid,
              trans_no,
              SUBSTR(sub.date_created, 1, 10) AS post_date,
              r.region,
              c.company_code AS company,
              sub.is_uploaded,
              sub.download_date
              ,GROUP_CONCAT(DISTINCT m.mid separator ',') AS misc_expense_id
            FROM
              tbl_sap_upload_batch sub
            JOIN
              tbl_sap_upload_sales_batch USING (subid)
            JOIN
              tbl_sales s USING (sid)
            INNER JOIN
              tbl_region r ON s.region = r.rid
            INNER JOIN
              tbl_company c ON s.company = c.cid
            LEFT JOIN
              tbl_voucher v ON s.voucher = v.vid
            LEFT JOIN
              tbl_misc m ON v.vid = m.ca_ref
            LEFT JOIN
              tbl_misc_expense_history mxh1 ON mxh1.mid = m.mid
            LEFT JOIN
              tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
            LEFT JOIN
              tbl_status st ON mxh1.status = st.status_id AND st.status_type = 'MISC_EXP'
            WHERE
              sub.is_uploaded = 0 AND mxh2.id IS NULL $this->companyQry AND (st.status_name = 'For Liquidation' OR st.status_name IS NULL)
            GROUP BY subid, region, company
            ORDER BY sub.date_created DESC
            LIMIT 1000
          ")->result_object();

          return $result;
	}

	public function sap_upload($subid)
	{
          $sql1 = <<<QRY
            SELECT
              b.bid, b.topsheet, SUBSTR(b.post_date, 1, 10) AS batch_post_date, b.trans_no,
              b.doc_no, b.misc, b.status, b.download_date, t.tid, t.user,
              SUBSTR(t.post_date, 1, 10) AS topsheet_post_date, t.trans_no,
              t.date, t.meal, t.photocopy, t.transportation,
              t.others, t.others_specify, t.status, t.misc_status, t.print,
              t.transmittal, t.print_date, t.transmittal_date,
              f.acct_number AS account_key,
              CASE WHEN t.company = 2 THEN 6 ELSE t.company END AS bcode,
              CASE
                WHEN t.region = 1 THEN 'NCR'
                WHEN t.region = 2 THEN 'Region 1'
                WHEN t.region = 3 THEN 'Region 2'
                WHEN t.region = 4 THEN 'Region 3'
                WHEN t.region = 5 THEN 'Region 4A'
                WHEN t.region = 6 THEN 'Region 4B'
                WHEN t.region = 7 THEN 'Region 5'
                WHEN t.region = 8 THEN 'Region 6'
                WHEN t.region = 9 THEN 'Region 7'
                WHEN t.region = 10 THEN 'Region 8'
                WHEN t.region = 11 THEN 'Region IX'
                WHEN t.region = 12 THEN 'Region X'
                WHEN t.region = 13 THEN 'Region XI'
                WHEN t.region = 14 THEN 'Region XII'
                WHEN t.region = 15 THEN 'Region XIII'
              END AS region,
              CASE
                WHEN t.company = 1 THEN 'MNC'
                WHEN t.company = 2 THEN 'MTI'
                WHEN t.company = 6 THEN 'MTI'
                WHEN t.company = 3 THEN 'HPTI'
                WHEN t.company = 8 THEN 'MDI'
              END AS company
             FROM
               tbl_batch b
                   INNER JOIN
               tbl_topsheet t ON b.topsheet = t.tid
                  INNER JOIN
               tbl_fund f ON t.region = f.region
               WHERE
                 bid = $subid
QRY;

              $sql2 = <<<QRY
                SELECT
                  DATE_FORMAT(s.date_sold, '%m/%d/%Y') AS post_date,
                  CASE c.company_code
                    WHEN 'MNC'  THEN '1000'
                    WHEN 'MTI'  THEN '6000'
                    WHEN 'HPTI' THEN '3000'
                    WHEN 'MDI'  THEN '8000'
                  END AS c_code,
                  CASE s.registration_type
                    WHEN 'Free Registration'  THEN '215450'
                    ELSE CONCAT('219', SUBSTR(bcode, 1, 3))
                  END AS sap_code,
                  s.si_no,
                  s.ar_no,
                  s.registration_type,
                  f.acct_number AS account_key,
                  s.registration + s.tip AS regn_expense, -- NEED TO ADD misc expenses
                  CONCAT(s.bcode, '000') AS branch_code,
                  IFNULL(lp.reference, v.reference) AS reference_number,
                  cust.cust_code,
                  CONCAT(cust.last_name, ', ', cust.first_name) AS customer_name
                FROM
                  tbl_sap_upload_batch sub
	        JOIN
                  tbl_sap_upload_sales_batch USING (subid)
	        JOIN
                  tbl_sales s USING (sid)
	        INNER JOIN
	          tbl_customer cust ON s.customer = cust.cid
	        INNER JOIN
	          tbl_region r ON s.region = r.rid
	        INNER JOIN
	          tbl_fund f ON r.rid = f.region
	        INNER JOIN
	          tbl_company c ON s.company = c.cid
                LEFT JOIN
                  tbl_lto_payment lp ON s.lto_payment = lp.lpid
                LEFT JOIN
                  tbl_voucher v ON s.voucher = v.vid
                WHERE
                  subid = $subid
QRY;
                $batch = $this->db->query($sql2)->result_array();
                //$batch = $this->db->query($sql1)->row();
                //echo '<pre>'; var_dump($this->db->last_query()); echo '</pre>'; die();
                // echo '<pre>'; var_dump($batch); echo '</pre>'; die();

                //switch ($batch->region) {
                //  case 'NCR':
                //  case 'Region 6':

                //    $batch->sales = $this->db->query("
                //      SELECT
                //        *, s.amount as amount
                //      FROM
                //        tbl_sales s
                //      INNER JOIN
                //        tbl_customer ON customer = cid
                //      INNER JOIN
                //        tbl_lto_payment ON lto_payment = lpid
                //      WHERE
                //        batch = ".$subid
                //    )->result_object();
                //    break;

                //  default:
                //    $batch->sales = $this->db->query("
                //      SELECT
                //        *, s.amount as amount
                //      FROM
                //        tbl_sales s
                //      INNER JOIN
                //        tbl_customer ON customer = cid
                //      INNER JOIN
                //        tbl_voucher ON voucher = vid
                //      WHERE
                //        batch = ".$subid
                //    )->result_object();
                //    break;
                //}

                $misc_exp_qry = <<<SQL
                  SELECT
                    reference, FORMAT(amount / COUNT(*), 2) AS misc_expense_amount
                  FROM (
                    SELECT
                      s.sid, reference, SUM(m.amount) AS amount
                    FROM
                        tbl_sap_upload_sales_batch susb
                    LEFT JOIN tbl_sales s ON s.sid = susb.sid
                    LEFT JOIN tbl_voucher v ON v.vid = s.voucher
                    LEFT JOIN tbl_misc m ON m.ca_ref = v.vid
                    LEFT JOIN tbl_misc_expense_history mxh1 ON m.mid = mxh1.mid
                    LEFT JOIN tbl_status st ON mxh1.status = st.status_id AND status_type = 'MISC_EXP'
                    LEFT JOIN tbl_misc_expense_history mxh2 ON mxh2.mid = mxh1.mid AND mxh1.id < mxh2.id
                    WHERE
                      mxh2.id IS NULL AND susb.subid = {$subid} AND s.payment_method = 'CASH' AND st.status_id = 3
                    GROUP BY s.sid
                  ) AS first_result
                  GROUP BY amount , reference
SQL;
                $get_misc_expense = $this->db->query($misc_exp_qry)->result_array();
                $misc_expenses = array();
                foreach ($get_misc_expense as $misc_expense) {
                  $misc_expenses[$misc_expense['reference']] = $misc_expense['misc_expense_amount'];
                }

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('donwnloaded sap template subid: '.$subid.'.');

                $result = array('batch' => $batch, 'misc_expenses' => $misc_expenses);

		return $result;
	}

	public function liquidate_batch($batch)
	{
		$this->db->update('tbl_sap_upload_batch', $batch, array('subid' => $batch->subid));
		$batch = $this->db->query("select * from tbl_sap_upload_batch where subid = ".$batch->subid)->row();

                $date = date('Y-m-d');
		// update sales status
                $update_qry = <<<SQL
                  UPDATE
                    tbl_sales s
                  INNER JOIN
                    tbl_sap_upload_sales_batch susb  ON s.sid = susb.sid
                  SET
                    status = 5, close_date = "{$date}"
		  WHERE susb.subid = $batch->subid
SQL;
		$this->db->query($update_qry);

		// update topsheet status

		// $count = $this->db->query("select count(*) as count from tbl_sales
		// 	where status < 5 and topsheet = ".$batch->topsheet)->row()->count;
		// if ($count == 0) {
		// 	$this->db->query("update tbl_topsheet set status = 3 where tid = ".$batch->topsheet);
		// }

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('saved document number ['.$batch->doc_no.'] for ['.$batch->trans_no.']');

		return $batch;
	}

        public function liquidate_misc_exp($misc_exp)
        {
          $insert_misc_exp = array();
          $uid = $_SESSION['uid'];
          $misc_exp_ids = explode(',', $misc_exp);

          foreach ($misc_exp_ids as $misc_exp_id) {
            $insert_misc_exp[] = array(
              'mid' => $misc_exp_id,
              'status' => 4,
              'uid' => $uid
            );
          }
          $this->db->insert_batch('tbl_misc_expense_history', $insert_misc_exp);
        }

}
