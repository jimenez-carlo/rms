<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Batch_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();

                // Check the value of $this->region and $this->company in application/core/MY_Controller
                if ($_SESSION['company'] == 8) {
                  $this->region     = $this->mdi_region;
                  $this->company    = $this->mdi;
                  $this->companyQry = ' AND company = 8';
                } else {
                  $this->companyQry = ' AND company != 8';
                }
	}

	public function list_for_upload() {
          $result = $this->db->query("
            SELECT
              b.bid, b.trans_no, b.post_date,
              ts.region, ts.company, b.download_date
            FROM
              tbl_batch b
            INNER JOIN
              tbl_topsheet ts ON b.topsheet = ts.tid
            WHERE b.status = 0 $this->companyQry
            ORDER BY b.post_date DESC
            LIMIT 1000
          ")->result_object();

          foreach ($result as $key => $batch) {
                  $batch->region = $this->region[$batch->region];
                  $batch->company = $this->company[$batch->company];
                  $batch->post_date = substr($batch->post_date, 0, 10);
                  $result[$key] = $batch;
          }

          return $result;
	}

	public function sap_upload($bid)
	{
		$this->load->model('Cmc_model', 'cmc');

                $batch = $this->db->query("
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
                    bid = $bid
                ")->row();

                switch ($batch->region) {
                  case 'NCR':
                  case 'Region 6':

                    $batch->sales = $this->db->query("
                      SELECT
                        *, s.amount as amount
                      FROM
                        tbl_sales s
                      INNER JOIN
                        tbl_customer ON customer = cid
                      INNER JOIN
                        tbl_lto_payment ON lto_payment = lpid
                      WHERE
                        batch = ".$bid
                    )->result_object();
                    break;

                  default:
                    $batch->sales = $this->db->query("
                      SELECT
                        *, s.amount as amount
                      FROM
                        tbl_sales s
                      INNER JOIN
                        tbl_customer ON customer = cid
                      INNER JOIN
                        tbl_voucher ON voucher = vid
                      WHERE
                        batch = ".$bid
                    )->result_object();
                    break;
                }

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('donwnloaded sap template ['.$batch->trans_no.']');

		return $batch;
	}

	public function liquidate_batch($batch)
	{
		$batch->status = 1;
		$this->db->update('tbl_batch', $batch, array('bid' => $batch->bid));
		$batch = $this->db->query("select * from tbl_batch where bid = ".$batch->bid)->row();

		// update sales status
		$this->db->query("update tbl_sales
			set status = 5, close_date = '".date('Y-m-d')."'
			where batch = ".$batch->bid);

		// update misc status
		$this->db->query("update tbl_misc set status = 4 where batch = ".$batch->bid);

		// update topsheet status
		$count = $this->db->query("select count(*) as count from tbl_sales
			where status < 5 and topsheet = ".$batch->topsheet)->row()->count;
		if ($count == 0) {
			$this->db->query("update tbl_topsheet set status = 3 where tid = ".$batch->topsheet);
		}

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('saved document number ['.$batch->doc_no.'] for ['.$batch->trans_no.']');

		return $batch;
	}
}
