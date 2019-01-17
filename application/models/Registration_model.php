<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Registration_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function load_list($data)
	{
		$result = $this->db->query("select t.*,
				left(date, 10) as date,
				count(*) as sales
			from tbl_lto_transmittal t
			inner join tbl_sales on lto_transmittal = ltid
			where t.region = ".$data['region']."
			and status = 3
			group by ltid
			order by date desc")->result_object();
		return $result;
	}

	public function list_sales($data)
	{
		$this->load->model('Sales_model', 'sales');
		$sales_type = $this->sales->sales_type;

		$result = $this->db->query("select *
			from tbl_sales
			inner join tbl_customer on cid = customer
			inner join tbl_engine on eid = engine
			where region = ".$data['region']."
			and status = 3
			order by bcode")->result_object();
		foreach ($result as $key => $sales)
		{
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$sales->sales_type = $sales_type[$sales->sales_type];
			$result[$key] = $sales;
		}
		return $result;
	}

	public function load_sales($data)
	{
		$this->load->model('Sales_model', 'sales');
		$sales_type = $this->sales->sales_type;

		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$data['ltid'])->row();
		$transmittal->cash = $this->db->query("select cash_on_hand
			from tbl_fund
			where region = ".$transmittal->region)->row()->cash_on_hand;

		$transmittal->sales = $this->db->query("select *
			from tbl_sales
			inner join tbl_customer on cid = customer
			inner join tbl_engine on eid = engine
			where lto_transmittal = ".$data['ltid']."
			and sid in (".implode(',', array_keys($data['registration'])).")
			and status = 3
			order by bcode")->result_object();
		foreach ($transmittal->sales as $key => $sales)
		{
			$sales->date_sold = substr($sales->date_sold, 0, 10);
			$sales->sales_type = $sales_type[$sales->sales_type];
			$transmittal->sales[$key] = $sales;
		}
		
		return $transmittal;
	}

	public function register_sales($data)
	{
		$this->load->model('Sales_model', 'sales');
		$transmittal = $this->db->query("select * from tbl_lto_transmittal
			where ltid = ".$data['ltid'])->row();

		foreach ($data['registration'] as $sid => $registration)
		{
			$sales = new Stdclass();
			$sales->sid = $sid;
			$sales->registration = $data['registration'][$sid];
			$sales->tip = $data['tip'][$sid];
			$sales->cr_date = $data['cr_date'][$sid];
			$sales->cr_no = $data['cr_no'][$sid];
			$sales->mvf_no = $data['mvf_no'][$sid];
			$sales->plate_no = $data['plate_no'][$sid];
			$sales->status = 4;
			$sales->registration_date = date('Y-m-d H:i:s');
			$this->sales->save_registration($sales);
		}

		if ($data['expense'] != 0)
		{
			$fund = $this->db->query("select * from tbl_fund
				where region = ".$transmittal->region)->row();

			$new_fund = new Stdclass();
			$new_fund->cash_on_hand = $fund->cash_on_hand + $data['expense'];
			$this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

			$history = new Stdclass();
			$history->fund = $fund->fid;
			$history->in_amount = ($data['expense'] > 0) ? $data['expense'] : 0;
			$history->out_amount = ($data['expense'] < 0) ? $data['expense'] * 1 : 0;
			$history->new_fund = $fund->fund;
			$history->new_hand = $new_fund->cash_on_hand;
			$history->new_check = $fund->cash_on_check;
			$history->type = 6;
			$this->db->insert('tbl_fund_history', $history);
		}

		$_SESSION['messages'][] = "Transmittal # ".$transmittal->code." updated successfully.";
	}
}