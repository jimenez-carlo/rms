<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Batch_model extends CI_Model{

	public $region = array(
		1 => 'NCR',
		2 => 'Region 1',
		3 => 'Region 2',
		4 => 'Region 3',
		5 => 'Region 4A',
		6 => 'Region 4B',
		7 => 'Region 5',
		8 => 'Region 6',
		9 => 'Region 7',
		10 => 'Region 8',
	);

	public $company = array(
		1 => 'MNC',
		2 => 'MTI',
		3 => 'HPTI',
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function list_for_upload()
	{
		$result = $this->db->query("select bid, b.trans_no, b.post_date,
				region, company, download_date
			from tbl_batch b
			inner join tbl_topsheet on topsheet = tid
			where b.status = 0
			order by b.post_date desc
			limit 1000")->result_object();

		foreach ($result as $key => $batch)
		{
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
			where region = ".$batch->region."
			and company = ".$batch->company)->row()->acct_number;
		$batch->region = $this->region[$batch->region];
		$batch->company = $this->company[$batch->company];


		$batch->sales = $this->db->query("select * from tbl_sales
			inner join tbl_customer on customer = cid
			where batch = ".$bid)->result_object();
		foreach ($batch->sales as $key => $sales)
		{
			$sales->branch = $this->cmc->get_branch($sales->branch);
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