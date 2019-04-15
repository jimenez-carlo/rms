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
            select bid, b.trans_no, b.post_date,
                   region, company, download_date
            from tbl_batch b
            inner join tbl_topsheet on topsheet = tid
            where b.status = 0 $this->companyQry
            order by b.post_date desc
            limit 1000
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

		$batch = $this->db->query("select * from tbl_batch
			inner join tbl_topsheet on topsheet = tid
			where bid = ".$bid)->row();
		$batch->bcode = ($batch->company == 2) ? 6 : $batch->company;
		$batch->post_date = substr($batch->post_date, 0, 10);

		$batch->account_key = $this->db->query("select acct_number from tbl_fund
			where region = ".$batch->region)->row()->acct_number;
		$batch->region = $this->region[$batch->region];
		$batch->company = $this->company[$batch->company];


		if($batch->region == 'NCR'){
		$batch->sales = $this->db->query("select *, s.amount as amount
			from tbl_sales s
			inner join tbl_customer on customer = cid
			inner join tbl_lto_payment on lto_payment = lpid
			where batch = ".$bid)->result_object();


		}else{
		$batch->sales = $this->db->query("select *, s.amount as amount
			from tbl_sales s
			inner join tbl_customer on customer = cid
			inner join tbl_voucher on voucher = vid
			where batch = ".$bid)->result_object();

		}


		foreach ($batch->sales as $key => $sales)
		{
			$batch->sales[$key] = $sales;
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
