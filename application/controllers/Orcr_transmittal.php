<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_transmittal extends MY_Controller {
	
	public function __construct() { 
		parent::__construct();
		$this->load->helper('url');
    $this->load->model('Login_model', 'login');
    $this->load->model('Sales_model', 'sales');
    $this->load->model('Topsheet_model', 'topsheet');
    $this->load->model('Transmittal_model', 'transmittal');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'ORCR Transmittal');
		$this->header_data('nav', 'orcr_transmittal');
		$this->header_data('dir', './');
		$this->footer_data('script', '
			<script>
			$(function(){
				$("a.trans_no").click(function(){
					$("input[name=trans_no]").val($(this).text());
					$("input[name=search]").click();
				});
				$("input[type=radio]").change(function(){
					var sid = $(this).attr("name").split("[")[1].slice(0,-1);

					if ($(this).val() == 0)
					{
						$("textarea.sid-"+sid).removeClass("hide");
						
					}
					else
					{
						$("textarea.sid-"+sid).addClass("hide").val("");
						
					}
				});
				$(":checked").change();
			});
			</script>');

		$data = array();

		// on search
		$trans_no = $this->input->post('trans_no');
		if (!empty($trans_no))
		{
			$transmittal = $this->topsheet->get_transmittal_row($trans_no);
			$transmittal->sales = $this->sales->get_sales_by_transmittal($transmittal->tid);
		}

		// on save
		$submit = $this->input->post('submit');
		if (!empty($submit))
		{
			$receive = $this->input->post('receive');
			$remarks = $this->input->post('remarks');

			foreach ($transmittal->sales as $sales)
			{
				if (isset($receive[$sales->sid]) && $receive[$sales->sid] == 0)
				{
					// require remarks if hold
		    		$this->form_validation->set_rules('remarks['.$sales->sid.']', 'Remarks for ORCR # '.$sales->cr_no, 'required');
		    		$valid = 1;
				}
			}

			if ($this->form_validation->run() == TRUE || !isset($valid))
			{
				$received = 0;
				foreach ($transmittal->sales as $sales)
				{
					if (!isset($receive[$sales->sid]))
					{
						$received++;
					}
					else if ($receive[$sales->sid] == 0)
					{
	    			$obj = new Stdclass();
	    			$obj->transmittal = $transmittal->tid;
	    			$obj->sales = $sales->sid;
	    			$obj->remarks = $remarks[$sales->sid];
	    			$this->transmittal->save_remarks($obj);

	    			$sale = $this->sales->get_sales_row($sales->sid);
	    			$this->login->saveLog('marked customer '.$sale->first_name.' '.$sale->last_name.' ['.$sale->cust_code.'] Engine # '.$sale->engine_no.' as not received with remarks: '.$remarks[$sales->sid]);
					}
					else
					{
						$this->receive($sales->sid);

						$received++;
					}
				}

				if ($received == count($transmittal->sales))
				{
					$this->topsheet->update_transmittal($transmittal->tid);
				}
			}
		}

		// show pending list if not viewing specific record
		if (!isset($transmittal))
		{
			$data['table'] = $this->topsheet->get_tbl_topsheet_transmittal();
		}
		else
		{
			foreach ($transmittal->sales as $key => $row)
			{
				$transmittal->sales[$key] = $this->sales->load2($row->sid);
			}
			$data['transmittal'] = $transmittal;
		}

		$this->template('orcr_transmittal/list', $data); 
	}

	public function receive($sid)
	{
		$obj = new Stdclass();
		$obj->status = 1;
		$obj->receive_date = date('Y-m-d H:i:s');
		$this->db->where('sales', $sid);
		$this->db->update('tbl_transmittal_sales', $obj);

		$sales = $this->sales->get_sales_row($sid);

		$this->login->saveLog('marked customer '.$sales->first_name.' '.$sales->last_name.' ['.$sales->cust_code.'] Engine # '.$sales->engine_no.' as received');

		if (!$sales->status)
		{
			$_SESSION['messages'][] = 'ORCR # '.$sales->cr_no.' for '.$sales->first_name.' '.$sales->last_name.' has been received.';
		}
		else
		{
			$_SESSION['messages'][] = 'OR for '.$sales->first_name.' '.$sales->last_name.' has been received.';
		}
	}

	public function report()
	{
		$this->access(1);
		$this->header_data('title', 'OR CR Transmittal');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../');
		$this->header_data('link', '
				<link href="../assets/DT_bootstrap.css" rel="stylesheet" media="screen">
	      <link href="../vendors/chosen.min.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="../vendors/datatables/js/jquery.dataTables.min.js"></script>
	        <script src="../assets/scripts.js"></script>
	        <script src="../assets/DT_bootstrap.js"></script>
	        <script>
			$(function(){
				$(".summary a").each(function(){
					if ($(this).text() == "0")
						$(this).closest("td").html("0");
				});

				$(".summary a").click(function(){
					var val = $(this).attr("class").split("-");
					$("select[name=branch]").select2("val", val[0]);
					$("select[name=status]").select2("val", val[1]);
					$("input[name=search]").click();
				});
			});
	        </script>');

		$sales = array();
		$branch = $this->input->post('branch');
		$status = $this->input->post('status');
		
		// enable branch filter
		$this->load->model("Cmc_model", "cmc");
		$data["branch"] = $this->cmc->get_branches_tbl("","","","","","",$_SESSION['region']);

		$branch = $this->input->post('branch');
		$status = $this->input->post('status');
		if (!empty($branch))
		{
			if ($status != 0)
			{
				$where_status = " and (case
					when ttid is null
						then '1'
					when (select count(*) from tbl_sales_status
						where sales = sid
						and status = 'ORCR Received') > 0
						then '3'
					when (select count(*) from tbl_orcr_remarks
						where sales = sid) > 0
						then '4'
					else '2' end) = $status";
			}
			else
			{
				$where_status = "";
			}

			$data["transmittal"] = $this->transmittal->get_transmittal_tbl_report($where_status);
				/*
					INNER JOIN (select *, case when company = 2 then 6 else company end as cid from tbl_topsheet) d ON d.cid = LEFT(c.branch,1) AND LEFT(d.rerfo_date,10)=LEFT(c.rerfo_date,10)
				*/
		}
		else
		{
			// get branches
			$global = $this->load->database('global', TRUE);
			$branches = array();
			$result = $global->get_where('tbl_branches', array('ph_region' => $_SESSION['region']))->result_object();

			foreach ($result as $branch)
			{
				$result = $this->transmittal->get_transmittal_status_report($branch->bid);
				/*$result = $this->db->query("select count(*) as count,
					case when (select count(*) from tbl_rerfo_transmittal
							where rerfo = rid) = 0
							then 'pending'
						when (select count(*) from tbl_sales_status
							where sales = sid
							and status = 'ORCR Received') > 0
							then 'received'
						when (select count(*) from tbl_orcr_remarks
							where sales = sid) > 0
							then 'unreceived'
						else 'transmitted'
					end as status
					from tbl_rerfo r
					inner join tbl_sales s
						on s.branch = r.branch
						and left(sales_type, 9) = r.type
						and transmittal_date is not null
						and left(registration_date, 10) = left(exp_date, 10)
					where r.branch = ".$branch->bid."
					and r.type = 'Brand New'
					group by 2")->result_object();*/
				/*
					inner join (select *, case when company = 2 then 6 else company end as cid from tbl_topsheet) t ON t.cid = LEFT(r.branch,1) AND LEFT(t.rerfo_date,10)=LEFT(r.rerfo_date,10)
				*/

				$branch->pending = 0;
				$branch->transmitted = 0;
				$branch->received = 0;
				$branch->unreceived = 0;
				foreach ($result as $row)
				{
					$status = $row->status;
					$branch->$status = $row->count;
				}
				$branches[$branch->bid] = $branch;
			}

			$data['branches'] = $branches;
		}

		$this->template('orcr_transmittal/report', $data);
	}

	public function view($ttid)
	{
		$this->access(1);
		$this->header_data('title', 'OR CR Transmittal');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../../');

		$this->load->model('Sales_model', 'sales');
		$transmittal = new Stdclass();

		$transmittal->sales = $this->sales->get_sales_by_transmittal($ttid);

		foreach ($transmittal->sales as $key => $row)
		{
			$transmittal->sales[$key] = $this->sales->load2($row->sid);
		}

		$remarks = $this->input->post('remarks');
		$reply = $this->input->post('reply');
		if (!empty($reply))
		{
			foreach ($reply as $sid => $val)
			{
				$this->form_validation->set_rules('remarks['.$sid.']', 'Remarks', 'required');

				if ($this->form_validation->run() == TRUE)
				{
	    			$obj = new Stdclass();
	    			$obj->sales = $sid;
	    			$obj->remarks = $remarks[$sid];
	    			$obj->remarks_user = $_SESSION['username'];
	    			$obj->remarks_name = $_SESSION['firstname'].' '.$_SESSION['lastname'];
	    			$this->db->insert('tbl_orcr_remarks', $obj);
				
						$transmittal->sales = $this->sales->get_sales_by_transmittal($ttid);

						foreach ($transmittal->sales as $key => $row)
						{
							$transmittal->sales[$key] = $this->sales->load2($row->sid);
						}
				}
			}
		}

		$data['transmittal'] = $transmittal;
		$this->template('orcr_transmittal/view', $data);
	}
}
