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
              ,GROUP_CONCAT(DISTINCT mid separator ',') AS misc_expense_id
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
              tbl_voucher  v ON v.vid = s.voucher
            LEFT JOIN
            (SELECT
               m.mid, m.ca_ref
            FROM
              tbl_misc m
            LEFT JOIN
              tbl_misc_expense_history mxh1 ON mxh1.mid = m.mid
            LEFT JOIN
              tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
            LEFT JOIN
              tbl_status st ON mxh1.status = st.status_id AND st.status_type = 'MISC_EXP'
            WHERE
              mxh2.id IS NULL AND st.status_name = 'For Liquidation'
            ) AS miscellaneous_expense ON ca_ref = v.vid
            WHERE
              sub.is_uploaded = 0 $this->companyQry
            GROUP BY subid, region, company
            ORDER BY sub.date_created DESC
            LIMIT 1000
          ")->result_object();

          return $result;
	}

	public function sap_upload($subid)
	{
              $sql = <<<SQL
                SELECT
                  DISTINCT
                  DATE_FORMAT(s.date_sold, '%m/%d/%Y') AS post_date,
                  CASE c.company_code
                    WHEN 'MNC'  THEN '1000'
                    WHEN 'MTI'  THEN '6000'
                    WHEN 'HPTI' THEN '3000'
                    WHEN 'MDI'  THEN '8000'
                  END AS c_code,
                  CASE s.registration_type
                    WHEN 'Free Registration'  THEN '215450'
                    ELSE
                      CONCAT('219',
                      CASE
                        WHEN c.company_code IN('MNC','MDI')   THEN SUBSTR(bcode, 2, 4)
                        WHEN c.company_code IN('HPTI', 'MTI') THEN CONCAT(LEFT(bcode, 1),RIGHT(bcode,2))
                      END)
                  END AS sap_code,
                  s.si_no, s.ar_no, s.amount AS ar_amount,
                  s.registration_type, f.acct_number AS account_key,
                  s.registration + s.tip AS regn_expense, -- NEED TO ADD misc expenses
                  CONCAT(s.bcode, '000') AS branch_code,
                  IFNULL(lp.reference, v.reference) AS reference_number,
                  cust.cust_code, CONCAT(IFNULL(cust.last_name,''), ', ', IFNULL(cust.first_name,'')) AS customer_name
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
SQL;
                $batch = $this->db->query($sql)->result_array();

                $misc_exp_qry = <<<SQL
                  SELECT
                    reference,
                    FORMAT( (amount - MOD(amount, COUNT(*)) ) / COUNT(*), 2) AS misc_expense_amount,
                    FORMAT(MOD(amount, COUNT(*)), 2) AS remainder
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
                if (!empty($get_misc_expense)) {
                  foreach ($get_misc_expense as $misc_expense) {
                    $misc_expenses[$misc_expense['reference']] = $misc_expense['misc_expense_amount'];
                    $misc_expenses['remainder'] = $misc_expense['remainder'];
                  }
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
