<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Transmittal_model extends CI_Model{

	public $status = array(
		0 => 'Unprocessed',
		1 => 'Incomplete',
		2 => 'Received'
	);

	public $sales_status = array(
		0 => 'Not Received',
		1 => 'Received',
	);

	public $type = array(
		0 => 'Brand New (Cash)',
		1 => 'Brand New (Installment)'
	);

	public function __construct()
	{
		parent::__construct();

		$this->load->model('Topsheet_model', 'topsheet');
	}

	public function get_transmittal_status_list()
	{
		$result = $this->db->query("
			select tr.*,ts.region,ts.company from tbl_transmittal tr
			left join tbl_topsheet ts on topsheet=ts.tid limit 1000")->result_object();
		foreach ($result as $key => $transmittal)
		{
			$transmittal->company = $this->topsheet->company[$transmittal->company];
			$transmittal->date = substr($transmittal->date, 0, 10);
		}
		return $result;
	}

	public function get_topsheet_for_transmittal()
	{
		$result = $this->db->query("select * from tbl_topsheet
			where region=".$_SESSION['region']." limit 1000")->result_object();
		foreach ($result as $key => $topsheet)
		{
			$topsheet->company = $this->topsheet->company[$topsheet->company];
			$topsheet->date = substr($topsheet->date, 0, 10);
		}
		return $result;
	}

	public function transmittal_topsheet($tid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$topsheet = $this->db->query("select * from tbl_topsheet
			where tid = ".$tid)->row();

		$result = $this->db->query("select group_concat(sid) as sids,
				branch, sales_type
			from tbl_sales
			where topsheet = ".$tid."
			group by branch, sales_type")->result_object();
		foreach ($result as $row)
		{
			$row->tid = $tid;
			$row->branch = $this->cmc->get_branch($row->branch);

			switch ($row->sales_type)
			{
				case 0: // cash
					$row->type = 0;
					$row->trans_no = $row->branch->b_code.'0'.$row->type
						.substr($topsheet->trans_no, -6);
					$this->save_transmittal($row);
					break;
				case 1: // installment
					$row->type = 1;
					$row->trans_no = $row->branch->b_code.'0'.$row->type
						.substr($topsheet->trans_no, -6);
					$this->save_transmittal($row);

					// bmi
					$row->type = 2;
					$row->trans_no = $row->branch->b_code.'0'.$row->type
						.substr($topsheet->trans_no, -6);
					$this->save_transmittal($row);
					break;
			}
		}

		$this->db->query('update tbl_topsheet set transmittal = 1 where tid = '.$tid);
	}

	public function save_transmittal($param)
	{
		$transmittal = $this->db->query("select * from tbl_transmittal
			where trans_no = '".$param->trans_no."'")->row();
		if (empty($transmittal))
		{
			$transmittal = new Stdclass();
			$transmittal->user = $_SESSION['uid'];
			$transmittal->topsheet = $param->tid;
			$transmittal->trans_no = $param->trans_no;
			$transmittal->branch = $param->branch->bid;
			$transmittal->type = $param->type;
			$transmittal->date = date('Y-m-d H:i:s');
			$this->db->insert('tbl_transmittal', $transmittal);
			$transmittal->tid = $this->db->insert_id();
		}

		foreach (explode(',', $param->sids) as $sid)
		{
			$trans_sales = new Stdclass();
			$trans_sales->sales = $sid;
			$trans_sales->transmittal = $transmittal->tid;
			$this->db->insert('tbl_transmittal_sales', $trans_sales);
		}
	}

	public function print_topsheet_transmittal($tid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$result = $this->db->query("select * from tbl_transmittal
			where topsheet = ".$tid)->result_object();

		foreach ($result as $key => $transmittal)
		{
			$transmittal->user = $this->cmc->get_user_info($transmittal->user);
			$transmittal->branch = $this->cmc->get_branch($transmittal->branch);
			$transmittal->region = $this->topsheet->region[$transmittal->branch->ph_region];
			$transmittal->date = substr($transmittal->date, 0, 10);

			$transmittal->sales = $this->db->query("select * from tbl_sales
				inner join tbl_customer on customer = cid
				inner join tbl_transmittal_sales on sales = sid
				where transmittal = ".$transmittal->tid)->result_object();

			$result[$key] = $transmittal;
		}

		return $result;
	}

	public function get_branch_transmittal($branch)
	{
		$result = $this->db->query("select * from tbl_transmittal
			where type < 2 and status < 2 and branch = ".$branch)->result_object();
		foreach ($result as $key => $row)
		{
			$row->user = $this->cmc->get_user_info($row->user);
			$row->date = substr($row->date, 0, 10);
			$row->type = $this->type[$row->type];
			$row->status = $this->status[$row->status];
			$result[$key] = $row;
		}
		return $result;
	}

	public function get_bmi_transmittal()
	{
		$this->load->model('Cmc_model', 'cmc');
		$result = $this->db->query("select * from tbl_transmittal
			where type = 2 and status < 2")->result_object();
		foreach ($result as $key => $row)
		{
			$row->branch = $this->cmc->get_branch($row->branch);
			$row->user = $this->cmc->get_user_info($row->user);
			$row->date = substr($row->date, 0, 10);
			$row->status = $this->status[$row->status];
			$result[$key] = $row;
		}
		return $result;
	}

	public function load_transmittal($tid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$transmittal = $this->db->query("select * from tbl_transmittal
			where tid = ".$tid)->row();
		$transmittal->branch = $this->cmc->get_branch($transmittal->branch);

		$transmittal->sales = $this->db->query("select * from tbl_sales
			inner join tbl_customer on customer = cid
			inner join tbl_transmittal_sales on sales = sid
			where transmittal = ".$tid)->result_object();
		foreach ($transmittal->sales as $key => $sales)
		{
			$last_remarks = $this->db->query("select * from tbl_transmittal_remarks
				where sales = ".$sales->sid."
				order by trid desc limit 1")->row();
			$sales->last_user = (!empty($last_remarks))
				? $this->cmc->get_user_info($last_remarks->user)
				: null;

			$sales->status = $this->sales_status[$sales->status];
			$transmittal->sales[$key] = $sales;
		}

		return $transmittal;
	}

	public function load_remarks($tid, $sid)
	{
		$this->load->model('Cmc_model', 'cmc');
		$result = $this->db->query("select * from tbl_transmittal_remarks
			where transmittal = ".$tid." and sales = ".$sid)->result_object();
		foreach ($result as $key => $row)
		{
			$row->user = $this->cmc->get_user_info($row->user);
			$row->date = substr($row->date, 0, 10);
			$result[$key] = $row;
		}
		return $result;
	}

	public function save_remarks($remarks)
	{
		$remarks->user = $_SESSION['uid'];
		$this->db->insert('tbl_transmittal_remarks', $remarks);

		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			where sid = ".$remarks->sales)->row();

		$this->login->saveLog('saved remarks for customer '.$sales->first_name.' '.$sales->last_name.' [Engine # '.$sales->engine_no.']: '.$remarks->remarks);
	}

	public function received($sid)
	{
		$sales = $this->db->query("select * from tbl_sales
			inner join tbl_engine on engine = eid
			inner join tbl_customer on customer = cid
			inner join tbl_transmittal_sales on sales = sid
			where sid = ".$sid)->row();

		$this->db->query("update tbl_transmittal_sales
			set status = 1, receive_date = '".date('Y-m-d H:i:s')."'
			where tsid = ".$sales->tsid);

		$count = $this->db->query("select count(*) as count from tbl_transmittal_sales
			where status = 0 and transmittal = ".$sales->transmittal)->row()->count;
		if ($count == 0)
		{
			$this->db->query("update tbl_transmittal
				set status = 2, receive_date = '".date('Y-m-d H:i:s')."'
				where tid = ".$sales->transmittal);
		}
		else
		{
			$this->db->query("update tbl_transmittal set status = 1
				where tid = ".$sales->transmittal);
		}

		$this->load->model('Login_model', 'login');
		$this->login->saveLog('received document for customer '.$sales->first_name.' '.$sales->last_name.' [Engine # '.$sales->engine_no.'].');

		return $sales;
	}

	public function load2($tmid)
	{
		$transmittal = $this->db->get_where('tbl_transmittal', array('tmid' => $tmid))->row();

		if (substr($transmittal->trans_no, 0, 3) == 'TRS')
		{
			// if repo transmittal, get sales records
			$transmittal->rows = $this->db->get_where('tbl_sales_transmittal', array('transmittal' => $transmittal->tmid))->result_object();
		}
		else
		{
			// if transfer transmittal, get transfer records
			$transmittal->rows = $this->db->get_where('tbl_transfer', array('transmittal' => $transmittal->tmid))->result_object();
		}

		// load full sales objects
		foreach ($transmittal->rows as $key => $row)
		{
			$transmittal->rows[$key]->sales = $this->db->get_where('tbl_sales', array('sid' => $transmittal->rows[$key]->sales))->row();
			$transmittal->rows[$key]->sales->engine = $this->db->get_where('tbl_engine', array('eid' => $transmittal->rows[$key]->sales->engine))->row();
			$transmittal->rows[$key]->sales->customer = $this->db->get_where('tbl_customer', array('cid' => $transmittal->rows[$key]->sales->customer))->row();
		}
		
		return $transmittal;
	}

	public function load_row($tmid)
	{
		return $this->db->get_where('tbl_transmittal', array('tmid' => $tmid))->row();
	}

	public function save2(&$transmittal)
	{
		if ($transmittal->tmid)
		{
			foreach ($transmittal->rows as $row)
			{
				// return foreign keys before saving
				$sales = $row->sales;
				$sales->engine = $sales->engine->eid;
				$sales->customer = $sales->customer->cid;
				$this->db->update("tbl_sales", $sales, array("sid" => $sales->sid));

				$row->sales = $sales->sid;

				if (isset($row->stid))
				{
					// if repo transmittal
					$this->db->update("tbl_sales_transmittal", $row, array("stid" => $row->stid));
				}

				if (isset($row->tfid))
				{
					// if transfer transmittal
					$this->db->update("tbl_transfer", $row, array("tfid" => $row->tfid));
				}
			}

			// remove transmittal rows before saving
			unset($transmittal->rows);
			$this->db->update("tbl_transmittal", $transmittal, array("tmid" => $transmittal->tmid));
		}
		else
		{
			// get branch code
			$global = $this->load->database('global', TRUE);
			$bcode = $global->query("select b_code from tbl_branches 
				where bid = ".$this->branch)->row()->b_code;

			// generate transmittal number based on branch code and current date
			$trans_no = $$transmittal->trans_no.'-'.$bcode.date('ymd');

			// auto increment
			$count = $this->db->query("select count(*)+1 as c from tbl_transmittal 
				where trans_no like '".$trans_no."%'")->row()->c;
			$$transmittal->trans_no = $trans_no.$count;

			$this->db->insert("tbl_transmittal", $$transmittal);

			$transmittal->tmid = $this->db->insert_id();
		}

		// reload transmittal
		$transmittal = $this->load2($transmittal->tmid);
	}

	public function save()
	{
		if ($this->tmid)
		{
			$this->db->update("tbl_transmittal", $this, array("tmid" => $this->tmid));
		}
		else
		{
			// GENERATE TRANSMITTAL 
			$global = $this->load->database('global', TRUE);
			$bcode = $global->query("select b_code from tbl_branches 
				where bid = ".$this->branch)->row()->b_code;
			$trans_no = $this->trans_no.'-'.$bcode.date('ymd');
			$count = $this->db->query("select count(*)+1 as c from tbl_transmittal 
				where trans_no like '".$trans_no."%'")->row()->c;
			$this->trans_no = $trans_no.$count;

			$this->db->insert("tbl_transmittal", $this);

			$this->tmid = $this->db->insert_id();
		}
	}

	public function get_id($param)
	{
		$id = array();

		$result = $this->db->get_where('tbl_transmittal', $param);
		foreach ($result->result_object() as $row)
		{
			$id[] = $row->tmid;
		}

		return $id;
	}

	public function get_id2($param)
	{
		$result = $this->db->get_where('tbl_transmittal', $param);
		return $result->row()->tmid;
	}

	public function get_transmittal_tbl_report($where_status)
	{
		return $this->db->query("SELECT s.sid, s.branch, CONCAT(first_name,' ',last_name) AS customer_name, s.cr_no, ttid, tt.track_no,
				case when ttid is null
						then 'For Transmittal'
					when (select count(*) from tbl_sales_status
						where sales = sid
						and status = 'ORCR Received') > 0
						then 'Received'
					when (select count(*) from tbl_orcr_remarks
						where sales = sid) > 0 and (select count(*) from tbl_sales_status
						where sales = sid
						and status = 'ORCR Received') = 0
						then 'Not Received'
					else 'Transmitted'
				end as status
				from tbl_topsheet_sales ts
				inner join tbl_sales s on sales = sid
				inner join tbl_topsheet_transmittal  tt on transmittal=ttid
				inner join tbl_customer on customer=cid
				WHERE s.branch=$branch ".$where_status)->result_object();
	}

	public function get_transmittal_status_report($bid)
	{
		return $this->db->query("
									select count(*) as count,
										case
											when (select count(*) from tbl_topsheet_transmittal
															where transmittal = ttid) =0
											then 'pending'

											when (select count(*) from tbl_sales 
															where sales = sid
															and status = 'ORCR Received') > 0
											then 'received'

											when (select count(*) from tbl_orcr_remarks
															where sales = sid) > 0
											then 'unreceived'
											else 'transmitted'

										end as status

									from tbl_topsheet_sales ts
									inner join tbl_sales s on sales = sid
									inner join tbl_topsheet_transmittal  tt on transmittal=ttid
									where tt.branch=".$bid."
									and left(s.sales_type,9) = 'Brand New'
									group by 2")->result_object();
	}

	public function get_trans_no_by_array($trans_no)
	{
		return $this->db->get_where('tbl_transmittal', array('trans_no' => $trans_no))->row();
	}

	public function trans_no_exists($trans_no)
	{
		return $this->db->get_where('tbl_transmittal', array('trans_no' => $trans_no))->num_rows();
	}
}