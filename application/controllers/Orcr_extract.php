<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_extract extends MY_Controller {
	
  public function __construct() { 
     parent::__construct();
     $this->load->helper('url');
  }

	public function index() 
	{
		$this->access(1);
		$this->header_data('title', 'ORCR Extract');
		$this->header_data('nav', 'orcr_extract');
		$this->header_data('dir', './');

		$data['count'] = $this->db->query("select count(sid) as count from tbl_sales
  		left join tbl_orcr_extract on sales = sid
  		where status > 3 and oeid is null")->row()->count;

		//$data['count'] = $this->db->query("select count(sid) as count from tbl_sales where status > 3")->row()->count;
		
		$data['table'] = $this->db->query("select batch_no,
				max(extract_date) as extract_date, count(sales) as count
			from tbl_orcr_extract
			group by batch_no
			order by max(extract_date) desc limit 100")->result_object();

		$this->template('orcr_extract', $data);
	}

	public function csv($batch_no = 0) {
		$extract = $this->input->post('extract');
		if (empty($extract)) redirect('orcr_extract');

		$batch_no = $this->input->post('batch_no');
		if (empty($batch_no)) {
			// get next batch
			$batch_no = $this->db->query("select ifnull(max(batch_no), 0)+1 as batch_no from tbl_orcr_extract")->row()->batch_no;

			// extract to tbl
			$this->db->query("insert into tbl_orcr_extract
					select 0, ".$batch_no.", sid, CURRENT_TIMESTAMP,
					case when tbl_sales.plate_no is null then 0 else 1 end as plate_no
					from tbl_sales
					left join tbl_orcr_extract on sales = sid
					where status > 3 and oeid is null
					order by sid limit 5000");
		}

		$data['result'] = $this->db->query("select bcode, trim(concat(ifnull(first_name, ''), ' ', ifnull(last_name, ''))) as name, engine_no, cr_date, cr_no, tbl_sales.plate_no from tbl_sales
				inner join tbl_customer on cid = customer
				inner join tbl_engine on eid = engine
				inner join tbl_orcr_extract on sales = sid
				where batch_no = ".$batch_no."
				order by bcode")->result_array();

		$this->load->view('orcr_extract_csv', $data);
	}
}


