<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends MY_Controller {
	
	public function __construct() { 
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('directory');
    		$this->load->model('Sales_model', 'sales');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Sales');
		$this->header_data('nav', 'sales');
		$this->header_data('dir', './');

		$param = new Stdclass();
		$param->status = $this->input->post('status');
		$param->name = $this->input->post('name');
		$param->engine_no = $this->input->post('engine_no');
		$param->branch = $this->input->post('branch');

		if (empty($param->branch) && !is_numeric($param->branch) && ($_SESSION['position'] == 73 || $_SESSION['position'] == 81)) {
			$param->branch = $_SESSION['branch'];
		}
		
		$data['branch_def'] = ($_SESSION['position'] == 73 || $_SESSION['position'] == 81) ? $_SESSION['branch'] : 0;
		$data['branches'] = $this->sales->dd_branches();
		$data['status'] = $this->sales->status;
		$data['table'] = $this->sales->customer_status_report($param);
		$this->template('sales/list', $data); 
	}

	public function view($sid = null)
	{
		$dir = (empty($sid)) ? './../' : './../../';
		$this->access(1);
		$this->header_data('title', 'Sales');
		$this->header_data('nav', 'sales');
		$this->header_data('dir', $dir);

		$view = $this->input->post('view');
		$sid = (empty($view)) ? $sid : current(array_keys($view));
		if (empty($sid)) redirect('sales');

		$data['sales'] = $this->sales->load_sales($sid);
		$this->template('sales/view', $data);
	}

	public function edit()
	{
		$sid = 0;

		$save = $this->input->post('save');
		if (!empty($save)) {
			$sid = current(array_keys($save));

			$cust_code = $this->input->post('cust_code');
			$customer = $this->db->query("select * from tbl_customer
				where cust_code = '".$cust_code."'")->row();
			if (empty($customer))
			{
				$customer = new Stdclass();
				$customer->first_name = $this->input->post('first_name');
				$customer->last_name = $this->input->post('last_name');
				$customer->cust_code = $this->input->post('cust_code');
				$customer->cust_type = (empty($customer->first_name) || empty($customer->last_name)) ? 1 : 0;
				$this->db->insert('tbl_customer', $customer);
				$customer->cid = $this->db->insert_id();
			}
			else {
				$customer->first_name = $this->input->post('first_name');
				$customer->last_name = $this->input->post('last_name');
				$this->db->update('tbl_customer', $customer, array('cid' => $customer->cid));
			}

			$engine_no = $this->input->post('engine_no');
			$engine = $this->db->query("select * from tbl_engine
				where engine_no = '".$engine_no."'")->row();
			if (empty($engine))
			{
				$engine = new Stdclass();
				$engine->engine_no = $this->input->post('engine_no');
				$engine->chassis_no = $this->input->post('chassis_no');
				$engine->mat_no = '';
				$this->db->insert('tbl_engine', $engine);
				$engine->eid = $this->db->insert_id();
			}
			else {
				$engine->chassis_no = $this->input->post('chassis_no');
				$this->db->update('tbl_engine', $engine, array('eid' => $engine->eid));
			}

			$sales = new Stdclass();
			$sales->customer = $customer->cid;
			$sales->engine = $engine->eid;
			$sales->sales_type = $this->input->post('sales_type');
			$sales->si_no = $this->input->post('si_no');
			$sales->ar_no = $this->input->post('ar_no');
			$sales->amount = $this->input->post('amount');
			$sales->registration_type = $this->input->post('registration_type');
			$this->db->update('tbl_sales', $sales, array('sid' => $sid));

			$_SESSION['messages'][] = 'Record saved successfully.';
		}

		$edit = $this->input->post('edit');
		$sid = (empty($edit)) ? $sid : current(array_keys($edit));
		if (empty($sid)) redirect('sales');

		$this->access(1);
		$this->header_data('title', 'Sales');
		$this->header_data('nav', 'sales');
		$this->header_data('dir', './../');

		$data['sales'] = $this->sales->load_sales($sid);
		$this->template('sales/edit', $data);
	}

	public function orcr()
	{
		$this->access(1);
		$this->header_data('title', 'OR CR Print');
		$this->header_data('nav', 'orcr');
		$this->header_data('dir', './../');

		$data = array();
		$engine_no = $this->input->post('engine_no');

		if (!empty($engine_no))
		{
			$sales = $this->sales->get_orcr_by_engine($engine_no);
			
			if (!empty($sales)) {
				$data['sales'] = $sales;
			}
			else {
				$_SESSION['warning'][] = 'Engine #'.$engine_no.' does not exist.';
			}
		}

		$this->template('sales/orcr_print', $data);
	}

	public function print_orcr()
	{
		$sid = $this->input->post('sid');
		if (empty($sid)) redirect('sales');
		$data['sales'] = $this->sales->print_orcr($sid);
		$this->load->view('sales/print_orcr', $data);
	}

	public function missing_branch()
	{
		$this->access(1);
		$this->header_data('title', 'Missing Branches Report');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './../');

		$data['table'] = $this->sales->load_missing_branch();
		$this->template('sales/missing_branch', $data);
	}
}


